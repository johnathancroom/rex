<?php
// rex/system/modules/uri.php

namespace rex\system\modules;

class uri extends \rex\system\skeletons\module {
  private $segments;
  
  public function initialize() {
    if (isset($_SERVER['PATH_INFO']) == true) {
      $uri = trim($_SERVER['PATH_INFO'], '/');
    }
    if (isset($uri) == false) {
      if (isset($_SERVER['REQUEST_URI']) == true) {
        $uri = trim(substr(rtrim(strrev(substr(strrev($_SERVER['REQUEST_URI']), strlen($_SERVER['QUERY_STRING']))), '?'), strlen(rtrim(strrev(substr(strrev($_SERVER['SCRIPT_NAME']), strpos(strrev($_SERVER['SCRIPT_NAME']), '/'))), '/'))), '/');
      }
    }
    $this->segments = (empty($uri) == false) ? explode('/', $uri) : false;
    unset($uri);
    
    return;
  }
  public function exists() {
    if (isset($arguments[0]) == false) {
      return $this->segments;
    }
    if (is_integer($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if (isset($this->segments[$key - 1]) == false) {
      return false;
    }
    return true;
  }
  public function get() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return $this->segments;
    }
    if (is_integer($arguments[0]) == false) {
      return false;
    }
    if ($this->get() == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) == false) {
      return false;
    }
    return $this->segments[$key - 1];
  }
}
?>