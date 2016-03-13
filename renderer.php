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
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/renderer.php');
require_once($CFG->dirroot . '/course/format/columns/lib.php');

class format_columns_renderer extends format_section_renderer_base {

    private $cncolumnwidth = 100; // Default width in percent of the column(s).
    private $cncolumnpadding = 0; // Default padding in pixels of the column(s).
    private $mobiletheme = false; // As not using a mobile theme we can react to the number of columns setting.
    private $tablettheme = false; // As not using a tablet theme we can react to the number of columns setting.
    private $courseformat; // Our course format object as defined in lib.php.
    private $cnsettings; // Settings for the format.
    private $formatresponsive;
    private $rtl = false;

    /**
     * Constructor method, calls the parent constructor - MDL-21097
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courseformat = course_get_format($page->course); // Needed for columns settings retrieval.

        /* Since format_columns_renderer::section_edit_controls() only displays the 'Set current section' control when editing
           mode is on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any
           other managing capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');

        $this->formatresponsive = get_config('format_columns', 'formatresponsive');

        $this->rtl = right_to_left();
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */

    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'cntopics section-zero'));
    }

    /**
     * Generate the starting container html for a list of sections when showing columns.
     * @return string HTML to output.
     */
    protected function start_columns_section_list() {
        $classes = 'cntopics';
        $attributes = array();
        if ($this->formatresponsive) {
            $style = '';
            if ($this->cnsettings['columnorientation'] == 1) { // Vertical columns.
                $style .= 'width: ' . $this->cncolumnwidth . '%;';
            } else {
                $style .= 'width: 100%;';  // Horizontal columns.
            }
            if ($this->mobiletheme === false) {
                $classes .= ' cnlayout';
            }
            $style .= ' padding-left: ' . $this->cncolumnpadding . 'px; padding-right: ' . $this->cncolumnpadding . 'px;';
            $attributes['style'] = $style;
        } else {
            if ($this->cnsettings['columnorientation'] == 1) { // Vertical columns.
                $classes .= ' ' . $this->get_column_class($this->cnsettings['columns']);
            } else {
                $classes .= ' ' . $this->get_row_class();
            }
        }
        $attributes['class'] = $classes;
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
     * Generate the edit control items of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of edit control items
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {
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

        $isstealth = $section->section > $course->numsections;
        $controls = array();
        if (!$isstealth && $section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $markedthistopic = get_string('markedthistopic');
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marked',
                    'name' => $highlightoff,
                    'pixattr' => array('class' => '', 'alt' => $markedthistopic),
                    'attr' => array('class' => 'editing_highlight', 'title' => $markedthistopic));
            } else {
                $url->param('marker', $section->section);
                $markthistopic = get_string('markthistopic');
                $highlight = get_string('highlight');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marker',
                    'name' => $highlight,
                    'pixattr' => array('class' => '', 'alt' => $markthistopic),
                    'attr' => array('class' => 'editing_highlight', 'title' => $markthistopic));
            }
        }

        $parentcontrols = parent::section_edit_control_items($course, $section, $onsectionpage);

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = array();
            /* We can't use splice because we are using associative arrays.
               Step through the array and merge the arrays. */
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge($merged, $controls);
                }
            }

            return $merged;
        } else {
            return array_merge($controls, $parentcontrols);
        }
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

        // If section is hidden then display grey section link.
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $o = '';
        $title = get_section_name($course, $section);
        $liattributes = array('id' => 'section-'.$section->section, 'class' => $classattr, 'role' => 'region', 'aria-label' => $title);
        if (($this->formatresponsive) && ($this->cnsettings['columnorientation'] == 2)) { // Horizontal column layout.
            $liattributes['style'] = 'width: ' . $this->cncolumnwidth . '%;';
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

        $o .= html_writer::start_tag('div', array('class' => 'summarytext'));
        $o .= $this->format_summary_text($section);
        $o .= html_writer::end_tag('div');
        $o .= $this->section_activity_summary($section, $course, null);

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

        if ((!$this->formatresponsive) && ($section->section != 0) &&
            ($this->cnsettings['columnorientation'] == 2)) { // Horizontal column layout.
            $sectionstyle .= ' ' . $this->get_column_class($this->cnsettings['columns']);
        }
        $liattributes = array(
            'id' => 'section-' . $section->section,
            'class' => 'section main clearfix' . $sectionstyle,
            'role' => 'region',
            'aria-label' => $this->courseformat->get_section_name($section)
        );
        if (($this->formatresponsive) && ($this->cnsettings['columnorientation'] == 2)) { // Horizontal column layout.
            $liattributes['style'] = 'width: ' . $this->cncolumnwidth . '%;';
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

                $rightcontent .= html_writer::link($url, html_writer::empty_tag('img', array(
                    'src' => $this->output->pix_url('t/edit'),
                    'class' => 'iconsmall edit cneditsection', 'alt' => get_string('edit'))),
                     array('title' => get_string('editsummary'), 'class' => 'cneditsection'));
                $rightcontent .= html_writer::empty_tag('br');
            }
            $rightcontent .= $this->section_right_content($section, $course, $onsectionpage);
            $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        }
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null.
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
            $o .= html_writer::link($url, html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
                'class' => 'iconsmall edit', 'alt' => get_string('edit'))), array('title' => get_string('editsummary')));
        }
        $o .= html_writer::end_tag('div');

        $o .= $this->section_availability_message($section, has_capability('moodle/course:viewhiddensections', $context));

        return $o;
    }

    /**
     * Generate the display of the footer part of a section.
     *
     * @return string HTML to output.
     */
    protected function section_footer() {
        $o = html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the header html of a stealth section.
     *
     * @param int $sectionno The section number in the coruse which is being dsiplayed.
     * @return string HTML to output.
     */
    protected function stealth_section_header($sectionno) {
        $o = '';
        $sectionstyle = '';
        $course = $this->courseformat->get_course();
        // Horizontal column layout.
        if ((!$this->formatresponsive) && ($sectionno != 0) && ($this->cnsettings['columnorientation'] == 2)) {
            $sectionstyle .= ' ' . $this->get_column_class($this->cnsettings['columns']);
        }
        $liattributes = array(
            'id' => 'section-' . $sectionno,
            'class' => 'section main clearfix orphaned hidden' . $sectionstyle,
            'role' => 'region',
            'aria-label' => $this->courseformat->get_section_name($course, $sectionno, false)
        );
        if (($this->formatresponsive) && ($this->cnsettings['columnorientation'] == 2)) { // Horizontal column layout.
            $liattributes['style'] = 'width: ' . $this->cncolumnwidth . '%;';
        }
        $o .= html_writer::start_tag('li', $liattributes);
        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $section = $this->courseformat->get_section($sectionno);
        $rightcontent = $this->section_right_content($section, $course, false);
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $o .= $this->output->heading(get_string('orphanedactivitiesinsectionno', '', $sectionno), 3, 'sectionname');
        return $o;
    }

    /**
     * Generate the html for a hidden section.
     *
     * @param stdClass $section The section in the course which is being displayed.
     * @param int|stdClass $courseorid The course to get the section name for (object or just course id).
     * @return string HTML to output.
     */
    protected function section_hidden($section, $courseorid = null) {
        $o = '';
        $course = $this->courseformat->get_course();
        $sectionstyle = 'section main clearfix hidden';
        if ((!$this->formatresponsive) && ($this->tcsettings['columnorientation'] == 2)) { // Horizontal column layout.
            $sectionstyle .= ' ' . $this->get_column_class($this->cnsettings['columns']);
        }
        $liattributes = array(
            'id' => 'section-' . $section->section,
            'class' => $sectionstyle,
            'role' => 'region',
            'aria-label' => $this->courseformat->get_section_name($course, $section, false)
        );
        if (($this->formatresponsive) && ($this->cnsettings['columnorientation'] == 2)) { // Horizontal column layout.
            $liattributes['style'] = 'width: ' . $this->cncolumnwidth . '%;';
        }

        $o .= html_writer::start_tag('li', $liattributes);
        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $leftcontent = $this->section_left_content($section, $course, false);
            $rightcontent = $this->section_right_content($section, $course, false);

            if ($this->rtl) {
                // Swap content.
                $o .= html_writer::tag('div', $leftcontent, array('class' => 'right side'));
                $o .= html_writer::tag('div', $rightcontent, array('class' => 'left side'));
            } else {
                $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
                $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
            }

        }

        $o .= html_writer::start_tag('div', array('class' => 'content sectionhidden'));

        $title = get_string('notavailable');
        if ((($this->mobiletheme === false) && ($this->tablettheme === false)) || ($this->userisediting)) {
            $o .= $this->output->heading($title, 3, 'section-title');
        } else {
            $o .= html_writer::tag('h3', $title); // Moodle H3's look bad on mobile / tablet so use plain.
        }
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Output the html for a multiple section page.
     *
     * @param stdClass $course The course entry from DB.
     * @param array $sections The course_sections entries from the DB.
     * @param array $mods used for print_section().
     * @param array $modnames used for print_section().
     * @param array $modnamesused used for print_section().
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
        if ($this->formatresponsive) {
            $this->cncolumnwidth = 100; // Reset to default.
        }
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

            if ($this->formatresponsive) {
                $this->cncolumnwidth = 100 / $this->cnsettings['columns'];
                $this->cncolumnpadding = 0; // In px.
            }
        } else if ($this->cnsettings['columns'] < 1) {
            // Default in cnconfig.php (and reset in database) or database has been changed incorrectly.
            $this->cnsettings['columns'] = 1;

            // Update....
            $this->courseformat->update_columns_columns_setting($this->cnsettings['columns']);
        }
        echo $this->end_section_list();
        if ((!$this->formatresponsive) && ($this->cnsettings['columnorientation'] == 1)) { // Vertical columns.
            echo html_writer::start_tag('div', array('class' => $this->get_row_class()));
        }
        echo $this->start_columns_section_list();

        $canbreak = false; // Once the first section is shown we can decide if we break on another column.
        $columncount = 1;
        $shownsectioncount = 0;

        while ($section <= $course->numsections) {
            $thissection = $modinfo->get_section_info($section);

            /* Show the section if the user is permitted to access it, OR if it's not available
               but showavailability is turned on. */
            $showsection = $thissection->uservisible ||
               ($thissection->visible && !$thissection->available && $thissection->showavailability);
            if (!$showsection) {
                /* Hidden section message is overridden by 'unavailable' control
                   (showavailability option). */
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

            // Only break in non-mobile themes or using a reponsive theme.
            if ($this->mobiletheme === false) {
                if (($canbreak == false) && ($showsection == true)) {
                    $canbreak = true;
                    if ($this->cnsettings['columnorientation'] == 1) { // Vertical mode.
                        $columnbreakpoint = ($shownsectioncount + ($numsections / $this->cnsettings['columns'])) - 1;
                    } else {
                        $columnbreakpoint = ($shownsectioncount + $this->cnsettings['columns']) - 1;
                    }
                }

                if (($canbreak == true) &&
                    ($shownsectioncount >= $columnbreakpoint) &&
                    (($columncount < $this->cnsettings['columns']) || ($this->cnsettings['columnorientation'] == 2))) {
                    echo $this->end_section_list();
                    echo $this->start_columns_section_list();
                    $columncount++;
                    // Next breakpoint is...
                    if ($this->cnsettings['columnorientation'] == 1) { // Vertical mode.
                        $columnbreakpoint += $numsections / $this->cnsettings['columns'];
                    } else {
                        $columnbreakpoint += $this->cnsettings['columns'];
                    }
                }
            }
            unset($sections[$section]);
            $section++;
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection->section, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();
            if ((!$this->formatresponsive) && ($this->cnsettings['columnorientation'] == 1)) { // Vertical columns.
                echo html_writer::end_tag('div');
            }

            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));

            // Increase number of sections.
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php', array(
                'courseid' => $course->id,
                'increase' => true,
                'sesskey' => sesskey()));
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            echo html_writer::link($url, $icon . get_accesshide($straddsection), array('class' => 'increase-sections'));

            if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php', array(
                    'courseid' => $course->id,
                    'increase' => false,
                    'sesskey' => sesskey()));
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link($url, $icon . get_accesshide($strremovesection), array('class' => 'reduce-sections'));
            }

            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
            if ((!$this->formatresponsive) && ($this->cnsettings['columnorientation'] == 1)) { // Vertical columns.
                echo html_writer::end_tag('div');
            }
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

    protected function get_row_class() {
        return 'row-fluid';
    }

    protected function get_column_class($columns) {
        $colclasses = array(1 => 'span12', 2 => 'span6', 3 => 'span4', 4 => 'span3');

        return $colclasses[$columns];
    }

    public function get_format_responsive() {
        return $this->formatresponsive;
    }
}
