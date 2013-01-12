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
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/columns/lib.php');

/**
 * restore plugin class that provides the necessary information
 * needed to restore one columns course format
 */
class restore_format_columns_plugin extends restore_format_plugin {

    /**
     * Returns the paths to be handled by the plugin at course level
     */
    protected function define_course_plugin_structure() {

        $paths = array();

        // Add own format stuff
        $elename = 'columns'; // This defines the postfix of 'process_*' below.
        $elepath = $this->get_pathfor('/'); // This is defines the nested tag within 'plugin_format_columns_course' to allow '/course/plugin_format_columns_course' in the path therefore as a path structure representing the levels in course.xml in the backup file.
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Process the 'plugin_format_columns_course' element within the 'course' element in the 'course.xml' file in the '/course' folder
     * of the zipped backup 'mbz' file.
     */
    public function process_columns($data) {
        global $DB;

        $data = (object) $data;

        // We only process this information if the course we are restoring to
        // has 'columns' format (target format can change depending of restore options)
        $format = $DB->get_field('course', 'format', array('id' => $this->task->get_courseid()));
        if ($format != 'columns') {
            return;
        }

        $data->courseid = $this->task->get_courseid();

        if (!($course = $DB->get_record('course', array('id' => $data->courseid)))) {
            print_error('invalidcourseid', 'error');
        } // From /course/view.php
        $courseformat = course_get_format($course);

        if (isset($data->columns)) {
            // In $CFG->dirroot.'/course/format/columns/lib.php'...
            $courseformat->restore_columns_setting($data->courseid, $data->columns);
        }

        // No need to annotate anything here
    }

    protected function after_execute_structure() {
        
    }

}