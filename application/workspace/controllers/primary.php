<?php
// application/workspace/controllers/primary.php

namespace rex\application\workspace\controllers;

class primary extends controller {
  public function initialize() {
    print $this->rex->application->workspace->views->load('primary.html');
  }
}
?>