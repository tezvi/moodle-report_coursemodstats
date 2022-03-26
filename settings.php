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
 * Contains settings used by coursemodstats report.
 *
 * @package    report_coursemodstats
 * @copyright  2022 Andrej Vitez <contact@andrejvitez.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Just a link to course report.
$ADMIN->add(
        'reports',
        new admin_externalpage(
                'report_coursemodstats', get_string('admin_menu_item', 'report_coursemodstats'),
                $CFG->wwwroot . "/report/coursemodstats/index.php", 'report/coursemodstats:view'
        )
);

// No report settings.
$settings = null;
