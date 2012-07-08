<?php
// rex/core/modules/common.php

namespace rex\core\modules;

class common extends module {
  public function initialize() {
    $this->array['divide'] = function ($array) {
      foreach ($array as $key => $value) {
        $keys[] = $key;
        $values[] = $value;
      }
      return array($keys, $values);
    };
  }
}
?>