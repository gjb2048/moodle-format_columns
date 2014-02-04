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
    private $cncolumnpadding = 0; /* Default padding in pixels of the column(s). */
    private $mobiletheme = false; /* As not using a mobile theme we can react to the number of columns setting. */
    private $tablettheme = false; /* As not using a tablet theme we can react to the number of columns setting. */
    private $courseformat; // Our course format object as defined in lib.php;
    private $cnsettings; // Settings for the format.

    /**
     * Constructor method, calls the parent constructor - MDL-21097
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courseformat = course_get_format($page->course); // Needed for columns settings retrieval.

        // Since format_columns_renderer::section_edit_controls() only displays the 'Set current section' control when editing mode is on
        // we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any other managing capability.
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

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
        $classes = 'cntopics topics';
        $style = '';
        if ($this->cnsettings['columnorientation'] == 1) {
            $style .= 'width:' . $this->cncolumnwidth . '%;';  // Vertical columns.
        } else {
            $style .= 'width:100%;';  // Horizontal columns.
        }
        if ($this->mobiletheme === false) {
            $classes .= ' cnlayout';
        }
        $style .= ' padding:' . $this->cncolumnpadding . 'px;';
        $attributes = array('class' => $classes);
        $attributes['style'] = $style;
        return html_writer::start_tag('ul', $attributes);
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
     * Generate a summary of a section for display on the 'course index page'
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods) {
        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $o = '';
        $title = get_section_name($course, $section);
        $liattributes = array('id' => 'section-'.$section->section, 'class' => $classattr, 'role'=>'region', 'aria-label'=> $title);
        if ($this->cnsettings['columnorientation'] == 2) { // Horizontal column layout.
            $liattributes['style'] = 'width:' . $this->cncolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);

        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if ($section->uservisible) {
            $title = html_writer::tag('a', $title,
                    array('href' => course_get_url($course, $section->section), 'class' => $linkclasses));
        }
        $o .= $this->output->heading($title, 3, 'section-title');

        $o.= html_writer::start_tag('div', array('class' => 'summarytext'));
        $o.= $this->format_summary_text($section);
        $o.= html_writer::end_tag('div');
        $o.= $this->section_activity_summary($section, $course, null);

        $context = context_course::instance($course->id);
        $o .= $this->section_availability_message($section,
                has_capability('moodle/course:viewhiddensections', $context));

        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null) {
        global $PAGE;

        $o = '';
        $sectionstyle = '';
        $context = context_course::instance($course->id);

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $liattributes = array(
            'id' => 'section-' . $section->section,
            'class' => 'section main clearfix' . $sectionstyle,
            'role' => 'region',
            'aria-label' => $this->courseformat->get_section_name($section)
        );
        if ($this->cnsettings['columnorientation'] == 2) { // Horizontal column layout.
            $liattributes['style'] = 'width:' . $this->cncolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);

        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
        }

        $context = context_course::instance($course->id);
        if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
            $rightcontent = '';
            if (($section->section != 0) && $PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
                $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));

                $rightcontent .= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                    'class' => 'iconsmall edit cneditsection', 'alt' => get_string('edit'))), array('title' => get_string('editsummary'), 'class' => 'cneditsection'));
                $rightcontent .= html_writer::empty_tag('br');
            }
            $rightcontent .= $this->section_right_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        }
        $o.= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        if (($onsectionpage == false) && ($section->section != 0)) {
            $title = get_section_name($course, $section);
            if (($this->mobiletheme === false) && ($this->tablettheme === false)) {
                $o .= $this->output->heading($title, 3, 'sectionname');
            } else {
                $o .= html_writer::tag('h3', $title); // Moodle H3's look bad on mobile / tablet with Columns so use plain.
            }
        } else {
            // When on a section page, we only display the general section title, if title is not the default one.
            $hasnamesecpg = ($section->section == 0 && (string) $section->name !== '');

            if ($hasnamesecpg) {
                $o .= $this->output->heading($this->section_title($section, $course), 3, 'sectionname');
            }
        }

        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        $o .= $this->format_summary_text($section);

        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
            $url = new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn));
            $o.= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                                'class' => 'iconsmall edit', 'alt' => get_string('edit'))), array('title' => get_string('editsummary')));
        }
        $o .= html_writer::end_tag('div');

        $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));

        return $o;
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
        return parent::print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
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

        $userisediting = $PAGE->user_is_editing();

        $modinfo = get_fast_modinfo($course);
        $course = $this->courseformat->get_course();
        if (empty($this->cnsettings)) {
            $this->cnsettings = $this->courseformat->get_settings();
        }

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

        $sections = $modinfo->get_section_info_all();
        // General section if non-empty.
        $thissection = $sections[0];
        unset($sections[0]);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->section_header($thissection, $course, false, 0);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
            echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0, 0);
            echo $this->section_footer();
        }

        $section = 1;

        $numsections = $course->numsections; // Because we want to manipulate this for column breakpoints.

        $columnbreakpoint = 0;
        if ($numsections < $this->cnsettings['columns']) {
            $this->cnsettings['columns'] = $numsections;  // Help to ensure a reasonable display.
        }
        if (($this->cnsettings['columns'] > 1) && ($this->mobiletheme == false)) {
            if ($this->cnsettings['columns'] > 4) {
                // Default in cnconfig.php (and reset in database) or database has been changed incorrectly.
                $this->cnsettings['columns'] = 4;

                // Update....
                $courseformat->update_columns_columns_setting($this->cnsettings['columns']);
            }
            if (($this->tablettheme === true) && ($this->cnsettings['columns'] > 2)) {
                // Use a maximum of 2 for tablets.
                $this->cnsettings['columns'] = 2;
            }

            $this->cncolumnwidth = 100 / $this->cnsettings['columns'];
            if ($this->cnsettings['columnorientation'] == 2) { // Horizontal column layout.
                $this->cncolumnwidth -= 1;
            } else {
                $this->cncolumnwidth -= 0.2;
            }
            $this->cncolumnpadding = 0; // px
        } elseif ($this->cnsettings['columns'] < 1) {
            // Default in cnconfig.php (and reset in database) or database has been changed incorrectly.
            $this->cnsettings['columns'] = 1;

            // Update....
            $this->courseformat->update_columns_columns_setting($this->cnsettings['columns']);
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
                    echo $this->section_summary($thissection, $course, null);
                } else {
                    echo $this->section_header($thissection, $course, false, 0);
                    if ($thissection->uservisible) {
                        echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                        echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0);
                    }
                    echo html_writer::end_tag('div');
                    echo $this->section_footer();
                }
            }


            if ($this->cnsettings['columnorientation'] == 1) {  // Only break columns in vertical mode.
                if (($canbreak == false) && ($showsection == true)) {
                    $canbreak = true;
                    $columnbreakpoint = ($shownsectioncount + ($numsections / $this->cnsettings['columns'])) - 1;
                }

                if (($canbreak == true) && ($shownsectioncount >= $columnbreakpoint) && ($columncount < $this->cnsettings['columns'])) {
                    echo $this->end_section_list();
                    echo $this->start_columns_section_list();
                    $columncount++;
                    // Next breakpoint is...
                    $columnbreakpoint += $numsections / $this->cnsettings['columns'];
                }
            }
            unset($sections[$section]);
            $section++;
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    // this is not stealth section or it is empty
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection->section, 0);
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

    public function set_portable($portable) {
        switch ($portable) {
            case 1:
                $this->mobiletheme = true;
            break;
            case 2:
                $this->tablettheme = true;
            break;
            default:
                $this->mobiletheme = false;
                $this->tablettheme = false;
            break;
        }
    }
}
