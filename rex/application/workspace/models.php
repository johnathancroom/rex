<?php
// rex/application/workspace/models.php

namespace rex\application\workspace\models;

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
  public function exists($identifier) {
    if (is_string($identifier) === false) {
      return false;
    }
    $class = __NAMESPACE__ . '\\' . $identifier;
    return class_exists($class);
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
    $class = __NAMESPACE__ . '\\' . $identifier;
    if ($this->exists($identifier) === false) {
      $file = $this->data['path'] . '/' . $identifier . '.php';
      if (file_exists($file) === false) {
        $this->rex->core->modules->log->_('Failed to load model \'' . $identifier . '\' because the file \'' . $file . '\' does not exist');
        return false;
      }
      include $file;
      if (class_exists($class) === false) {
        $this->rex->core->modules->log->_('Failed to load model \'' . $identifier . '\' because the class \'' . $class . '\' is not defined in the file \'' . $file . '\'');
        return false;
      }
    }
    $handle = new $class($this, $data);
    $this->rex->core->modules->log->_('Loaded model \'' . $identifier . '\'');
    return $handle;
  }
}
class model {
  public function __construct($parent, $data = NULL) {
    if (is_array($data) === true) {
      $this->data = $data;
    }
    if (is_object($parent) === true) {
      $this->parent = $parent;
    }
    if (isset($this->parent) === true) {
      $this->rex = $this->parent->parent->parent->parent;
    }
    
    if (method_exists($this, 'initialize') === true) {
      $this->initialize();
    }
  }
  public function __destruct() {
    if (method_exists($this, 'deinitialize') === true) {
      $this->deinitialize();
    }
  }
}
?>