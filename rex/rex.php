<?php
// rex/rex.php

namespace rex;

class handle {
  public $data;
  
  public function __construct() {
    if (version_compare(PHP_VERSION, '5.3.0', '>=') == false) {
      echo "PHP must be version 5.3.0 or greater";
    }
    
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL | E_STRICT);
    
    $arguments = func_get_args();
    
    $this->data['path'] = __DIR__;
    $this->data['core']['path'] = $this->data['path'] . "/" . "core";
    $this->data['system']['path'] = $this->data['path'] . "/" . "system";
    $this->data['workspace']['path'] = $this->data['path'] . "/" . "workspace";
    
    include $this->data['core']['path'] . "/" . "core.php";
    $this->core = new core\handle($this, array('data' => array('path' => $this->data['core']['path'])));
    
    if (isset($arguments[0]) == true) {
      if (is_array($arguments[0]) == true) {
        $transport = $arguments[0];
        if (isset($transport['settings']) == true) {
          if (is_array($transport['settings']) == true) {
            $settings = $transport['settings'];
            foreach ($settings as $key => $value) {
              if ($this->core->modules['settings']->exists($key) == false) {
                $this->core->modules['settings']->add($key, $value);
              }
            }
          }
        }
      }
    }
    if (isset($settings) == true) {
      unset($settings);
    }
    
    include $this->data['system']['path'] . "/" . "system.php";
    $this->system = new system\handle($this, array('data' => array('path' => $this->data['system']['path'])));
    
    include $this->data['workspace']['path'] . "/" . "workspace.php";
    $this->workspace = new workspace\handle($this, array('data' => array('path' => $this->data['workspace']['path'])));
    
    return;
  }
}
?>