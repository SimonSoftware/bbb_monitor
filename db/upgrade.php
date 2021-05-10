<?php

defined('MOODLE_INTERNAL') || die;

function xmldb_bbbmonitor_upgrade($oldversion)
{
    global $CFG, $DB, $OUTPUT;
    $dbman = $DB->get_manager();

    if ($oldversion < 2021050909) {
        $table = new xmldb_table('bbbmonitor');
        $field = new xmldb_field('meetingid', XMLDB_TYPE_CHAR, '255');
        $dbman->add_field($table, $field);
    }
    return true;
}