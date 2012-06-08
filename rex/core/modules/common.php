<?php
// rex/core/modules/common.php

namespace rex\core\modules;

class common extends \rex\core\skeletons\module {
  public function format_bytes() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_integer($arguments[0]) == false) {
      return false;
    }
    $bytes = $arguments[0];
    if (isset($arguments[1]) == true) {
      if (is_integer($arguments[1]) == true) {
        $precision = $arguments[1];
      }
    }
    if (isset($precision) == false) {
      $precision = 2;
    }
    $kilobyte = 1024;
    $megabyte = 1048576;
    $gigabyte = 1073741824;
    if ($bytes >= $kilobyte && $bytes < $megabyte) {
      return round($bytes / $kilobyte, $precision) . " KB";
    } elseif ($bytes >= $megabyte && $bytes < $gigabyte) {
      return round($bytes / $megabyte, $precision) . " MB";
    } elseif ($bytes >= $gigabyte) {
      return round($bytes / $gigabyte, $precision) . " GB";
    } else {
      return $bytes . " B";
    }
  }
}
?>