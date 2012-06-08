<?php
// workspace/controllers/primary.php

namespace rex\workspace\controllers;

class primary extends \rex\workspace\skeletons\controller {
  public function initialize() {
    $this->rex->system->load('module', 'output');
    
    $this->rex->system->modules['output']->append($this->parent->load('view', 'primary.html'));
    
    return;
  }
}
?>