<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The main mod_bbbmonitor configuration form.
 *
 * @package     mod_bbbmonitor
 * @copyright   2021 Simon Software & Services <paolo@simonsoftware.it>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_bbbmonitor
 * @copyright   2021 Simon Software & Services <paolo@simonsoftware.it>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_bbbmonitor_mod_form extends moodleform_mod
{

    /**
     * Defines forms elements
     */
    public function definition()
    {
        global $CFG;

        $mform = $this->_form;
        $course = get_course($this->current->course);
        $context = context_course::instance($course->id);


        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('bbbmonitorname', 'mod_bbbmonitor'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'bbbmonitorname', 'mod_bbbmonitor');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of mod_bbbmonitor settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        $mform->addElement('header', 'bbbmeetingselection', get_string('bbbmeetingselection', 'mod_bbbmonitor'));

        $this->bbbmonitor_mform_add_block_meeting($mform, $course);

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    private function bbbmonitor_mform_add_block_meeting(&$mform, $course)
    {
        global $DB;
        $type = 'select';
        $name = 'meetingid';
        $selectMeetings = array();

        $meetings = $DB->get_records('bigbluebuttonbn', ['course' => $course->id]);
        foreach($meetings as $meeting) {
            $key = $meeting->meetingid;
            $value = $meeting->name;
            $selectMeetings[$key] = $value;
        }
        $mform->addElement($type, $name, get_string('field_roomselected', 'bbbmonitor'), $selectMeetings);
        $mform->addHelpButton('type', 'field_roomselected', 'bbbmonitor');
    }

}
