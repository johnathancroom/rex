<?php
// rex/core/core.php

namespace rex\core;

class handle {
  public function __construct($parent, $data = NULL) {
    if (is_array($data) === true) {
      $this->data = $data;
    }
    if (is_object($parent) === true) {
      $this->parent = $parent;
    }
    if (isset($this->parent) === true) {
      $this->rex = $this->parent;
    }
    
    $modules['path'] = $this->data['path'] . '/' . 'modules';
    include realpath($modules['path'] . '/' . '../') . '/' . 'modules.php';
    $this->modules = new modules\handle($this, array('path' => $modules['path']));
    
    if (($handle = opendir($modules['path'])) !== false) {
      while(($file = readdir($handle)) !== false) {
        if (in_array($file, array('.', '..')) === true) {
          continue;
        }
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
          continue;
        }
        if ($this->modules->exists(pathinfo($file, PATHINFO_FILENAME)) === true) {
          continue;
        }
        $this->modules->load(pathinfo($file, PATHINFO_FILENAME));
      }
    }
  }
}
?>