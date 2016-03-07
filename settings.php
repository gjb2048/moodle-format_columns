<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    /* Default course display.
     * Course display default, can be either one of:
     * COURSE_DISPLAY_SINGLEPAGE or - All sections on one page.
     * COURSE_DISPLAY_MULTIPAGE     - One section per page.
     * as defined in moodlelib.php.
     */
    $name = 'format_columns/defaultcoursedisplay';
    $title = get_string('defaultcoursedisplay', 'format_columns');
    $description = get_string('defaultcoursedisplay_desc', 'format_columns');
    $default = COURSE_DISPLAY_SINGLEPAGE;
    $choices = array(
        COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
        COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default number of columns between 1 and 4.
    $name = 'format_columns/defaultcolumns';
    $title = get_string('defaultcolumns', 'format_columns');
    $description = get_string('defaultcolumns_desc', 'format_columns');
    $default = 2;
    $choices = array(
        1 => new lang_string('one', 'format_columns'),   // Default.
        2 => new lang_string('two', 'format_columns'),   // Two.
        3 => new lang_string('three', 'format_columns'), // Three.
        4 => new lang_string('four', 'format_columns')   // Four.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default column orientation - 1 = vertical and 2 = horizontal.
    $name = 'format_columns/defaultcolumnorientation';
    $title = get_string('defaultcolumnorientation', 'format_columns');
    $description = get_string('defaultcolumnorientation_desc', 'format_columns');
    $default = 2;
    $choices = array(
        1 => new lang_string('columnvertical', 'format_columns'),
        2 => new lang_string('columnhorizontal', 'format_columns') // Default.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Format responsive.  Turn on to support a non responsive theme theme. */
    $name = 'format_columns/formatresponsive';
    $title = get_string('formatresponsive', 'format_columns');
    $description = get_string('formatresponsive_desc', 'format_columns');
    $default = 0;
    $choices = array(
        0 => new lang_string('off', 'format_columns'), // Off.
        1 => new lang_string('on', 'format_columns')   // On.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));
}