<?php
// rex/system/modules/debug.php

namespace rex\system\modules;

class debug extends \rex\system\skeletons\module {
  public function initialize() {
    $settings['output'] = "1";
    $settings['write'] = "0";
    $settings['file'] = realpath($this->rex->data['path'] . '/' . '..' . '/') . "/" . "debug.txt";
    foreach ($settings as $key => $value) {
      if ($this->rex->core->modules['settings']->exists($key) == false) {
        $this->rex->core->modules['settings']->add('rex.system.modules.debug.' . $key, $value);
      }
    }
    unset($settings);
    
    if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.debug.output') == 1) {
      $this->output();
    }
    if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.debug.write') == 1) {
      $this->write();
    }
    
    return;
  }
  public function run() {
    $arguments = func_get_args();
    ob_start();
    $this->rex->core->modules['profile']->_('rex.end');
    echo "LOG ENTRIES START\n";
    echo "\n";
    $entries = $this->rex->core->modules['log']->get();
    $entries = $this->format_array($entries, ' | ');
    foreach ($entries as $entry) {
      echo "| " . $entry . " |\n";
    }
    echo "\n";
    echo "LOG ENTRIES END\n";
    echo "\n";
    echo "\n";
    echo "STATISTICS START\n";
    echo "\n";
    $statistics[] = array('profile.rex', $this->rex->core->modules['profile']->get('rex.start', 'rex.end') . ' s');
    $statistics[] = array('profile.memory', $this->rex->core->modules['common']->format_bytes(memory_get_usage()));
    $statistics[] = array('profile.memory.peak', $this->rex->core->modules['common']->format_bytes(memory_get_peak_usage()));
    $statistics = $this->format_array($statistics);
    foreach ($statistics as $statistic) {
      echo $statistic . "\n";
    }
    echo "\n";
    echo "STATISTICS END\n";
    echo "\n";
    echo "\n";
    echo "SETTINGS START\n";
    echo "\n";
    $settings = $this->rex->core->modules['settings']->get();
    if (isset($array) == true) {
      unset($array);
    }
    foreach ($settings as $key => $value) {
      $array[] = array($key, $value);
    }
    $settings = $this->format_array($array);
    foreach ($settings as $setting) {
      echo $setting . "\n";
    }
    echo "\n";
    echo "SETTINGS END\n";
    $contents = ob_get_contents();
    ob_end_clean();
    if (isset($arguments[0]) == true) {
      if (is_bool($arguments[0]) == true) {
        $return = $arguments[0];
      }
    }
    if (isset($return) == false) {
      $return = false;
    }
    if ($return == true) {
      return $contents;
    }
    print $contents;
    return;
  }
  public function output() {
    if ($this->parent->exists('module', 'output') == true) {
      if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.output.flush') == 0) {
        ob_start();
        echo "<pre>\n";
        $this->run();
        echo "</pre>\n";
        $contents = ob_get_contents();
        ob_end_clean();
        $this->parent->modules['output']->append($contents);
        return;
      }
    }
    echo "<pre>\n";
    $this->run();
    echo "</pre>\n";
    return;
  }
  public function write() {
    $file = $this->rex->core->modules['settings']->get('rex.system.modules.debug.file');
    if (file_exists($file) == false) {
      return false;
    }
    if (is_writable($file) == false) {
      return false;
    }
    if (($handle = fopen($file, 'w')) == false) {
      return false;
    }
    $contents = $this->run(true);
    if (fwrite($handle, $contents) == false) {
      return false;
    }
    fclose($handle);
    return true;
  }
  public function format_array() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_array($arguments[0]) == false) {
      return false;
    }
    $array = $arguments[0];
    foreach ($array as $key => $value) {
      foreach (array_keys($value) as $column) {
        if (isset($columns) == false) {
          $columns[] = $column;
          continue;
        }
        if (in_array($column, $columns) == false) {
          $columns[] = $column;
        }
      }
    }
    foreach ($array as $key => $value) {
      foreach ($columns as $column) {
        if (isset($value[$column]) == true) {
          $lengths[$column][] = strlen($value[$column]);
        }
      }
    }
    foreach ($lengths as $column => $data) {
      $lengths[$column] = max($data);
    }
    if (isset($arguments[1]) == true) {
      if (is_string($arguments[1]) == true) {
        $separator = $arguments[1];
      }
    }
    if (isset($separator) == false) {
      $separator = " ";
    }
    foreach ($array as $key => $value) {
      foreach ($columns as $column) {
        if (isset($value[$column]) == true) {
          $column_string = str_pad($value[$column], $lengths[$column]) . $separator;
          $row_string = (isset($row_string) == true) ? $row_string . $column_string : $column_string;
        }
      }
      $array[$key] = strrev(substr(strrev($row_string), strlen($separator)));
      if (isset($row_string) == true) {
        unset($row_string);
      }
    }
    return $array;
  }
}
?>