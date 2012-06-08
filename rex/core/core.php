<?php
// rex/core/core.php

namespace rex\core;

class handle {
  public $parent;
  public $rex;
  public $data;
  
  public function __construct() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == true) {
      if (is_object($arguments[0]) == true) {
        $parent = $arguments[0];
        $this->parent = $parent;
        $this->rex = $this->parent;
      }
    }
    if (isset($parnet) == true) {
      unset($parent);
    }
    if (isset($arguments[1]) == true) {
      if (is_array($arguments[1]) == true) {
        $transport = $arguments[1];
        if (isset($transport['data']) == true) {
          if (is_array($transport['data']) == true) {
            $data = $transport['data'];
            $this->data = $data;
          }
        }
      }
    }
    if (isset($data) == true) {
      unset($data);
    }
    
    $this->data['skeletons']['path'] = $this->data['path'] . "/" . "skeletons";
    $this->data['skeletons']['namespace'] = __NAMESPACE__ . "\\" . "skeletons";
    $this->data['modules']['path'] = $this->data['path'] . "/" . "modules";
    $this->data['modules']['namespace'] = __NAMESPACE__ . "\\" . "modules";
    
    $this->load('module', 'common');
    $this->load('module', 'settings');
    $this->load('module', 'log');
    $this->load('module', 'profile');
    
    return;
  }
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
      case "skeleton":
        $identifier = $arguments[1];
        $class = $this->data['skeletons']['namespace'] . "\\" . $identifier;
        if (class_exists($class) == false) {
          return false;
        }
        return true;
        break;
      case "module":
        $identifier = $arguments[1];
        if (isset($this->modules[$identifier]) == false) {
          return false;
        }
        if (is_object($this->modules[$identifier]) == false) {
          return false;
        }
        $class = $this->data['modules']['namespace'] . "\\" . $identifier;
        if (get_class($this->modules[$identifier]) !== $class) {
          return false;
        }
        return true;
        break;
      default:
        return false;
        break;
    }
  }
  public function load() {
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
      case "skeleton":
        $identifier = $arguments[1];
        if ($this->exists($type, $identifier) == true) {
          return false;
        }
        $file = $this->data['skeletons']['path'] . "/" . $identifier . ".php";
        if (file_exists($file) == false) {
          return false;
        }
        include $file;
        $class = $this->data['skeletons']['namespace'] . "\\" . $identifier;
        if (class_exists($class) == false) {
          return false;
        }
        return true;
        break;
      case "module":
        $identifier = $arguments[1];
        if ($this->exists($type, $identifier) == true) {
          return false;
        }
        $file = $this->data['modules']['path'] . "/" . $identifier . ".php";
        if (file_exists($file) == false) {
          return false;
        }
        if ($this->exists('skeleton', $type) == false) {
          if ($this->load('skeleton', $type) == false) {
            return false;
          }
        }
        include $file;
        $class = $this->data['modules']['namespace'] . "\\" . $identifier;
        if (class_exists($class) == false) {
          return false;
        }
        if (isset($arguments[2]) == true) {
          if (is_array($arguments[2]) == true) {
            $data = $arguments[2];
          }
        }
        if (isset($data) == true) {
          $this->modules[$identifier] = new $class($this, array('data' => $data));
        } else {
          $this->modules[$identifier] = new $class($this);
        }
        return true;
        break;
      default:
        break;
    }
  }
}
?>