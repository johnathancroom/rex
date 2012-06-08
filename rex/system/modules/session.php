<?php
// rex/system/modules/session.php

namespace rex\system\modules;

class session extends \rex\system\skeletons\module {
  public function initialize() {
    if ($this->parent->exists('module', 'input') == false) {
      $this->parent->load('module', 'input');
    }
    
    $settings['refresh.rate'] = "300";
    $settings['refresh.auto'] = "1";
    $settings['cookie.prefix'] = "";
    $settings['cookie.name'] = (($this->rex->core->modules['settings']->exists('rex.system.modules.session.cookie.prefix') == true) ? $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.prefix') : $settings['cookie.prefix']) . "session";
    $settings['cookie.expires'] = "2592000";
    $settings['cookie.path'] = "";
    $settings['cookie.domain'] = "";
    $settings['database.sync'] = "0";
    $settings['mysql.server'] = "";
    $settings['mysql.username'] = "";
    $settings['mysql.password'] = "";
    $settings['mysql.database'] = "";
    foreach ($settings as $key => $value) {
      if ($this->rex->core->modules['settings']->exists($key) == false) {
        $this->rex->core->modules['settings']->add('rex.system.modules.session.' . $key, $value);
      }
    }
    unset($settings);
    
    if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.session.refresh.auto') == 1) {
      $this->process();
    }
    
    return;
  }
  public function process() {
    if ($this->exists() == false) {
      $this->set();
      return;
    }
    if ($this->valid() == false) {
      $this->remove();
      return;
    }
    if ($this->get('time') + (integer) $this->rex->core->modules['settings']->get('rex.system.modules.session.refresh.rate') > microtime(true)) {
      $this->rex->core->modules['log']->_('Pending session update in ' . round($this->get('time') + (integer) $this->rex->core->modules['settings']->get('rex.system.modules.session.refresh.rate') - microtime(true), 4) . ' seconds');
      return;
    }
    $this->set($this->get());
    return;
  }
  public function exists() {
    $arguments = func_get_args();
    if (isset($arguments[0]) == false) {
      if ($this->parent->modules['input']->exists('cookie', $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.name')) == false) {
        return false;
      }
      return true;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists() == false) {
      return false;
    }
    $cookie['name'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.name');
    $session = unserialize(base64_decode($this->parent->modules['input']->get('cookie', $cookie['name'])));
    if (isset($session[$key]) == false) {
      return false;
    }
    return true;
  }
  public function valid() {
    if ($this->exists() == false) {
      $this->rex->core->modules['log']->_('Session is not valid - Session does not exist');
      return false;
    }
    if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.session.database.sync') == 1) {
      if ($this->parent->exists('module', 'mysql') == false) {
        $this->parent->load('module', 'mysql');
      }
      if ($this->parent->modules['mysql']->exists('session') == false) {
        $mysql['server'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.mysql.server');
        $mysql['username'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.mysql.username');
        $mysql['password'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.mysql.password');
        $mysql['database'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.mysql.database');
        if ($this->parent->modules['mysql']->add('session', $mysql['server'], $mysql['username'], $mysql['password'], $mysql['database']) == false) {
          $this->rex->core->modules['log']->_('Unable to validate session - Unable to add mysql connection');
          return false;
        }
      }
      if (($query = $this->parent->modules['mysql']->query('SELECT * FROM `sessions` WHERE `id` = \'' . $this->get('id') . '\';', 'session')) == false) {
        $this->rex->core->modules['log']->_('Unable to validate session - Unable to execute mysql query');
        return false;
      }
      if (mysql_num_rows($query) == 0) {
        $this->rex->core->modules['log']->_('Session is not valid - Database record does not exist');
        return false;
      }
      $row = mysql_fetch_array($query);
      if ($row['expires'] < time()) {
        $this->rex->core->modules['log']->_('Session is not valid - Database record has expired (\'' . (float) $row['exires'] . '\', \'' . time() . '\')');
        $this->parent->modules['mysql']->query('DELETE FROM `sessions` WHERE `id` = "' . $session['id'] . '";', 'session');
        return false;
      }
      if ($row['ip'] !== $this->get('ip')) {
        $this->rex->core->modules['log']->_('Session is not valid - IP address does not match database record');
        $this->parent->modules['mysql']->query('DELETE FROM `sessions` WHERE `id` = "' . $session['id'] . '";', 'session');
        return false;
      }
      if ($row['agent'] !== $this->get('agent')) {
        $this->rex->core->modules['log']->_('Session is not valid - User agent does not match database record');
        $this->parent->modules['mysql']->query('DELETE FROM `sessions` WHERE `id` = "' . $session['id'] . '";', 'session');
        return false;
      }
    }
    if ($this->get('ip') !== $_SERVER['REMOTE_ADDR']) {
      $this->rex->core->modules['log']->_('Session is not valid - IP address does not match');
      return false;
    }
    if ($this->get('agent') !== $_SERVER['HTTP_USER_AGENT']) {
      $this->rex->core->modules['log']->_('Session is not valid - User agent does not match');
      return false;
    }
    return true;
  }
  public function set() {
    $arguments = func_get_args();
    if ($this->exists() == true) {
      if ($this->valid() == true) {
        $session['id'] = $this->get('id');
        $session['ip'] = $this->get('ip');
        $session['agent'] = $this->get('agent');
      }
    }
    if (isset($session['id']) == false) {
      for ($i = 0; $i < 32; $i++) {
        if (isset($id) == false) {
          $id = rand(0, 9);
          continue;
        }
        $id = $id . rand(0, 9);
      }
      $session['id'] = md5($id);
    }
    if (isset($session['ip']) == false) {
      $session['ip'] = $_SERVER['REMOTE_ADDR'];
    }
    if (isset($session['agent']) == false) {
      $session['agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
    $session['time'] = microtime(true);
    if (isset($arguments[0]) == true) {
      if (is_array($arguments[0]) == true) {
        $data = $arguments[0];
        foreach ($data as $key => $value) {
          if (isset($session[$key]) == false) {
            $session[$key] = $value;
          }
        }
      }
    }
    if (isset($data) == true) {
      unset($data);
    }
    if ((integer) $this->rex->core->modules['settings']->get('rex.system.modules.session.database.sync') == 1) {
      if ($this->parent->exists('module', 'mysql') == false) {
        $this->parent->load('module', 'mysql');
      }
      if ($this->parent->modules['mysql']->exists('session') == false) {
        $mysql['server'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.mysql.server');
        $mysql['username'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.mysql.username');
        $mysql['password'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.mysql.password');
        $mysql['database'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.mysql.database');
        if ($this->parent->modules['mysql']->add('session', $mysql['server'], $mysql['username'], $mysql['password'], $mysql['database']) == false) {
          $this->rex->core->modules['log']->_('Unable to set session - Unable to add mysql connection');
          return false;
        }
      }
      if ($this->exists() == true) {
        $sql = "UPDATE `sessions` SET `ip` = \"" . $session['ip'] . "\", `agent` = \"" . $session['agent'] . "\", `expires` = \"" . (string) (time() + (integer) $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.expires')) . "\" WHERE `id` = \"" . $session['id'] . "\";";
      } else {
        $sql = "INSERT INTO `sessions` (`id`, `ip`, `agent`, `expires`) VALUES('" . $session['id'] . "', '" . $session['ip'] . "', '" . $session['agent'] . "', '" . (string) (time() + (integer) $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.expires')) . "');";
      }
      if (($query = $this->parent->modules['mysql']->query($sql, 'session')) == false) {
        $this->rex->core->modules['log']->_('Unable to set session - Unable to execute mysql query');
        return false;
      }
    }
    $cookie['name'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.name');
    $cookie['value'] = base64_encode(serialize($session));
    $cookie['expires'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.expires');
    $cookie['path'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.path');
    $cookie['domain'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.domain');
    if (setcookie($cookie['name'], $cookie['value'], (time() + (integer) $cookie['expires']), $cookie['path'], $cookie['domain']) == false) {
      $this->rex->core->modules['log']->_('Unable to set session - Unable to set cookie');
      return false;
    }
    $this->rex->core->modules['log']->_('Set session');
    return true;
  }
  public function remove() {
    if ($this->exists() == false) {
      $this->rex->core->modules['log']->_('Unable to remove session - Session does not exist');
      return false;
    }
    $cookie['name'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.name');
    $cookie['expires'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.expires');
    $cookie['path'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.path');
    $cookie['domain'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.domain');
    if (setcookie($cookie['name'], '', time() - (integer) $cookie['expires'], $cookie['path'], $cookie['domain']) == false) {
      $this->rex->core->modules['log']->_('Unable to remove session \'' . $cookie['name'] . '\' - Unable to set cookie');
      return false;
    }
    $this->rex->core->modules['log']->_('Remove session');
    return true;
  }
  public function get() {
    $arguments = func_get_args();
    if ($this->exists() == false) {
      return false;
    }
    $cookie['name'] = $this->rex->core->modules['settings']->get('rex.system.modules.session.cookie.name');
    $session = unserialize(base64_decode($this->parent->modules['input']->get('cookie', $cookie['name'])));
    if (isset($arguments[0]) == false) {
      return $session;
    }
    if (is_string($arguments[0]) == false) {
      return false;
    }
    $key = $arguments[0];
    if ($this->exists($key) == false) {
      return false;
    }
    return $session[$key];
  }
}
?>