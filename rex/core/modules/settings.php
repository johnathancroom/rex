<?php
// rex/core/modules/settings.php

namespace rex\core\modules;

class settings extends module {
  protected $settings;
  
  public function exists($key) {
    if (is_string($key) === false) {
      return false;
    }
    return isset($this->settings[$key]);
  }
  public function add($key, $value) {
    if (is_string($key) === false) {
      return false;
    }
    if (is_string($value) === false) {
      return false;
    }
    if ($this->exists($key) === true) {
      return false;
    }
    $this->settings[$key] = $value;
    return true;
  }
  public function modify($key, $value) {
    if (is_string($key) === false) {
      return false;
    }
    if (is_string($value) === false) {
      return false;
    }
    if ($this->exists($key) === false) {
      return false;
    }
    $this->settings[$key] = $value;
    return true;
  }
  public function remove($key) {
    if (is_string($key) === false) {
      return false;
    }
    if ($this->exists($key) === false) {
      return false;
    }
    unset($this->settings[$key]);
    return true;
  }
  public function get() {
    $arguments = func_get_args();
    if (isset($arguments[0]) === false) {
      return $this->settings;
    }
    if (is_string($arguments[0]) === false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) === false) {
      return false;
    }
    return $this->settings[$key];
  }
}
?>