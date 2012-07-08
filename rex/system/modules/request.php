<?php
// rex/system/modules/request.php

namespace rex\system\modules;

class request extends module {
  protected $method;
  protected $uri;
  
  public function initialize() {
    $this->method = $this->get('method');
    $this->uri = $this->get('uri');
  }
  public function get($switch) {
    if (is_string($switch) === false) {
      return false;
    }
    switch ($switch) {
      case 'method':
        if (isset($this->method) === true) {
          return $this->method;
        }
        if (isset($_SERVER['REQUEST_METHOD']) === false) {
          return false;
        }
        return strtolower($_SERVER['REQUEST_METHOD']);
        break;
      case 'uri':
        if (isset($this->uri) === true) {
          return $this->uri;
        }
        if (isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === true) {
          if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
          }
          if (isset($uri) === false) {
            if (strpos($_SERVER['REQUEST_URI'], dirname($_SERVER['SCRIPT_NAME'])) === 0) {
              $uri = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
          }
          if (isset($uri) === true) {
            if (strpos($uri, '?/') === 0) {
              $uri = substr($uri, strlen('?'));
            }
            $uri = explode('?', $uri);
            if (isset($uri[0]) === true) {
              $uri['path'] = $uri[0];
              unset($uri[0]);
            }
            if (isset($uri[1]) === true) {
              $uri['query'] = $uri[1];
              unset($uri[1]);
            } else {
              $_SERVER['QUERY_STRING'] = '';
              $_GET = array();
            }
          }
        }
        if (isset($uri['path']) === false) {
          if (isset($_SERVER['PATH_INFO']) === true) {
            $uri['path'] = $_SERVER['PATH_INFO'];
          }
        }
        if (isset($uri['query']) === false) {
          if (isset($_SERVER['QUERY_STRING']) === true) {
            $uri['query'] = $_SERVER['QUERY_STRING'];
          }
        }
        if (isset($uri['path']) === false) {
          return false;
        }
        if (isset($uri['query']) === false) {
          return false;
        }
        if ($uri['path'] !== '/' && empty($uri['path']) === false) {
          $uri['path'] = str_replace(array('//', '../'), '/', $uri['path']);
          $uri['path'] = parse_url('scheme://hostname/' . ltrim($uri['path'], '/'), PHP_URL_PATH);
        }
        $_SERVER['QUERY_STRING'] = $uri['query'];
        parse_str($uri['query'], $_GET);
        return $uri;
        break;
      default:
        return false;
        break;
    }
  }
}
?>