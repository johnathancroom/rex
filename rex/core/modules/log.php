<?php
// rex/core/modules/log.php

namespace rex\core\modules;

class log extends \rex\core\skeletons\module {
  private $entries;
  
  public function _() {
    $method['time']['start'] = microtime(true);
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $message = $arguments[0];
    $time = $method['time']['start'];
    if (isset($arguments[1]) == true) {
      if (is_array($arguments[1]) == true) {
        if (isset($arguments[1]['type']) == true) {
          if (is_string($arguments[1]['type']) == true) {
            $type = $arguments[1]['type'];
          }
        }
        if (isset($arguments[1]['file']) == true) {
          if (is_string($arguments[1]['file']) == true) {
            $file = $arguments[1]['file'];
          }
        }
        if (isset($arguments[1]['line']) == true) {
          if (is_integer($arguments[1]['line']) == true) {
            $line = $arguments[1]['line'];
          }
        }
      }
    }
    if (isset($type) == false) {
      $type = "debug";
    }
    $backtrace = debug_backtrace();
    if (isset($file) == false) {
      $file = $backtrace[0]['file'];
    }
    if (isset($line) == false) {
      $line = $backtrace[0]['line'];
    }
    if ($this->add($type, $message, $time, $file, $line) == false) {
      return false;
    }
    return true;
  }
  public function exists() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_integer($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if (isset($this->entries[$key]) == false) {
      return false;
    }
    return true;
  }
  public function add() {
    $arguments = func_get_args();
    foreach (array(0, 1, 2, 3, 4) as $key) {
      if (isset($arguments[$key]) == false) {
        return false;
      }
    }
    foreach (array(0, 1, 3) as $key) {
      if (is_string($arguments[$key]) == false) {
        return false;
      }
    }
    if (is_float($arguments[2]) == false) {
      return false;
    }
    if (is_integer($arguments[4]) == false) {
      return false;
    }
    $type = $arguments[0];
    if (in_array($type, array('debug', 'error', 'fatal')) == false) {
      return false;
    }
    $message = $arguments[1];
    $time = $arguments[2];
    $file = $arguments[3];
    $line = $arguments[4];
    $this->entries[] = array('type' => $type, 'message' => $message, 'time' => $time, 'file' => $file, 'line' => $line);
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
    unset($this->entries[$key]);
    return true;
  }
  public function get() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return $this->entries;
    }
    if (is_integer($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) == false) {
      return false;
    }
    return $this->entries[$key];
  }
}
?>