<?php

namespace Codeception\Lib\Connector;


use Drupal\Driver\DrupalDriver;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Response;

class Drupal7 extends Client
{
    use Shared\PhpSuperGlobalsConverter;

    /**
     * @var DrupalDriver
     */
    protected $driver;

    /**
     * @return mixed
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param mixed $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Makes a request.
     *
     * @param object $request
     *   An origin request instance.
     *
     * @return object
     *   An origin response instance.
     */
    protected function doRequest($request)
    {
        $_COOKIE = $request->getCookies();
        $_SERVER = $request->getServer();
        $_FILES = $this->remapFiles($request->getFiles());

        $uri = str_replace('http://localhost', '', $request->getUri());

        $_REQUEST = $this->remapRequestParameters($request->getParameters());
        if (strtoupper($request->getMethod()) == 'GET') {
            $_GET = $_REQUEST;
        } else {
            $_POST = $_REQUEST;
        }

        $_SERVER['REQUEST_METHOD'] = strtoupper($request->getMethod());
        $_SERVER['REQUEST_URI'] = $uri;

        drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

        ob_start();
        menu_execute_active_handler();

        $content = ob_get_contents();
        ob_end_clean();

        $headers = [];
        $php_headers = headers_list();
        foreach ($php_headers as $value) {
            // Get the header name
            $parts = explode(':', $value);
            if (count($parts) > 1) {
                $name = trim(array_shift($parts));
                // Build the header hash map
                $headers[$name] = trim(implode(':', $parts));
            }
        }
        $headers['Content-type'] = isset($headers['Content-type'])
            ? $headers['Content-type']
            : "text/html; charset=UTF-8";

        $response = new Response($content, 200, $headers);
        return $response;

    }

}
