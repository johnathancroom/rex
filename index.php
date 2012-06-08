<?php
// index.php
include "rex/rex.php";

$settings['rex.workspace.environment'] = "1";
$settings['rex.system.modules.debug.output'] = "0";
$settings['rex.system.modules.debug.write'] = "1";

$rex = new rex\handle(array('settings' => $settings));

unset($settings);

$rex->workspace->load('controller', 'primary');
?>