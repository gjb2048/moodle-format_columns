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
require_once($CFG->dirroot . '/course/format/renderer.php');
require_once($CFG->dirroot . '/course/format/columns/lib.php');

class format_columns_renderer extends format_section_renderer_base {

    private $cncolumnwidth = 100; /* Default width in percent of the column(s). */
    private $cncolumnpadding = 0; /* Defailt padding in pixels of the column(s). */
    private $mymobiletheme = false; /* As not using the MyMobile theme we can react to the number of columns setting. */

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'cntopics'));
    }

    /**
     * Generate the starting container html for a list of sections when showing columns.
     * @return string HTML to output.
     */
    protected function start_columns_section_list() {
        return html_writer::start_tag('ul', array('class' => 'cntopics topics', 'style' => 'width:' . $this->cncolumnwidth . '%; float:left; padding:' . $this->cncolumnpadding . 'px;'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('sectionname', 'format_columns');
    }

    /**
     * Generate the content to displayed on the right part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_right_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        if ($section->section != 0) {
            $controls = $this->section_edit_controls($course, $section, $onsectionpage);
            if (!empty($controls)) {
                $o .= implode('<br />', $controls);
            }
        }

        return $o;
    }

    /**
     * Generate the content to displayed on the left part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_left_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o .= get_accesshide(get_string('currentsection', 'format_' . $course->format));
            }
        }
        return $o;
    }

    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     */
    protected function section_edit_controls($course, $section, $onsectionpage = false) {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $controls = array();
        if (has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $controls[] = html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/marked'),
                                    'class' => 'icon ', 'alt' => get_string('markedthistopic'))), array('title' => get_string('markedthistopic'), 'class' => 'editing_highlight'));
            } else {
                $url->param('marker', $section->section);
                $controls[] = html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/marker'),
                                    'class' => 'icon', 'alt' => get_string('markthistopic'))), array('title' => get_string('markthistopic'), 'class' => 'editing_highlight'));
            }
        }

        return array_merge($controls, parent::section_edit_controls($course, $section, $onsectionpage));
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the html for the 'Jump to' menu on a single section page.
     * Temporary until MDL-34917 in core.
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {
        $o = '';
        $section = 1;
        $sectionmenu = array();
        $sectionmenu[0] = get_string('maincoursepage', 'format_columns');  // Section 0 is never jumped to and is therefore used to indicate the main page.  And temporary until MDL-34917 in core.
        $context = context_course::instance($course->id);
        while ($section <= $course->numsections) {
            if (!empty($sections[$section])) {
                $thissection = $sections[$section];
            } else {
                // This will create a course section if it doesn't exist..
                $thissection = get_course_section($section, $course->id);

                // The returned section is only a bare database object rather than
                // a section_info object - we will need at least the uservisible
                // field in it.
                $thissection->uservisible = true;
                $thissection->availableinfo = null;
                $thissection->showavailability = 0;
            }
            $showsection = (has_capability('moodle/course:viewhiddensections', $context) or $thissection->visible or !$course->hiddensections);

            if (($showsection) && ($section != $displaysection)) {
                $sectionmenu[$section] = get_section_name($course, $thissection);
            }
            $section++;
        }

        $select = new single_select(new moodle_url('/course/view.php', array('id' => $course->id)), 'section', $sectionmenu);
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods used for print_section()
     * @param array $modnames used for print_section()
     * @param array $modnamesused used for print_section()
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE;

        // Can we view the section in question?
        $context = context_course::instance($course->id);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);

        if (!isset($sections[$displaysection])) {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sections[$displaysection]->visible && !$canviewhidden) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        // General section if non-empty.
        $thissection = $sections[0];
        if ($thissection->summary or $thissection->sequence or $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            print_section($course, $thissection, $mods, $modnamesused, true, "100%", false, $displaysection);
            if ($PAGE->user_is_editing()) {
                print_section_add_menus($course, 0, $modnames, false, false, $displaysection);
            }
            echo $this->section_footer();
            echo $this->end_section_list();
        }

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $sections, $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        // Title attributes
        $titleattr = 'mdl-align title';
        if (!$sections[$displaysection]->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= html_writer::tag('div', get_section_name($course, $sections[$displaysection]), array('class' => $titleattr));
        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        // The requested section page.
        $thissection = $sections[$displaysection];
        echo $this->section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        print_section($course, $thissection, $mods, $modnamesused, true, '100%', false, $displaysection);
        if ($PAGE->user_is_editing()) {
            print_section_add_menus($course, $displaysection, $modnames, false, false, $displaysection);
        }
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $courselink = html_writer::link(course_get_url($course), get_string('returntomaincoursepage'));
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag('div', $courselink, array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods used for print_section()
     * @param array $modnames used for print_section()
     * @param array $modnamesused used for print_section()
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;
        global $cnsetting;

        $this->mymobiletheme = ($PAGE->theme->name == 'mymobile');  // Not brilliant, but will work!

        $userisediting = $PAGE->user_is_editing();

        $modinfo = get_fast_modinfo($course);
        $courseformat = course_get_format($course);
        $course = $courseformat->get_course();

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        $this->cncolumnwidth = 100; // Reset to default.
        echo $this->start_section_list();

        // General section if non-empty.
        $thissection = $sections[0];
        unset($sections[0]);
        if ($thissection->summary or $thissection->sequence or $PAGE->user_is_editing()) {
            echo $this->section_header($thissection, $course, false, 0);
            print_section($course, $thissection, $mods, $modnamesused, true, "100%", false, 0);
            if ($PAGE->user_is_editing()) {
                print_section_add_menus($course, 0, $modnames, false, false, 0);
            }
            echo $this->section_footer();
        }

        $section = 1;

        $numsections = $course->numsections; // Because we want to manipulate this for column breakpoints.

        $columnbreakpoint = 0;
        if ($numsections < $cnsetting['columns']) {
            $cnsetting['columns'] = $numsections;  // Help to ensure a reasonable display.
        }
        if (($cnsetting['columns'] > 1) && ($this->mymobiletheme == false)) {
            if ($cnsetting['columns'] > 4) {
                // Default in cnconfig.php (and reset in database) or database has been changed incorrectly.
                $cnsetting['columns'] = 4;

                // Update....
                $courseformat->update_columns_columns_setting($cnsetting['columns']);
            }
            $this->cncolumnwidth = 100 / $cnsetting['columns'];
            $this->cncolumnwidth -= 1; // Allow for the padding in %.
            $this->cncolumnpadding = 2; // px
        } elseif ($cnsetting['columns'] < 1) {
            // Default in cnconfig.php (and reset in database) or database has been changed incorrectly.
            $cnsetting['columns'] = 1;

            // Update....
            $courseformat->update_columns_columns_setting($cnsetting['columns']);
        }
        echo $this->end_section_list();
        echo $this->start_columns_section_list();

        $canbreak = false; // Once the first section is shown we can decide if we break on another column.
        $columncount = 1;
        $shownsectioncount = 0;

        while ($section <= $course->numsections) {
            $thissection = $modinfo->get_section_info($section);

            // Show the section if the user is permitted to access it, OR if it's not available
            // but showavailability is turned on
            $showsection = $thissection->uservisible ||
                    ($thissection->visible && !$thissection->available && $thissection->showavailability);
            if (!$showsection) {
                // Hidden section message is overridden by 'unavailable' control
                // (showavailability option).
                if (!$course->hiddensections && $thissection->available) {
                    echo $this->section_hidden($section);
                }
            } else {
                $shownsectioncount++;
                if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                    // Display section summary only.
                    echo $this->section_summary($thissection, $course, $mods);
                } else {
                    echo $this->section_header($thissection, $course, false, 0);
                    if ($thissection->uservisible) {
                        print_section($course, $thissection, $mods, $modnamesused, true, "100%", false, 0);

                        if ($PAGE->user_is_editing()) {
                            print_section_add_menus($course, $section, $modnames, false, false, 0);
                        }
                    }
                    echo html_writer::end_tag('div');
                    echo $this->section_footer();
                }
            }


            if (($canbreak == false) && ($showsection == true)) {
                $canbreak = true;
                $columnbreakpoint = ($shownsectioncount + ($numsections / $cnsetting['columns'])) - 1;
            }

            if (($canbreak == true) && ($shownsectioncount >= $columnbreakpoint) && ($columncount < $cnsetting['columns'])) {
                echo $this->end_section_list();
                echo $this->start_columns_section_list();
                $columncount++;
                // Next breakpoint is...
                $columnbreakpoint += $numsections / $cnsetting['columns'];
            }
            unset($sections[$section]);
            $section++;
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            $modinfo = get_fast_modinfo($course);
            foreach ($sections as $section => $thissection) {
                if (empty($modinfo->sections[$section])) {
                    continue;
                }
                echo $this->stealth_section_header($section);
                print_section($course, $thissection, $mods, $modnamesused);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));

            // Increase number of sections.
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php',
                            array('courseid' => $course->id,
                                'increase' => true,
                                'sesskey' => sesskey()));
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            echo html_writer::link($url, $icon . get_accesshide($straddsection), array('class' => 'increase-sections'));

            if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                                array('courseid' => $course->id,
                                    'increase' => false,
                                    'sesskey' => sesskey()));
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link($url, $icon . get_accesshide($strremovesection), array('class' => 'reduce-sections'));
            }

            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
        }
    }

}
