<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Local library with business logic.
 *
 * @package    report_coursemodstats
 * @category   report
 * @copyright  2022 Andrej Vitez <contact@andrejvitez.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('MOODLE_INTERNAL') || die;
global $CFG;

require_once($CFG->dirroot . '/lib/phpspreadsheet/vendor/autoload.php');

function report_coursemodstats_export_excel($courselist, $visiblecourses, $visiblemodules)
{
    global $DB;

    $categories  = report_coursemodstats_get_categorytree();
    $modulecache = [];

    // Prepare SQL WHERE clauses for user defined filters.
    $sqlwherestatements = [];
    if ($visiblemodules) {
        $sqlwherestatements[] = 'm.visible = 1 AND cm.visible = 1';
    }
    if ($courselist) {
        $coursids = [];
        foreach ($courselist as $coursid) {
            $coursids[] = (int) $coursid;
        }
        $sqlwherestatements[] = sprintf('cm.course IN(%s)', implode(',', $coursids));
    }
    $sqlmodulewhere    = $sqlwherestatements ? 'where ' . implode(' AND ', $sqlwherestatements) : '';
    $sqlvisiblecourses = $visiblecourses ? 'where c.visible = 1' : '';

    $sql = "select c.id as courseid, TRIM(c.fullname) as fullname, c.category, m.name as modulename, a.modulecount
        from (
                 select cm.course, m.id as module, count(cm.id) as modulecount
                 from {modules} m
                 join {course_modules} cm ON cm.module = m.id
                 $sqlmodulewhere
                 group by cm.course, m.id
             ) a
        join {course} c ON c.id = a.course
        join {modules} m ON m.id = a.module
        $sqlvisiblecourses
        order by fullname, modulecount desc";

    // We are using approximate measuring method because true type fonts might be missing from Moodle installation.
    Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_APPROX);
    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $writer      = new Xlsx($spreadsheet);

    $columns         = [
        'fullname'     => 'Course name',
        'courseid'     => 'Course ID',
        'rootcategory' => 'Root category',
        'category'     => 'Category',
        'modulename'   => 'Module type',
        'modulecount'  => 'Instances',
    ];
    $columnaccessors = array_keys($columns);

    // Render excel heading columns.
    foreach (array_values($columns) as $idx => $column) {
        $sheet->setCellValueByColumnAndRow($idx + 1, 1, $column);
        $sheet->getColumnDimensionByColumn($idx + 1)->setAutoSize(true);
    }
    $lastcolumn  = Coordinate::stringFromColumnIndex(count($columns));
    $headerrange = "A1:{$lastcolumn}1";
    $sheet->freezePane($lastcolumn . '2');
    $sheet->setAutoFilter($headerrange);

    $style = $sheet->getStyle($headerrange);
    $style->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('dee6ef');
    $style->getBorders()
        ->getBottom()
        ->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new Color(Color::COLOR_BLACK));

    // Render excel rows with course data.
    $rowidx      = 2;
    $records     = $DB->get_recordset_sql($sql);
    $stripecolor = 'dee7e5';
    $usestripes  = true;
    $lastcourse  = null;
    // Prevent stripe styling if more than 50 courses selected to boost XLSx performance.
    $stripeenabled = true;

    foreach ($records as $coursemodstats) {
        $rootcategory = report_coursemodstats_find_root_category($coursemodstats->category, $categories);
        $rowdata      = array_merge((array) $coursemodstats, [
            'rootcategory' => $rootcategory ? $rootcategory->name : '',
            'modulename'   => get_module_title($coursemodstats->modulename, $modulecache),
            'category'     => isset($categories[$coursemodstats->category]) ? $categories[$coursemodstats->category]->name : '',
        ]);

        foreach ($columnaccessors as $columnidx => $column) {
            $sheet->setCellValueByColumnAndRow($columnidx + 1, $rowidx, $rowdata[$column]);
        }

        if (!$lastcourse || $lastcourse !== $coursemodstats->courseid) {
            $lastcourse = $coursemodstats->courseid;
            $usestripes = !$usestripes;
        }

        if ($usestripes && $stripeenabled) {
            $style = $sheet->getStyle(sprintf('A%1$d:%2$s%1$d', $rowidx, $lastcolumn));
            $style->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB($stripecolor);
            $style->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_HAIR)
                ->setColor(new Color('cccccc'));
        }

        $rowidx++;
    }

    ob_start();
    $writer->save('php://output');
    $exceloutput = ob_get_clean();

    return $exceloutput;
}

/**
 * @param object $course
 * @param array  $categories
 *
 * @return mixed|null
 */
function report_coursemodstats_find_root_category($category, $categories)
{
    $match = $categories[$category] ?? null;
    while ($match->parent) {
        $match = $categories[$match->parent];
    }

    return $match;
}

function report_coursemodstats_get_categorytree()
{
    global $DB;

    $categories = [
        0 => (object) ['id' => 0, 'name' => get_site()->fullname, 'parent' => 0]
    ];

    foreach ($DB->get_records('course_categories', [], 'sortorder asc') as $category) {
        $categories[$category->id] = $category;
    }

    return $categories;
}

function get_module_title($modulename, &$modulecache)
{
    if (isset($modulecache[$modulename])) {
        return $modulecache[$modulename];
    }

    $translated = get_string('modulename', $modulename);
    // If no translation is available use module identifier.
    if (empty($translated) || strpos($translated, '[[') === 0) {
        $translated = $modulename;
    }

    return $modulecache[$modulename] = $translated;
}
