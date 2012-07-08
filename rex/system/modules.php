<?php
// rex/system/modules.php

namespace rex\system\modules;

class handle {
  public function __construct($parent, $data = NULL) {
    if (is_array($data) === true) {
      $this->data = $data;
    }
    if (is_object($parent) === true) {
      $this->parent = $parent;
    }
    if (isset($this->parent) === true) {
      $this->rex = $this->parent->parent;
    }
  }
  public function exists($identifier) {
    if (is_string($identifier) === false) {
      return false;
    }
    return isset($this->$identifier);
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
    if ($this->exists($identifier) === true) {
      $this->rex->core->modules->log->_('Failed to load module \'' . $identifier . '\' because the identifier is already set');
      return false;
    }
    $file = $this->data['path'] . '/' . $identifier . '.php';
    if (file_exists($file) === false) {
      $this->rex->core->modules->log->_('Failed to load module \'' . $identifier . '\' because the file \'' . $file . '\' does not exist');
      return false;
    }
    include $file;
    $class = __NAMESPACE__ . '\\' . $identifier;
    if (class_exists($class) === false) {
      $this->rex->core->modules->log->_('Failed to load module \'' . $identifier . '\' because the class \'' . $class . '\' is not defined in the file \'' . $file . '\'');
      return false;
    }
    $this->$identifier = new $class($this, $data);
      $this->rex->core->modules->log->_('Loaded module \'' . $identifier . '\'');
    return true;
  }
}
class module {
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