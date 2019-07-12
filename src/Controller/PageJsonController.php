<?php

namespace Drupal\page_json\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\Entity\Node;

/**
 * Controller for Page JSON.
 */
class PageJsonController extends ControllerBase {

  /**
   * Gets JSON for a node.
   */
  public function pageJson(RouteMatchInterface $route_match) {
    $nid = $route_match->getParameter('nid');

    if (!is_numeric($nid)) {
      throw new AccessDeniedHttpException();
    }
    $node_dt = Node::load($nid);
    if (empty($node_dt)) {
      throw new AccessDeniedHttpException();
    }
    $node_type = $node_dt->bundle();

    // API Key retrieved from Request URL.
    $api_key = $route_match->getParameter('api_key');
    // API Key retrieved from Configuration.
    $config = \Drupal::service('config.factory')->getEditable('system.site');
    $siteapikey = $config->get('siteapikey');
    if (($api_key == $siteapikey) && ($node_type == 'page')) {
      $serializer = \Drupal::service('serializer');
      $data = $serializer->serialize($node_dt, 'json', ['plugin_id' => 'entity']);
      return new JsonResponse([
        'data' => $data,
        'method' => 'GET',
      ]);
    }
    else {
      throw new AccessDeniedHttpException();
    }

  }

}
