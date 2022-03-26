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
 * Main public index file for coursemodstats report.
 *
 * @package    report_coursemodstats
 * @copyright  2022 Andrej Vitez <contact@andrejvitez.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/report/log/locallib.php');
require_once(__DIR__ . "/report_form.php");
require_once(__DIR__ . "/locallib.php");

global $CFG, $PAGE;

require_login();
$context = context_system::instance();
$PAGE->set_context($context);

require_capability('report/coursemodstats:view', $context);

$title = get_string('title', 'report_coursemodstats');
$heading = get_string('heading', 'report_coursemodstats');

$url = new moodle_url('/report/log/index.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('incourse');

$PAGE->set_title($title);
$PAGE->set_heading($heading);
$PAGE->navbar->add($title);

$mform = new report_form();
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/'));
} else if ($data = $mform->get_data()) {
    \core_form\util::form_download_complete();
    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=report_course_stats.xlsx");
    header("Pragma: no-cache");
    header("Expires: 0");
    $courselist = empty($data->allcourses) ? $data->courses : null;
    $visiblecourses = !empty($data->onlyvisible_courses);
    $visiblemodules = !empty($data->onlyvisible_modules);
    echo report_coursemodstats_export_excel($courselist, $visiblecourses, $visiblemodules);
    exit;
}

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

$mform->display();

echo $OUTPUT->footer();
