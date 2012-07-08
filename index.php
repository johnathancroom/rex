<?php
// index.php

include 'rex/rex.php';
$rex = new rex\handle;
$rex->application->run();

include 'debug.php';
$debug = new debug;
echo '<pre>' . "\n";
$entries = $rex->core->modules->log->get();
if (empty($entries) === false) {
  echo 'LOG' . "\n";
  foreach ($debug->format_array($entries) as $entry) {
    echo $entry . "\n";
  }
  echo "\n";
}
$settings = $rex->core->modules->settings->get();
if (empty($settings) === false) {
  echo 'SETTINGS' . "\n";
  foreach ($settings as $key => $value) {
    $array[] = array('key' => $key, 'value' => $value);
  }
  $settings = $array;
  unset($array);
  foreach ($debug->format_array($settings) as $setting) {
    echo $setting . "\n";
  }
  echo "\n";
}
echo 'REX' . "\n";
var_dump($rex);
echo '</pre>' . "\n";
?>