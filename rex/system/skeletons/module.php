<?php
// rex/system/skeletons/module.php

namespace rex\system\skeletons;

class module {
  public $parent;
  public $rex;
  public $data;
  
  public function __construct() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == true) {
      if (is_object($arguments[0]) == true) {
        $this->parent = $arguments[0];
        $this->rex = $this->parent->parent;
      }
    }
    if (isset($arguments[1]) == true) {
      if (is_array($arguments[1]) == true) {
        if (isset($arguments[1]['data']) == true) {
          if (is_array($arguments[1]['data']) == true) {
            $this->data = $arguments[1]['data'];
          }
        }
      }
    }
    
    if (method_exists($this, 'initialize') == true) {
      $this->initialize();
    }
    
    return;
  }
  public function __destruct() {
    if (method_exists($this, 'deinitialize') == true) {
      $this->deinitialize();
    }
    
    return;
  }
}
?>