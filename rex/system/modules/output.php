<?php
// rex/system/modules/output.php

namespace rex\system\modules;

class output extends \rex\system\skeletons\module {
  private $contents;
  
  public function initialize() {
    $settings['start.auto'] = "1";
    $settings['end.auto'] = "1";
    $settings['flush'] = "0";
    foreach ($settings as $key => $value) {
      if ($this->rex->core->modules['settings']->exists($key) == false) {
        $this->rex->core->modules['settings']->add('rex.system.modules.output.' . $key, $value);
      }
    }
    unset($settings);
    
    if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.output.start.auto') == 1) {
      $this->start();
    }
    
    return;
  }
  public function deinitialize() {
    if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.output.end.auto') == 1) {
      $this->end();
      print $this->get();
      exit;
    }
    
    return;
  }
  public function start() {
    ob_start();
    $this->rex->core->modules['log']->_('Started output buffering');
    return;
  }
  public function end() {
    $arguments = func_get_args();
    if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.output.flush') == 1) {
      $results = true;
    } else {
      $results = false;
    }
    if (isset($arguments[0]) == true) {
      if (is_bool($arguments[0]) == true) {
        $flush = $arguments[0];
        if ($flush == true) {
          $results = true;
        } else {
          $results = false;
        }
      }
    }
    if (isset($flush) == true) {
      unset($flush);
    }
    if ($results == true) {
      ob_end_flush();
      $this->rex->core->modules['log']->_('Ended output buffering and flushed buffer contents');
      exit;
    } else {
      ob_end_clean();
      $this->rex->core->modules['log']->_('Ended output buffering and cleaned buffer contents');
      print $this->get();
      exit;
    }
    return true;
  }
  public function prepend() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $string = $arguments[0];
    if (isset($this->contents) == true) {
      $this->contents = $string . $this->contents;
    } else {
      $this->set($string);
    }
    return true;
  }
  public function append() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $string = $arguments[0];
    if (isset($this->contents) == true) {
      $this->contents = $this->contents . $string;
    } else {
      $this->set($string);
    }
    return true;
  }
  public function set() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $string = $arguments[0];
    $this->contents = $string;
    return true;
  }
  public function clear() {
    $this->contents = NULL;
    return;
  }
  public function get() {
    if (isset($this->contents) == false) {
      return NULL;
    }
    return $this->contents;
  }
}
?>