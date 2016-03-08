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
require_once($CFG->dirroot . '/course/format/lib.php'); // For format_base.

class format_columns extends format_base {

    private $settings;

    /**
     * Returns the format's settings and gets them if they do not exist.
     * @return type The settings as an array.
     */
    public function get_settings() {
        if (empty($this->settings) == true) {
            $this->settings = $this->get_format_options();
        }
        return $this->settings;
    }

    /**
     * Indicates this format uses sections.
     *
     * @return bool Returns true
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Returns the default section name for the format.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        /* Follow the same logic so that this method is supported.  The MDL-51610 enchancement refactored things,
           but that is not appropriate for us. */
        return $this->get_section_name($section);
    }

    /**
     * Gets the name for the provided section.
     *
     * @param stdClass $section The section.
     * @return string The section name.
     */
    public function get_section_name($section) {
        $course = $this->get_course();
        $section = $this->get_section($section);
        // We can't add a node without any text.
        if ((string) $section->name !== '') {
            return format_string($section->name, true, array('context' => context_course::instance($course->id)));
        } else if ($section->section == 0) {
            return get_string('section0name', 'format_columns');
        } else {
            return get_string('topic') . ' ' . $section->section;
        }
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            if ($sr !== null) {
                if ($sr) {
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else {
                $usercoursedisplay = $course->coursedisplay;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                global $CFG;
                if (empty($CFG->linkcoursesections) && !empty($options['navigation'])) {
                    return null;
                }
                $url->set_anchor('section-' . $sectionno);
            }
        }
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     * The property (array)testedbrowsers can be used as a parameter for {@link ajaxenabled()}.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    public function ajax_section_move() {
        global $PAGE;
        $titles = array();
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
            }
        }
        return array('sectiontitles' => $titles, 'action' => 'move');
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
        );
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * Columns format uses the following options:
     * - coursedisplay
     * - numsections
     * - hiddensections
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;

        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => $courseconfig->numsections,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => get_config('format_columns', 'defaultcoursedisplay'),
                    'type' => PARAM_INT,
                ),
                'columns' => array(
                    'default' => get_config('format_columns', 'defaultcolumns'),
                    'type' => PARAM_INT,
                ),
                'columnorientation' => array(
                    'default' => get_config('format_columns', 'defaultcolumnorientation'),
                    'type' => PARAM_INT,
                )
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            global $COURSE;
            $coursecontext = context_course::instance($COURSE->id);

            $courseconfig = get_config('moodlecourse');
            $sectionmenu = array();
            for ($i = 0; $i <= $courseconfig->maxsections; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = array(
                'numsections' => array(
                    'label' => new lang_string('numbersections', 'format_columns'),
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                )
            );
            if (has_capability('format/columns:changecolumns', $coursecontext)) {
                $courseformatoptionsedit['columns'] = array(
                    'label' => new lang_string('setcolumns', 'format_columns'),
                    'help' => 'setcolumns',
                    'help_component' => 'format_columns',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => get_string('one', 'format_columns'), // Default.
                            2 => get_string('two', 'format_columns'), // Two.
                            3 => get_string('three', 'format_columns'), // Three.
                            4 => get_string('four', 'format_columns')) // Four.
                    )
                );
                $courseformatoptionsedit['columnorientation'] = array(
                    'label' => new lang_string('setcolumnorientation', 'format_columns'),
                    'help' => 'setcolumnorientation',
                    'help_component' => 'format_columns',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(1 => get_string('columnvertical', 'format_columns'),
                              2 => get_string('columnhorizontal', 'format_columns')) // Default.
                    )
                );
            } else {
                $courseformatoptionsedit['columns'] = array(
                    'label' => new lang_string('setcolumns', 'format_columns'), 'element_type' => 'hidden');
                $courseformatoptionsedit['columnorientation'] = array(
                    'label' => new lang_string('setcolumnorientation', 'format_columns'), 'element_type' => 'hidden');
            }
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form
     *
     * This function is called from {@link course_edit_form::definition_after_data()}
     *
     * @param MoodleQuickForm $mform form the elements are added to
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form
     * @return array array of references to the added form elements
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {

        $elements = parent::create_edit_form_elements($mform, $forsection);

        if ($forsection == false) {
            global $COURSE, $USER;
            /* Increase the number of sections combo box values if the user has increased the number of sections
               using the icon on the course page beyond course 'maxsections' or course 'maxsections' has been
               reduced below the number of sections already set for the course on the site administration course
               defaults page.  This is so that the number of sections is not reduced leaving unintended orphaned
               activities / resources. */
            $maxsections = get_config('moodlecourse', 'maxsections');
            $numsections = $mform->getElementValue('numsections');
            $numsections = $numsections[0];
            if ($numsections > $maxsections) {
                $element = $mform->getElement('numsections');
                for ($i = $maxsections + 1; $i <= $numsections; $i++) {
                    $element->addOption("$i", $i);
                }
            }

            $coursecontext = context_course::instance($COURSE->id);

            $changecolumns = has_capability('format/columns:changecolumns', $coursecontext);
            $resetall = is_siteadmin($USER); // Site admins only.

            if ($changecolumns || $resetall) {
                $elements[] = $mform->addElement('header', 'cnreset', get_string('cnreset', 'format_columns'));
            }

            if ($changecolumns) {
                $mform->addHelpButton('cnreset', 'cnreset', 'format_columns', '', true);
                $elements[] = $mform->addElement('checkbox', 'resetcolumns', get_string('resetcolumns', 'format_columns'), false);
                $mform->addHelpButton('resetcolumns', 'resetcolumns', 'format_columns', '', true);
            }

            if (is_siteadmin($USER)) {
                $elements[] = $mform->addElement('checkbox', 'resetallcolumns',
                    get_string('resetallcolumns', 'format_columns'), false);
                $mform->addHelpButton('resetallcolumns', 'resetallcolumns', 'format_columns', '', true);
            }
        }

        return $elements;
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'Columns', we try to copy options
     * 'coursedisplay', 'numsections' and 'hiddensections' from the previous format.
     * If previous course format did not have 'numsections' option, we populate it with the
     * current number of sections.  The defaults will come from 'course_format_options'.
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB; // MDL-37976.
        /* Notes: Using 'unset' to really ensure that the reset form elements never get into the database.
                  This has to be done here so that the reset occurs after we have done updates such that the
                  reset itself is not seen as an update. */
        $resetcolumns = false;
        $resetallcolumns = false;
        if (isset($data->resetcolumns) == true) {
            $resetcolumns = true;
            unset($data->resetcolumns);
        }
        if (isset($data->resetallcolumns) == true) {
            $resetallcolumns = true;
            unset($data->resetallcolumns);
        }

        if ($oldcourse !== null) {
            $data = (array) $data;
            $oldcourse = (array) $oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'numsections') {
                        /* If previous format does not have the field 'numsections'
                           and $data['numsections'] is not set,
                           we fill it with the maximum section number from the DB. */
                        $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections}
                            WHERE course = ?', array($this->courseid));
                        if ($maxsection) {
                            // If there are no sections, or just default 0-section, 'numsections' will be set to default.
                            $data['numsections'] = $maxsection;
                        }
                    }
                }
            }
        }
        $changes = $this->update_format_options($data);

        // Now we can do the reset.
        if ($resetcolumns == true) {
            $this->reset_columns_setting($this->courseid);
            $changes = true;
        }

        if ($resetallcolumns == true) {
            $this->reset_columns_setting(0);
            $changes = true;
        }

        return $changes;
    }

    /**
     * Resets the format setting to the default.
     * @param int $courseid If not 0, then a specific course to reset.
     */
    public function reset_columns_setting($courseid) {
        global $DB, $USER, $COURSE;

        $currentcourseid = 0;
        if ($courseid == 0) {
            $records = $DB->get_records('course_format_options', array('format' => $this->format), '', 'id, courseid');
        } else {
            $records = $DB->get_records('course_format_options', array(
                'courseid' => $courseid, 'format' => $this->format), '', 'id, courseid');
        }

        $resetallifall = ((is_siteadmin($USER)) || ($courseid != 0)); // Will be true if reset all capability or a single course.

        $coursecontext = context_course::instance($COURSE->id);

        $updatedata = array();
        if (has_capability('format/columns:changecolumns', $coursecontext) && $resetallifall) {
            $updatedata['coursedisplay'] = get_config('format_columns', 'defaultcoursedisplay');
            $updatedata['columns'] = get_config('format_columns', 'defaultcolumns');
            $updatedata['columnorientation'] = get_config('format_columns', 'defaultcolumnorientation');
        }

        foreach ($records as $record) {
            if ($currentcourseid != $record->courseid) {
                $currentcourseid = $record->courseid; // Only do once per course.
                $ourcourseid = $this->courseid;
                $this->courseid = $currentcourseid;
                $this->update_format_options($updatedata);
                $this->courseid = $ourcourseid;
            }
        }
    }

    /**
     * Restores the course settings when restoring a Moodle 2.3 or below (bar 1.9) course and sets the settings when upgrading
     * from a prevous version.  Hence no need for 'coursedisplay' as that is a core rather than CN specific setting and not
     * in the old 'format_columns_settings' table.
     * @param int $courseid If not 0, then a specific course to reset.
     * @param int $columns The columns to use, see cnconfig.php.
     */
    public function restore_columns_setting($courseid, $columns) {
        $currentcourseid = $this->courseid;  // Save for later - stack data model.
        $this->courseid = $courseid;
        // Create data array.
        $data = array('columns' => $columns);

        $this->update_course_format_options($data);

        $this->courseid = $currentcourseid;
    }

    /**
     * Updates the number of columns when the renderer detects that they are wrong.
     * @param int $columns The columns to use, see cnconfig.php.
     */
    public function update_columns_columns_setting($columns) {
        // Create data array.
        $data = array('columns' => $columns);

        $this->update_course_format_options($data);
    }
}

/**
 * Used to display the course structure for a course where format=Columns
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = Columns.
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
