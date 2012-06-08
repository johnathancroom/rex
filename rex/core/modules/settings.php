<?php
// rex/core/modules/settings.php

namespace rex\core\modules;

class settings extends \rex\core\skeletons\module {
  private $settings;
  
  public function exists() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if (isset($this->settings[$key]) == false) {
      return false;
    }
    return true;
  }
  public function add() {
    $arguments = func_get_args();
    foreach (array(0, 1) as $key) {
      if (isset($arguments[$key]) == false) {
        return false;
      }
    }
    foreach (array(0, 1) as $key) {
      if (is_string($arguments[$key]) == false) {
        return false;
      }
    }
    $key = $arguments[0];
    if ($this->exists($key) == true) {
      return false;
    }
    $value = $arguments[1];
    $this->settings[$key] = $value;
    return true;
  }
  public function modify() {
    $arguments = func_get_args();
    foreach (array(0, 1) as $key) {
      if (isset($arguments[$key]) == false) {
        return false;
      }
    }
    foreach (array(0, 1) as $key) {
      if (is_string($arguments[$key]) == false) {
        return false;
      }
    }
    $key = $arguments[0];
    if ($this->exists($key) == false) {
      return false;
    }
    $value = $arguments[1];
    $this->settings[$key] = $value;
    return true;
  }
  public function remove() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) == false) {
      return false;
    }
    unset($this->settings[$key]);
    return true;
  }
  public function get() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return $this->settings;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) == false) {
      return false;
    }
    return $this->settings[$key];
  }
}
?>