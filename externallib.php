<?php

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . "/externallib.php");

class mod_bbbmonitor_external extends external_api
{
    /**
     * Returns welcome message
     * @return string_test_class welcome message
     * @throws dml_exception
     */

    public static function log($event = '', $timestamp = '', $domain = '')
    {
        global $DB;
        $ev = json_decode($event)[0];
        $operations = ['user-joined', 'user-left'];

        if (array_search($ev->data->id, $operations) === false) return "Not Logged";
        $meeting = $ev->data->attributes->meeting;
        $user = $ev->data->attributes->user;
        $username = $DB->get_record("user", ["id" => $user->{'external-user-id'}])->username;
        $obj = new stdClass();
        $obj->logtime = (int) $timestamp;
        $obj->webinar_name = $meeting->name ?: '';
        $obj->webinar_id = explode('-', $meeting->{'external-meeting-id'})[0] ?: '';
        $obj->username = $username ?: 'unknown';
        $obj->event = $ev->data->id ?: '';

        $isPresent = $DB->get_record('bbblog', ['logtime' => $obj->logtime, 'username' => $obj->username]);

        if ($isPresent) return "Already logged";

        $DB->insert_record('bbblog', $obj);
        return "Logged";
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function log_parameters() {
        return new external_function_parameters(
            array(
                'event' => new external_value(PARAM_TEXT, 'The test message', VALUE_DEFAULT, 'No value'),
                'timestamp' => new external_value(PARAM_TEXT, 'The test message', VALUE_DEFAULT, 'No value'),
                'domain' => new external_value(PARAM_TEXT, 'The test message', VALUE_DEFAULT, 'No value'),
            )
        );
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function log_returns() {
        return new external_value(PARAM_TEXT, 'Result message');
    }

}
