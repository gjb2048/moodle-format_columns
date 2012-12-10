<?php
/**
 * Columns Information
 *
 * A topic based format that allows the topics to be arranged in columns except 0.
 * Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    course/format
 * @subpackage columns
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Dan Poltawski.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once('../../../../config.php');
require_once($CFG->dirroot . '/course/format/columns/lib.php');
require_once($CFG->dirroot . '/course/format/columns/forms/settings_form.php');
require_once($CFG->dirroot . '/course/format/columns/cnconfig.php'); // For Columns defaults.

defined('MOODLE_INTERNAL') || die();

$courseid = required_param('id', PARAM_INT); // course id

if (!($course = $DB->get_record('course', array('id' => $courseid)))) {
    print_error('invalidcourseid', 'error');
} // From /course/view.php

$columnssetting = get_columns_setting($course->id);

preload_course_contexts($courseid); // From /course/view.php
if (!$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id)) {
    print_error('nocontext');
}
require_login($course); // From /course/view.php - Facilitates the correct population of the setttings block.

$PAGE->set_context($coursecontext);
$PAGE->set_url('/course/format/columns/forms/settings.php', array('id' => $courseid, 'sesskey' => sesskey())); // From /course/view.php
$PAGE->set_pagelayout('course'); // From /course/view.php
$PAGE->set_pagetype('course-view-columns'); // From /course/view.php
$PAGE->set_other_editing_capability('moodle/course:manageactivities'); // From /course/view.php
$PAGE->set_title(get_string('settings') . ' - ' . $course->fullname . ' ' . get_string('course'));
$PAGE->set_heading(get_string('formatsettings', 'format_columns') . ' - ' . $course->fullname . ' ' . get_string('course'));

require_sesskey();
require_capability('moodle/course:update', $coursecontext);

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

if ($PAGE->user_is_editing()) {
    $mform = new set_settings_form(null, array('courseid' => $courseid, 'setcolumns' => $columnssetting->columns));

    //print_object($mform);
    if ($mform->is_cancelled()) {
        redirect($courseurl);
    } else if ($formdata = $mform->get_data()) {
        //print_r($formdata);
        if (isset($formdata->resetcolumns) == true){
            put_columns_setting($formdata->id, $CNCFG->defaultcolumns);
        } else {
            put_columns_setting($formdata->id, $formdata->setcolumnsnew);
        }
        if (isset($formdata->resetallcolumns) == true) {
            reset_columns_setting();
        }
        redirect($courseurl);
    }

    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
} else {
    redirect($courseurl);
}