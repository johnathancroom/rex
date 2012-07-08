<?php
// rex/system/system.php

namespace rex\system;

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
    
    $extensions['path'] = $this->data['path'] . '/' . 'extensions';
    include realpath($extensions['path'] . '/' . '../') . '/' . 'extensions.php';
    $this->extensions = new extensions\handle($this, array('path' => $extensions['path']));
  }
}
?>