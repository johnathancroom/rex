<?php
// rex/system/modules/mysql.php

namespace rex\system\modules;

class mysql extends \rex\system\skeletons\module {
  private $connections;
  
  public function exists() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if (isset($this->connections[$key]) == false) {
      return false;
    }
    return true;
  }
  public function add() {
    $arguments = func_get_args();
    foreach (array(0, 1, 2, 3, 4) as $key) {
      if (isset($arguments[$key]) == false) {
        return false;
      }
    }
    foreach (array(0, 1, 2, 3, 4) as $key) {
      if (is_string($arguments[$key]) == false) {
        return false;
      }
    }
    $key = $arguments[0];
    if ($this->exists($key) == true) {
      $this->rex->core->modules['log']->_('Unable to add connection \'' . $key . '\' - Connection already exists');
      return false;
    }
    $server = $arguments[1];
    $username = $arguments[2];
    $password = $arguments[3];
    if (($handle = mysql_connect($server, $username, $password)) == false) {
      $this->rex->core->modules['log']->_('Unable to add connection \'' . $key . '\' - Unable to connect to server \'' . $server . '\'');
      return false;
    }
    $database = $arguments[4];
    if ((mysql_select_db($database, $handle)) == false) {
      $this->rex->core->modules['log']->_('Unable to add connection \'' . $key . '\' - Unable to select database \'' . $database . '\'');
      return false;
    }
    $this->connections[$key] = $handle;
    $this->rex->core->modules['log']->_('Added connection \'' . $key . '\' (\'' . $database . '\' on \'' . $server . '\')');
    return true;
  }
  public function remove() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) == false) {
      $this->rex->core->modules['log']->_('Unable to remove connection \'' . $key . '\' - Connection does not exist');
      return false;
    }
    unset($this->connections[$key]);
    $this->rex->core->modules['log']->_('Removed connection \'' . $key . '\'');
    return true;
  }
  public function get() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      return false;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) == false) {
      return false;
    }
    return $this->connections[$key];
  }
  public function query() {
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
    $sql = $arguments[0];
    $key = $arguments[1];
    if ($this->exists($key) == false) {
      $this->rex->core->modules['log']->_('Unable to execute query \'' . $sql . '\' on connection \'' . $key . '\' - Connection does not exist');
      return false;
    }
    if (($handle = mysql_query($sql, $this->get($key))) == false) {
      $this->rex->core->modules['log']->_('Unable to execute query \'' . $sql . '\' on connection \'' . $key . '\' - Unable to execute query');
      return false;
    }
    $this->rex->core->modules['log']->_('Executed query \'' . $sql . '\' on connection \'' . $key . '\'');
    return $handle;
  }
}
?>