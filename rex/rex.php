<?php
// rex/rex.php

namespace rex;

class handle {
  public function __construct() {
    if (version_compare(PHP_VERSION, '5.3', '>=') === false) {
      echo 'PHP must be version 5.3 or greater';
      exit;
    }
    
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL | E_STRICT);
    
    $this->data['path'] = __DIR__;
    
    $core['path'] = $this->data['path'] . '/' . 'core';
    include $core['path'] . '/' . 'core.php';
    $this->core = new core\handle($this, array('path' => $core['path']));
    
    $system['path'] = $this->data['path'] . '/' . 'system';
    include $system['path'] . '/' . 'system.php';
    $this->system = new system\handle($this, array('path' => $system['path']));
    
    $application['path'] = $this->data['path'] . '/' . 'application';
    include $application['path'] . '/' . 'application.php';
    $this->application = new application\handle($this, array('path' => $application['path']));
  }
}
?>