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
 * Prints an instance of mod_bbbmonitor.
 *
 * @package     mod_bbbmonitor
 * @copyright   2021 Simon Software & Services <paolo@simonsoftware.it>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
global $DB, $PAGE, $OUTPUT;

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$b = optional_param('b', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('bbbmonitor', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('bbbmonitor', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($b) {
    $moduleinstance = $DB->get_record('bbbmonitor', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('bbbmonitor', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_bbbmonitor'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_bbbmonitor\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('bbbmonitor', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/bbbmonitor/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

echo $OUTPUT->header();

$logs = $DB->get_records('bbblog', ['webinar_id' => $moduleinstance->meetingid]);

echo '<h2>Meeting: ' . $moduleinstance->name . '</h2>';
echo '<h4>' . $moduleinstance->meetingid . '</h4>';
echo '<br/>';
echo $OUTPUT->download_dataformat_selector(get_string('exportlogs', 'bbbmonitor'), 'download.php', 'dataformat', ['meetingid' => $moduleinstance->meetingid]);

if (count($logs) == 0) {
    echo "<p>No data for this meeting</p>";
} else {
    $table = new html_table();
    $table->head = array('Datetime','Webinar Name', 'Webinar ID', 'User', 'Event');
    $table->data = [];
    foreach ($logs as $log) {
        $table->data[] = array(date('c', $log->logtime/1000), $log->webinar_name, $log->webinar_id,$log->username,$log->event);
    }
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
