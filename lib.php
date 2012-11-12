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
require_once($CFG->dirroot . '/course/format/columns/cnconfig.php'); // For Columns defaults.

/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_columns_uses_sections() {
    return true;
}

/**
 * Used to display the course structure for a course where format=Collapsed Topics
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = Collapsed Topics.
 *
 * @param navigation_node $navigation The course node.
 * @param array $path An array of keys to the course node.
 * @param stdClass $course The course we are loading the section for.
 */
function callback_columns_load_content(&$navigation, $course, $coursenode) {
    return $navigation->load_generic_course_sections($course, $coursenode, 'columns');
}

/**
 * The string that is used to describe a section of the course.
 *
 * @return string The section description.
 */
function callback_columns_definition() {
    return get_string('sectionname', 'format_columns');
}

function callback_columns_get_section_name($course, $section) {
    // We can't add a node without any text
    if ((string)$section->name !== '') {
        return format_string($section->name, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));
    } else if ($section->section == 0) {
        return get_string('section0name', 'format_columns');
    } else {
        return get_string('topic').' '.$section->section;
    }
}

/**
 * Declares support for course AJAX features.
 *
 * @see course_format_ajax_support().
 * @return stdClass.
 */
function callback_columns_ajax_support() {
    $ajaxsupport = new stdClass();
    $ajaxsupport->capable = true;
    $ajaxsupport->testedbrowsers = array('MSIE' => 8.0, 'Gecko' => 20061111, 'Opera' => 9.0, 'Safari' => 531, 'Chrome' => 6.0);
    return $ajaxsupport;
}

/**
 * Callback function to do some action after section move.
 *
 * @param stdClass $course The course entry from DB.
 * @return array This will be passed in ajax respose.
 */
function callback_columns_ajax_section_move($course) {
    global $COURSE, $PAGE;

    $titles = array();
    rebuild_course_cache($course->id);
    $modinfo = get_fast_modinfo($COURSE);
    $renderer = $PAGE->get_renderer('format_topcoll');
    if ($renderer && ($sections = $modinfo->get_section_info_all())) {
        foreach ($sections as $number => $section) {
            $titles[$number] = $renderer->section_title($section, $course);
        }
    }
    return array('sectiontitles' => $titles, 'action' => 'move');
}

/**
 * Gets the format setting for the course or if it does not exist, create it.
 * CONTRIB-3378.
 * @param int $courseid The course identifier.
 * @return int The format setting.
 */
function get_columns_setting($courseid) {
    global $DB;
    global $CNCFG;

    if (!$setting = $DB->get_record('format_columns_settings', array('courseid' => $courseid))) {
        // Default values...
        $setting = new stdClass();
        $setting->courseid = $courseid;
        $setting->columns = $CNCFG->defaultcolumns;

        if (!$setting->id = $DB->insert_record('format_columns_settings', $setting)) {
            error('Could not set format setting. Columns format database is not ready.  An admin must visit notifications.');
        }
    }

    return $setting;
}

/**
 * Sets the format setting for the course or if it does not exist, create it.
 * @param int $courseid The course identifier.
 * @param int $columns The layout columns value to set.
 */
function put_columns_setting($courseid, $columns) {
    global $DB;
    if ($setting = $DB->get_record('format_columns_settings', array('courseid' => $courseid))) {
        $setting->columns = $columns;
        $DB->update_record('format_columns_settings', $setting);
    } else {
        $setting = new stdClass();
        $setting->courseid = $courseid;
        $setting->columns = $columns;
        $DB->insert_record('format_columns_settings', $setting);
    }
}

/**
 * Rests the format setting to the default for all courses that use Columns.
 */
function reset_columns_setting() {
    global $DB;
    global $CNCFG;

    $records = $DB->get_records('format_columns_settings');
    foreach ($records as $record) {
        $record->columns = $CNCFG->defaultcolumns;
        $DB->update_record('format_columns_settings', $record);
    }
}

/**
 * Deletes the settings entry for the given course upon course deletion.
 */
function format_columns_delete_course($courseid) {
    global $DB;

    $DB->delete_records("format_columns_settings", array("courseid" => $courseid));
}