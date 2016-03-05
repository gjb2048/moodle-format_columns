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
require_once($CFG->dirroot . '/course/format/lib.php');
require_once($CFG->dirroot . '/course/format/columns/lib.php');

function xmldb_format_columns_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    $result = true;

    // From Moodle 2.3 ....
    if ($result && $oldversion < 2012120900) { // Note to self, Moodle 2.3 version cannot now be greater than this.
        // Rename table format_columns_settings if it exists.
        $table = new xmldb_table('format_columns_settings');
        // Rename the table...
        if ($dbman->table_exists($table)) {
            // Extract data out of table and put in course settings table for 2.4.
            $records = $DB->get_records('format_columns_settings');
            foreach ($records as $record) {
                // Check that the course still exists - CONTRIB-4065...
                if ($DB->record_exists('course', array('id' => $record->courseid))) {
                    $courseformat = course_get_format($record->courseid);  // In '/course/format/lib.php'.
                    /* Only update if the current format is 'columns' as we must have an instance of 'format_columns'
                       (in 'lib.php') returned by the above.  Thanks to Marina Glancy for this :).
                       If there are entries that existed for courses that were originally columns, then they will be lost.  However
                       the code copes with this through the employment of defaults and I dont think the underlying code desires
                       entries in the course_format_settings table for courses of a format that belong to another format. */
                    if ($courseformat->get_format() == 'columns') {
                        // In '/course/format/columns/lib.php'.
                        $courseformat->restore_columns_setting($record->courseid, $record->columns);
                    }
                }
            }
            // Farewell old settings table.
            $dbman->drop_table($table);
        } //else Nothing to do as settings put in DB on first use.
    }

    // Automatic 'Purge all caches'....
    if ($oldversion < 2114052000) {
        purge_all_caches();
    }

    return $result;
}