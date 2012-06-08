<?php
// rex/system/modules/input.php

namespace rex\system\modules;

class input extends \rex\system\skeletons\module {
  public function exists() {
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
    $type = $arguments[0];
    switch ($type) {
      case "get":
        $key = $arguments[1];
        if (isset($_GET[$key]) == false) {
          return false;
        }
        return true;
        break;
      case "post":
        $key = $arguments[1];
        if (isset($_POST[$key]) == false) {
          return false;
        }
        return true;
        break;
      case "cookie":
        $key = $arguments[1];
        if (isset($_COOKIE[$key]) == false) {
          return false;
        }
        return true;
        break;
      default:
        return false;
        break;
    }
  }
  public function get() {
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
    $type = $arguments[0];
    switch ($type) {
      case "get":
        $key = $arguments[1];
        if ($this->exists($type, $key) == false) {
          return false;
        }
        return $_GET[$key];
        break;
      case "post":
        $key = $arguments[1];
        if ($this->exists($type, $key) == false) {
          return false;
        }
        return $_POST[$key];
        break;
      case "cookie":
        $key = $arguments[1];
        if ($this->exists($type, $key) == false) {
          return false;
        }
        return $_COOKIE[$key];
        break;
      default:
        return false;
        break;
    }
  }
}
?>