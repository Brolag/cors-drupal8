<?php

/**
 * @file 
 *   Contains Drupal\cors\StackOptionsRequest.
 */

namespace Drupal\cors;

use Drupal\Core\DrupalKernelInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Wrapper to OPTIONS request.
 */
class StackOptionsRequest implements HttpKernelInterface {

  /**
   * The wrapped kernel implementation.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  private $app;

  /**
   * Create a new StackOptionsRequest instance.
   *
   * @param  \Symfony\Component\HttpKernel\HttpKernelInterface  $app
   *   Hrrp Kernel.
   */
  public function __construct(HttpKernelInterface $app) {
    $this->app = $app;
  }

  /**
   * {@inheritDoc}
   */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    $response = $this->app->handle($request, $type, $catch);

     // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        $response->headers->set('Access-Control-Allow-Origin', "{$_SERVER['HTTP_ORIGIN']}");
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');
    }

    // Allow headers for OPTIONS method [estas son mis lineas]
    if ($request->getMethod() == 'OPTIONS') {
        $response->setContent(Null);
        $response->setStatusCode(200);
   
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS');         
        }
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
          $response->headers->set('Access-Control-Allow-Headers', "{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");      
        }
    }
    // Aqui terminan mis lineas

    return $response;
  }
}
