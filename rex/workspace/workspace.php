<?php
// rex/workspace/workspace.php

namespace rex\workspace;

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
    unset($data);
    
    $settings['path'] = realpath($this->rex->data['path'] . '/' . '..' . '/') . "/" . "workspace";
    $settings['environment'] = "0";
    foreach ($settings as $key => $value) {
      if ($this->rex->core->modules['settings']->exists($key) == false) {
        $this->rex->core->modules['settings']->add('rex.workspace.' . $key, $value);
      }
    }
    if (isset($settings) == true) {
      unset($settings);
    }
    
    $this->data['skeletons']['path'] = $this->data['path'] . "/" . "skeletons";
    $this->data['skeletons']['namespace'] = __NAMESPACE__ . "\\" . "skeletons";
    $this->data['controllers']['path'] = $this->rex->core->modules['settings']->get('rex.workspace.path') . "/" . "controllers";
    $this->data['controllers']['namespace'] = __NAMESPACE__ . "\\" . "controllers";
    $this->data['models']['path'] = $this->rex->core->modules['settings']->get('rex.workspace.path') . "/" . "models";
    $this->data['models']['namespace'] = __NAMESPACE__ . "\\" . "models";
    $this->data['views']['path'] = $this->rex->core->modules['settings']->get('rex.workspace.path') . "/" . "views";
    
    switch ((integer) $this->rex->core->modules['settings']->get('rex.workspace.environment')) {
      case 1:
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL | E_STRICT);
        $this->rex->core->modules['log']->_('Environment: development');
        break;
      default:
        ini_set('display_errors', 0);
        ini_set('error_reporting', E_ERROR);
        $this->rex->core->modules['log']->_('Environment: production');
        break;
    }
    
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
      case "controller":
        $identifier = $arguments[1];
        if (isset($this->controllers[$identifier]) == false) {
          return false;
        }
        if (is_object($this->controllers[$identifier]) == false) {
          return false;
        }
        $class = $this->data['controllers']['namespace'] . "\\" . $identifier;
        if (get_class($this->controllers[$identifier]) !== $class) {
          return false;
        }
        return true;
        break;
      case "model":
        $identifier = $arguments[1];
        $class = $this->data['models']['namespace'] . "\\" . $identifier;
        if (class_exists($class) == false) {
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
    $method['time']['start'] = microtime(true);
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
          $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - Skeleton is already loaded', array('type' => 'error'));
          return false;
        }
        $file = $this->data['skeletons']['path'] . "/" . $identifier . ".php";
        if (file_exists($file) == false) {
          $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - File \'' . $file . '\' does not exist', array('type' => 'error'));
          return false;
        }
        include $file;
        $class = $this->data['skeletons']['namespace'] . "\\" . $identifier;
        if (class_exists($class) == false) {
          $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - Class \'' . $class .'\' is not defined in file \'' . $file .'\'', array('type' => 'error'));
          return false;
        }
        $this->rex->core->modules['profile']->_('rex.workspace.skeletons.' . $identifier . '.start', $method['time']['start']);
        $this->rex->core->modules['profile']->_('rex.workspace.skeletons.' . $identifier . '.end');
        $this->rex->core->modules['log']->_('Loaded \'' . $type . '\' \'' . $identifier . '\' in ' . $this->rex->core->modules['profile']->get('rex.workspace.skeletons.' . $identifier . '.start', 'rex.workspace.skeletons.' . $identifier . '.end') . ' seconds', array('type' => 'debug'));
        return true;
        break;
      case "controller":
        $identifier = $arguments[1];
        if ($this->exists($type, $identifier) == true) {
          $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - Controller is already loaded', array('type' => 'error'));
          return false;
        }
        $file = $this->data['controllers']['path'] . "/" . $identifier . ".php";
        if (file_exists($file) == false) {
          $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - File \'' . $file . '\' does not exist', array('type' => 'error'));
          return false;
        }
        if ($this->exists('skeleton', $type) == false) {
          if ($this->load('skeleton', $type) == false) {
            $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - Unable to load the \'' . $type . '\' skeleton', array('type' => 'error'));
            return false;
          }
        }
        include $file;
        $class = $this->data['controllers']['namespace'] . "\\" . $identifier;
        if (class_exists($class) == false) {
          $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - Class \'' . $class . '\' is not defined in file \'' . $file . '\'', array('type' => 'error'));
          return false;
        }
        if (isset($arguments[2]) == true) {
          if (is_array($arguments[2]) == true) {
            $data = $arguments[2];
          }
        }
        if (isset($data) == true) {
          $this->controllers[$identifier] = new $class($this, array('data' => $data));
        } else {
          $this->controllers[$identifier] = new $class($this);
        }
        $this->rex->core->modules['profile']->_('rex.workspace.controllers.' . $identifier . '.start', $method['time']['start']);
        $this->rex->core->modules['profile']->_('rex.workspace.controllers.' . $identifier . '.end');
        $this->rex->core->modules['log']->_('Loaded \'' . $type . '\' \'' . $identifier . '\' in ' . $this->rex->core->modules['profile']->get('rex.workspace.controllers.' . $identifier . '.start', 'rex.workspace.controllers.' . $identifier . '.end') . ' seconds', array('type' => 'debug'));
        return true;
        break;
      case "model":
        $identifier = $arguments[1];
        if ($this->exists($type, $identifier) == false) {
          $file = $this->data['models']['path'] . "/" . $identifier . ".php";
          if (file_exists($file) == false) {
            $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - File \'' . $file . '\' does not exist', array('type' => 'error'));
            return false;
          }
          if ($this->exists('skeleton', $type) == false) {
            if ($this->load('skeleton', $type) == false) {
              $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - Unable to load the \'' . $type . '\' skeleton', array('type' => 'error'));
              return false;
            }
          }
          include $file;
          $class = $this->data['models']['namespace'] . "\\" . $identifier;
          if (class_exists($class) == false) {
            $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - Class \'' . $class . '\' is not defined in file \'' . $file . '\'', array('type' => 'error'));
            return false;
          }
        }
        if (isset($class) == false) {
          $class = $this->data['models']['namespace'] . "\\" . $identifier;
        }
        if (isset($arguments[2]) == true) {
          if (is_array($arguments[2]) == true) {
            $data = $arguments[2];
          }
        }
        if (isset($data) == true) {
          $handle = new $class($this, array('data' => $data));
        } else {
          $handle = new $class($this);
        }
        $this->rex->core->modules['profile']->_('rex.workspace.models.' . $identifier . '.start', $method['time']['start']);
        $this->rex->core->modules['profile']->_('rex.workspace.models.' . $identifier . '.end');
        $this->rex->core->modules['log']->_('Loaded \'' . $type . '\' \'' . $identifier . '\' in ' . $this->rex->core->modules['profile']->get('rex.workspace.models.' . $identifier . '.start', 'rex.workspace.models.' . $identifier . '.end') . ' seconds', array('type' => 'debug'));
        return $handle;
        break;
      case "view":
        $identifier = $arguments[1];
        $file = $this->data['views']['path'] . "/" . $identifier;
        if (file_exists($file) == false) {
          $this->rex->core->modules['log']->_('Unable to load \'' . $type . '\' \'' . $identifier . '\' - File \'' . $file . '\' does not exist', array('type' => 'error'));
          return false;
        }
        ob_start();
        include $file;
        $contents = ob_get_contents();
        ob_end_clean();
        $this->rex->core->modules['profile']->_('rex.workspace.views.' . $identifier . '.start', $method['time']['start']);
        $this->rex->core->modules['profile']->_('rex.workspace.views.' . $identifier . '.end');
        $this->rex->core->modules['log']->_('Loaded \'' . $type . '\' \'' . $identifier . '\' in ' . $this->rex->core->modules['profile']->get('rex.workspace.views.' . $identifier . '.start', 'rex.workspace.views.' . $identifier . '.end') . ' seconds', array('type' => 'debug'));
        return $contents;
        break;
      default:
        break;
    }
  }
  public function __destruct() {
    if ((integer) $this->rex->core->modules['settings']->get('rex.workspace.environment') == 1) {
      if ($this->rex->system->exists('module', 'debug') == false) {
        $this->rex->system->load('module', 'debug');
      }
    }
  }
}
?>