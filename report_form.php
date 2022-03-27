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
 * Report form definition.
 *
 * @package    report_coursemodstats
 * @copyright  2022 Andrej Vitez <contact@andrejvitez.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * Class report_form
 */
class report_form extends moodleform {
    /**
     * Creates and configures report form definition.
     *
     * @return void
     */
    public function definition() {
        global $DB;

        $this->_form->addElement(
                'static',
                'info_guide',
                '',
                html_writer::tag(
                        'div',
                        get_string('form_intro', 'report_coursemodstats'),
                        ['class' => 'alert alert-info']
                )
        );

        $this->_form->addElement('static', 'filter_header', get_string('filter_section', 'report_coursemodstats'));
        $this->_form->addElement(
                'checkbox',
                'onlyvisible_modules',
                get_string('select_only_visible_modules', 'report_coursemodstats')
        );
        $this->_form->addElement(
                'checkbox',
                'onlyvisible_courses',
                get_string('select_only_visible_courses', 'report_coursemodstats')
        );

        $this->_form->addElement('checkbox', 'allcourses', get_string('select_all_courses', 'report_coursemodstats'));
        $this->_form->setDefault('allcourses', true);

        $categories = $DB->get_records_menu('course', [], 'fullname ASC', 'id,fullname');
        $this->_form->addElement(
                'select',
                'courses',
                get_string('report_form_select_courses', 'report_coursemodstats'),
                $categories,
                ['multiple' => 'multiple', 'size' => '25']
        );

        $this->_form->disabledIf('courses', 'allcourses', 'checked');

        $this->add_action_buttons(true, get_string('report_form_submit', 'report_coursemodstats'));
    }
}
