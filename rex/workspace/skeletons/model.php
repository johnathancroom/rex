<?php
// rex/workspace/skeletons/model.php

namespace rex\workspace\skeletons;

class model {
  public $parent;
  public $rex;
  public $data;
  
  public function __construct() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == true) {
      if (is_object($arguments[0]) == true) {
        $parent = $arguments[0];
        $this->parent = $parent;
        $this->rex = $this->parent->parent;
      }
    }
    if (isset($parnet) == true) {
      unset($parent);
    }
    if (isset($arguments[1]) == true) {
      if (is_array($arguments[1]) == true) {
        $transport = $arguments[1];
        if (isset($transport['data']) == true) {
          if (is_array($transport['data']) == true) {
            $data = $transport['data'];
            $this->data = $data;
          }
        }
      }
    }
    if (isset($data) == true) {
      unset($data);
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