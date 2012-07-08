<?php
// rex/system/modules/router.php

namespace rex\system\modules;

class router extends module {
  protected $routes;
  protected $wildcards;
  
  public function initialize() {
    $settings['wildcard.required.delimiter.left'] = '(:';
    $settings['wildcard.required.delimiter.right'] = ')';
    $settings['wildcard.optional.delimiter.left'] = '(:';
    $settings['wildcard.optional.delimiter.right'] = '?)';
    foreach ($settings as $key => $value) {
      $key = 'rex.system.modules.router.' . $key;
      if ($this->rex->core->modules->settings->exists($key) === false) {
        $this->rex->core->modules->settings->add($key, $value);
      }
    }
    
    $wildcards['digit'] = '[0-9]+';
    $wildcards['alpha'] = '[a-zA-Z]+';
    $wildcards['alnum'] = '[a-zA-Z0-9]+';
    $wildcards['all'] = '[a-zA-Z0-9' . preg_quote('.-_%') . ']+';
    $wildcards['any'] = '.+';
    foreach ($wildcards as $tag => $pattern) {
      $tag = $this->rex->core->modules->settings->get('rex.system.modules.router.wildcard.required.delimiter.left') . $tag . $this->rex->core->modules->settings->get('rex.system.modules.router.wildcard.required.delimiter.right');
      $pattern = '(' . $pattern . ')';
      $this->wildcards['required'][$tag] = $pattern;
    }
    foreach ($wildcards as $tag => $pattern) {
      $tag = '/' . $this->rex->core->modules->settings->get('rex.system.modules.router.wildcard.optional.delimiter.left') . $tag . $this->rex->core->modules->settings->get('rex.system.modules.router.wildcard.optional.delimiter.right');
      $pattern = '(?:/' . '(' . $pattern . ')';
      $this->wildcards['optional'][$tag] = $pattern;
    }
  }
  public function get($path, $action) {
    return $this->add('get', $path, $action);
  }
  public function post($path, $action) {
    return $this->add('post', $path, $action);
  }
  public function put($path, $action) {
    return $this->add('put', $path, $action);
  }
  public function delete($path, $action) {
    return $this->add('delete', $path, $action);
  }
  public function add($method, $path, $action) {
    if (is_string($method) === false) {
      return false;
    }
    if (is_string($path) === false) {
      return false;
    }
    if (is_object($action) === false) {
      if (is_string($action) === false) {
        return false;
      }
    }
    if (in_array($method, array('get', 'post', 'put', 'delete')) === false) {
      return false;
    }
    if (empty($path) === true) {
      return false;
    }
    if (is_object($action) === true) {
      $action = array('type' => 'closure', 'callback' => $action);
    }
    if (is_string($action) === true) {
      $action = array('type' => 'controller', 'identifier' => $action);
    }
    $this->routes[] = array('method' => $method, 'path' => $path, 'action' => $action);
    return true;
  }
  public function route() {
    if ($this->rex->system->modules->exists('request') === false) {
      if ($this->rex->system->modules->load('request') === false) {
        return false;
      }
    }
    if (($request['method'] = $this->rex->system->modules->request->get('method')) === false) {
      return false;
    }
    if (($request['uri'] = $this->rex->system->modules->request->get('uri')) === false) {
      return false;
    }
    if (empty($this->routes) === true) {
      echo '<pre>No routes</pre>' . "\n";
      return false;
    }
    if ($this->matches($this->routes, $request, $matches) === false) {
      echo '<pre>No matches</pre>' . "\n";
      return false;
    }
    foreach ($matches as $route) {
      switch ($route['action']['type']) {
        case 'closure':
          if (isset($route['action']['parameters']) === true) {
            call_user_func_array($route['action']['callback'], $route['action']['parameters']);
          } else {
            $route['action']['callback']();
          }
          break;
        case 'controller':
          $this->rex->application->workspace->controllers->load($route['action']['identifier']);
          break;
        default:
          continue;
          break;
      }
    }
  }
  protected function matches($routes, $request, &$matches) {
    foreach ($routes as $route) {
      if ($route['method'] === $request['method']) {
        if (preg_match('/' . preg_quote($this->rex->core->modules->settings->get('rex.system.modules.router.wildcard.required.delimiter.left')) . '[a-z]+' . preg_quote($this->rex->core->modules->settings->get('rex.system.modules.router.wildcard.required.delimiter.right')) . '|' . preg_quote($this->rex->core->modules->settings->get('rex.system.modules.router.wildcard.optional.delimiter.left')) . '[a-z]+' . preg_quote($this->rex->core->modules->settings->get('rex.system.modules.router.wildcard.optional.delimiter.right')) . '/', trim($route['path'], '/')) === 1) {
          list($search, $replace) = $this->rex->core->modules->common->array['divide']($this->wildcards['optional']);
          $pattern = str_replace($search, $replace, trim($route['path'], '/'), $replacements);
          if ($replacements > 0) {
            $pattern = $pattern . str_repeat(')?', $replacements);
          }
          $pattern = strtr($pattern, $this->wildcards['required']);
          if (preg_match('#^' . $pattern . '$#', trim($request['uri']['path'], '/'), $parameters) === 1) {
            $parameters = array_slice($parameters, 1);
            if (empty($parameters) === false) {
              $route['action']['parameters'] = $parameters;
            }
            $matches[] = $route;
          }
        } else {
          if (trim($route['path'], '/') === trim($request['uri']['path'], '/')) {
            $matches[] = $route;
          }
        }
      }
    }
    return isset($matches);
  }
}
?>