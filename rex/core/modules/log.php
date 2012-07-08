<?php
// rex/core/modules/log.php

namespace rex\core\modules;

class log extends module {
  protected $entries;
  
  public function _($message) {
    if (is_string($message) === false) {
      return false;
    }
    $arguments = func_get_args();
    if (isset($arguments[1]) === true) {
      if (is_array($arguments[1]) === false) {
        return false;
      }
      foreach ($arguments[1] as $key => $value) {
        switch ($key) {
          case 'time':
            if (is_integer($value) === false) {
              if (is_float($value) === false) {
                return false;
              }
            }
            $time = $value;
            break;
          case 'file':
            if (isset($arguments[1]['line']) === false) {
              return false;
            }
            if (is_string($value) === false) {
              return false;
            }
            if (is_intger($arguments[1]['line']) === false) {
              return false;
            }
            $file = $value;
            $line = $arguments[1]['line'];
            unset($arguments[1]['line']);
            break;
          case 'line':
            if (isset($arguments[1]['file']) === false) {
              return false;
            }
            if (is_integer($value) === false) {
              return false;
            }
            if (is_string($arguments[1]['file']) === false) {
              return false;
            }
            $line = $value;
            $file = $arguments[1]['file'];
            unset($arguments[1]['file']);
            break;
          default:
            return false;
        }
      }
    }
    if (isset($time) === false) {
      $time = microtime(true);
    }
    if (isset($file, $line) === false) {
      $backtrace = debug_backtrace();
      $file = $backtrace[0]['file'];
      $line = $backtrace[0]['line'];
    }
    return $this->add($message, $time, $file, $line);
  }
  public function add($message, $time, $file, $line) {
    if (is_string($message) === false) {
      return false;
    }
    if (is_integer($time) === false) {
      if (is_float($time) === false) {
        return false;
      }
    }
    if (is_string($file) === false) {
      return false;
    }
    if (is_integer($line) === false) {
      return false;
    }
    $this->entries[] = array('message' => $message, 'time' => $time, 'file' => $file, 'line' => $line);
    return true;
  }
  public function get() {
    return $this->entries;
  }
}
?>