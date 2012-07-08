<?php
// rex/application/workspace/workspace.php

namespace rex\application\workspace;

class handle {
  public function __construct($parent, $data = NULL) {
    if (is_array($data) === true) {
      $this->data = $data;
    }
    if (is_object($parent) === true) {
      $this->parent = $parent;
    }
    if (isset($this->parent) === true) {
      $this->rex = $this->parent->parent;
    }
    
    $controllers['path'] = $this->rex->core->modules->settings->get('rex.application.instance.path') . '/' . 'workspace' . '/' . 'controllers';
    include $this->data['path'] . '/' . 'controllers.php';
    $this->controllers = new controllers\handle($this, array('path' => $controllers['path']));
    
    $models['path'] = $this->rex->core->modules->settings->get('rex.application.instance.path') . '/' . 'workspace' . '/' . 'models';
    include $this->data['path'] . '/' . 'models.php';
    $this->models = new models\handle($this, array('path' => $models['path']));
    
    $views['path'] = $this->rex->core->modules->settings->get('rex.application.instance.path') . '/' . 'workspace' . '/' . 'views';
    include $this->data['path'] . '/' . 'views.php';
    $this->views = new views\handle($this, array('path' => $views['path']));
  }
}
?>