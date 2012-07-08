<?php
// rex/application/workspace/views.php

namespace rex\application\workspace\views;

class handle {
  public function __construct($parent, $data = NULL) {
    if (is_array($data) === true) {
      $this->data = $data;
    }
    if (is_object($parent) === true) {
      $this->parent = $parent;
    }
    if (isset($this->parent) === true) {
      $this->rex = $this->parent->parent->parent;
    }
  }
  public function load($identifier, $data = NULL) {
    if (is_string($identifier) === false) {
      return false;
    }
    if (isset($data) === true) {
      if (is_array($data) === false) {
        return false;
      }
    }
    $file = $this->data['path'] . '/' . $identifier;
    if (file_exists($file) === false) {
      $this->rex->core->modules->log->_('Failed to load view \'' . $identifier . '\' because the file \'' . $file . '\' does not exist');
      return false;
    }
    ob_start();
    include $file;
    $string = ob_get_contents();
    ob_end_clean();
    $this->rex->core->modules->log->_('Loaded view \'' . $identifier . '\'');
    return $string;
  }
}
?>