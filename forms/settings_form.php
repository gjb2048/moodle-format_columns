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
require_once("$CFG->libdir/formslib.php");

class set_settings_form extends moodleform {

    function definition() {
        global $CFG, $USER; //, $DB;

        $mform = $this->_form;
        $instance = $this->_customdata;

        $mform->addElement('header', 'setcolumns', get_string('setcolumns', 'format_columns'));
        //$mform->addHelpButton('setcolumns', 'setcolumns', 'format_columns', '', true);

        $formcoursecolumns =
                array(1 => get_string('one', 'format_columns'), // Default
                    2 => get_string('two', 'format_columns'), // Two   
                    3 => get_string('three', 'format_columns'), // Three
                    4 => get_string('four', 'format_columns')); // Four
        $mform->addElement('select', 'setcolumnsnew', get_string('setcolumns', 'format_columns'), $formcoursecolumns);
        $mform->setDefault('setcolumnsnew', $instance['setcolumns']);
        $mform->addHelpButton('setcolumnsnew', 'setcolumns', 'format_columns', '', true);

        $mform->addElement('checkbox', 'resetcolumns', get_string('resetcolumns', 'format_columns'), false);
        $mform->addHelpButton('resetcolumns', 'resetcolumns', 'format_columns', '', true);

        if (is_siteadmin($USER)) {
            $mform->addElement('checkbox', 'resetallcolumns', get_string('resetallcolumns', 'format_columns'), false);
            $mform->addHelpButton('resetallcolumns', 'resetallcolumns', 'format_columns', '', true);
        }

        // hidden params
        $mform->addElement('hidden', 'id', $instance['courseid']);
        $mform->setType('id', PARAM_INT);
        // buttons
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
}
?>