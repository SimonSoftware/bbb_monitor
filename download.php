<?php

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
global $DB;

$dataformat = optional_param('dataformat', '', PARAM_ALPHA);
$meetingid = optional_param('meetingid', '', PARAM_TEXT);

$columns = $DB->get_columns('bbblog');
$cols = [];
foreach ($columns as $column) {
    $cols[$column->name] = $column->name;
}
$logs = $DB->get_records('bbblog', ['webinar_id' => $meetingid]);

\core\dataformat::download_data("bbblog", $dataformat, $cols, $logs);
