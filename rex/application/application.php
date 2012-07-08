<?php
// rex/application/application.php

namespace rex\application;

class handle {
  public function __construct($parent, $data = NULL) {
    if (is_array($data) === true) {
      $this->data = $data;
    }
    if (is_object($parent) === true) {
      $this->parent = $parent;
    }
    if (isset($this->parent) === true) {
      $this->rex = $this->parent;
    }
    
    $settings['instance.path'] = realpath($this->rex->data['path'] . '/' . '../') . '/' . 'application';
    $settings['instance.environment'] = 'development';
    foreach ($settings as $key => $value) {
      $key = 'rex.application.' . $key;
      if ($this->rex->core->modules->settings->exists($key) === false) {
        $this->rex->core->modules->settings->add($key, $value);
      }
    }
    
    $workspace['path'] = $this->data['path'] . '/' . 'workspace';
    include $workspace['path'] . '/' . 'workspace.php';
    $this->workspace = new workspace\handle($this, array('path' => $workspace['path']));
  }
  public function run() {  
    switch ($this->rex->core->modules->settings->get('rex.application.instance.environment')) {
      case 'production':
        ini_set('display_errors', 0);
        ini_set('error_reporting', E_ALL | E_STRICT);
        break;
      default:
        ini_set('display_errors', 1);
        ini_set('error_reporting', E_ALL | E_STRICT);
        break;
    }
    
    if (file_exists($this->rex->core->modules->settings->get('rex.application.instance.path')) === true) {
      $file = $this->rex->core->modules->settings->get('rex.application.instance.path') . '/' . 'routes.php';
      if (file_exists($file) === true) {
        if ($this->rex->system->modules->exists('router') === false) {
          $this->rex->system->modules->load('router');
        }
        if ($this->rex->system->modules->exists('router') === true) {
          include $file;
          $this->rex->system->modules->router->route();
        }
      }
    }
  }
}
?>