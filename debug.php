<?php
// debug.php

class debug {
  public function format_array($array) {
    if (is_array($array) == false) {
      return false;
    }
    foreach ($array as $row) {
      if (is_array($row) == false) {
        return false;
      }
    }
    foreach ($array as $row) {
      foreach (array_keys($row) as $column) {
        if (isset($columns) == false) {
          $columns[] = $column;
          continue;
        }
        if (in_array($column, $columns) == false) {
          $columns[] = $column;
        }
      }
    }
    foreach ($array as $row) {
      foreach ($columns as $column) {
        if (isset($row[$column]) == false) {
          continue;
        }
        $lengths[$column][] = strlen($row[$column]);
      }
    }
    foreach ($lengths as $column => $data) {
      $lengths[$column] = max($data);
    }
    foreach ($array as $key => $value) {
      foreach ($columns as $column) {
        $string['column'] = str_pad($value[$column], $lengths[$column]) . ' ';
        if (isset($string['row']) == false) {
          $string['row'] = $string['column'];
          continue;
        }
        $string['row'] = $string['row'] . $string['column'];
      }
      $value = substr($string['row'], 0, -1);
      unset($string);
      $array[$key] = $value;
    }
    return $array;
  }
}
?>