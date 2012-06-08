<?php
// rex/system/modules/parser.php

namespace rex\system\modules;

class parser extends \rex\system\skeletons\module {
  public function initialize() {
    $settings['delimiter.left'] = "{";
    $settings['delimiter.right'] = "}";
    foreach ($settings as $key => $value) {
      if ($this->rex->core->modules['settings']->exists($key) == false) {
        $this->rex->core->modules['settings']->add('rex.system.modules.parser.' . $key, $value);
      }
    }
    unset($settings);
  }
  public function parse() {
    $arguments = func_get_args();
    foreach (array(0, 1) as $key) {
      if (isset($arguments[$key]) == false) {
        return false;
      }
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    if (is_array($arguments[1]) == false) {
      return false;
    }
    $string = $arguments[0];
    $data = $arguments[1];
    foreach ($data as $key => $value) {
      if (is_array($value) == true) {
        $string = $this->parse_pair($key, $value, $string);
      }
    }
    foreach ($data as $key => $value) {
      if (is_string($value) == true) {
        $string = $this->parse_single($key, $value, $string);
      }
    }
    return $string;
  }
  public function parse_single() {
    $arguments = func_get_args();
    foreach (array(0, 1, 2) as $key) {
      if (isset($arguments[$key]) == false) {
        return false;
      }
    }
    foreach (array() as $key) {
      if (is_string($arguments[$key]) == false) {
        return false;
      }
    }
    $tag = $arguments[0];
    $value = $arguments[1];
    $string = $arguments[2];
    $search = $this->rex->core->modules['settings']->get('rex.system.modules.parser.delimiter.left') . $tag . $this->rex->core->modules['settings']->get('rex.system.modules.parser.delimiter.right');
    $replace = $value;
    $haystack = $string;
    return str_replace($search, $replace, $haystack);
  }
  public function parse_pair () {
    $arguments = func_get_args();
    foreach (array(0, 1, 2) as $key) {
      if (isset($arguments[$key]) == false) {
        return false;
      }
    }
    foreach (array(0, 2) as $key) {
      if (is_string($arguments[$key]) == false) {
        return false;
      }
    }
    if (is_array($arguments[1]) == false) {
      return false;
    }
    $tag = $arguments[0];
    $data = $arguments[1];
    $string = $arguments[2];
    if (preg_match('|' . preg_quote($this->rex->core->modules['settings']->get('rex.system.modules.parser.delimiter.left')) . $tag . preg_quote($this->rex->core->modules['settings']->get('rex.system.modules.parser.delimiter.right')) . '(.+?)' . preg_quote($this->rex->core->modules['settings']->get('rex.system.modules.parser.delimiter.left')) . '/' . $tag . preg_quote($this->rex->core->modules['settings']->get('rex.system.modules.parser.delimiter.right')) . '|s', $string, $matches) == false) {
      return false;
    }
    $wrapper = $matches[0];
    $contents = $matches[1];
    for ($i = 0; $i < count($data); $i++) {
      if (isset($replacement) == false) {
        $replacement = $this->parse($contents, $data[$i]);
        continue;
      }
      $replacement = $replacement . $this->parse($contents, $data[$i]);
    }
    $search = $wrapper;
    $replace = $replacement;
    $haystack = $string;
    return str_replace($search, $replace, $haystack);
  }
}
?>