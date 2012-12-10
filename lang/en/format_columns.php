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

// Used by the Moodle Core for identifing the format and displaying in the list of formats for a course in its settings.
$string['namecolumns'] = 'Columns';
$string['formatcolumns'] = 'Columns';
$string['columnssidewidth'] = '28px';

$string['sectionname'] = 'Topic';
$string['pluginname'] = 'Columns format';
$string['section0name'] = 'General';

// MDL-26105
$string['page-course-view-columns'] = 'Any course main page in the columns format';
$string['page-course-view-columns-x'] = 'Any course page in the columns format';

$string['hidefromothers'] = 'Hide topic';
$string['showfromothers'] = 'Show topic';
$string['currentsection'] = 'This topic';
$string['markedthissection'] = 'This topic is highlighted as the current topic';
$string['markthissection'] = 'Highlight this topic as the current topic';

$string['formatsettings'] = 'Format settings';
$string['formatsettingsinformation'] = '<br />To change the settings of the course format, click on the icon to the right.';
$string['setcolumns'] = 'Set columns';
$string['one'] = 'One';
$string['two'] = 'Two';
$string['three'] = 'Three';
$string['four'] = 'Four';
$string['resetcolumns'] = 'Reset columns';
$string['resetallcolumns'] = 'Reset columns for all Columns courses';

// Temporary until MDL-34917 in core.
$string['maincoursepage'] = 'Main course page';

// Help
$string['resetcolumns_help'] = 'Resets the columns to the default values in "/course/format/columns/cnconfig.php" so it will be the same as a course the first time it is in the Columns format.';
$string['resetallcolumns_help'] = 'Resets the columns to the default values in "/course/format/columns/cnconfig.php" for all courses so it will be the same as a course the first time it is in the Columns format.';
$string['setcolumns_help'] = 'How many columns to use.';
?>
