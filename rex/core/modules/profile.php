<?php
// rex/core/modules/profile.php

namespace rex\core\modules;

class profile extends \rex\core\skeletons\module {
  private $points;
  
  public function initialize() {
    if (defined('REX_PROFILE_START') == true) {
      if (is_float(REX_PROFILE_START) == true) {
        $this->add('rex.start', REX_PROFILE_START);
      }
    }
    if ($this->exists('rex.start') == false) {
      $this->add('rex.start', microtime(true));
    }
    
    return;
  }
  public function _() {
    $method['time']['start'] = microtime(true);
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if (isset($arguments[1]) == true) {
      if (is_float($arguments[1]) == true) {
        $time = $arguments[1];
      }
    }
    if (isset($time) == false) {
      $time = $method['time']['start'];
    }
    if ($this->add($key, $time) == false) {
      return false;
    }
    return true;
  }
  public function exists() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if (isset($this->points[$key]) == false) {
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
    if (is_string($arguments[0]) == false) {
      return false;
    }
    if (is_float($arguments[1]) == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) == true) {
      return false;
    }
    $time = $arguments[1];
    $this->points[$key] = $time;
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
    unset($this->points[$key]);
    return true;
  }
  public function get() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return $this->points;
    }
    if (isset($arguments[1]) == false) {
      if (is_string($arguments[0]) == false) {
        return false;
      }
      $key = $arguments[0];
      if ($this->exists($key) == false) {
        return false;
      }
      return $this->points[$key];
    }
    foreach (array(0, 1) as $key) {
      if (is_string($arguments[$key]) == false) {
        return false;
      }
    }
    $start = $arguments[0];
    $end = $arguments[1];
    foreach (array($start, $end) as $key) {
      if ($this->exists($key) == false) {
        return false;
      }
    }
    $start = $this->get($start);
    $end = $this->get($end);
    return round(($end) - ($start), 4);
  }
}
?>