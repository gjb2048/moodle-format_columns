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
 * @author     G J Barnard - gjbarnard at gmail dot com, {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
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
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

// Horrible backwards compatible parameter aliasing..
if ($ctopic = optional_param('ctopics', 0, PARAM_INT)) { // Collapsed Topics old section parameter.
    $url = $PAGE->url;
    $url->param('section', $ctopic);
    debugging('Outdated collapsed topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($topic = optional_param('topic', 0, PARAM_INT)) { // Topics and Grid old section parameter.
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic / grid param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($week = optional_param('week', 0, PARAM_INT)) { // Weeks old section parameter.
    $url = $PAGE->url;
    $url->param('section', $week);
    debugging('Outdated week param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing..

// make sure all sections are created
$courseformat = course_get_format($course);
$course = $courseformat->get_course();
course_create_sections_if_missing($course, range(0, $course->numsections));

$context = context_course::instance($course->id);

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

$renderer = $PAGE->get_renderer('format_columns');

if (!empty($displaysection)) {
    $renderer->print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
} else {
    $devicetype = core_useragent::get_device_type(); // In moodlelib.php.
    if ($devicetype == "mobile") {
        $portable = 1;
    } else if ($devicetype == "tablet") {
        $portable = 2;
    } else {
        $portable = 0;
    }
    $renderer->set_portable($portable);
    $cnsettings = $courseformat->get_settings();
    ?>
    <style type="text/css" media="screen">
    /* <![CDATA[ */

    <?php
    // Dynamically changing widths with language.
    if ((!$PAGE->user_is_editing()) && ($portable == 0)) {
        echo '.course-content ul.ctopics li.section.main .content, .course-content ul.ctopics li.tcsection .content {';
        echo 'margin: 0 ' . get_string('topcollsidewidth', 'format_topcoll');
        echo '}';
    }

    // Make room for editing icons.
    if (!$PAGE->user_is_editing()) {
        echo '.course-content ul.ctopics li.section.main .side, .course-content ul.ctopics li.tcsection .side {';
        echo 'width: ' . get_string('topcollsidewidth', 'format_topcoll');
        echo '}';
    }

    // Establish horizontal unordered list for horizontal columns.
    if ($cnsettings['columnorientation'] == 2) {
        echo '.course-content ul.cntopics li.section {';
        // Note: Need '!important' for Bootstrap V3 based themes as '.hidden' class now uses it when V2.3.2 did not.
        echo 'display: inline-block !important;';
        echo 'vertical-align:top;';
        echo '}';
        echo 'body.ie7 .course-content ul.ctopics li.section {';
        echo 'zoom: 1;';
        echo '*display: inline;';
        echo '}';
    }
    ?>;
    /* ]]> */
    </style>
    <?php
    $renderer->print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused);
}

// Include course format js module
$PAGE->requires->js('/course/format/columns/format.js');
