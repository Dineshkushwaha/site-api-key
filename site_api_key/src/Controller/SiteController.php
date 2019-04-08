<?php

namespace Drupal\site_api_key\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SiteController implements get_content method.
 */
class SiteController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->config = $configFactory->get('siteapikey.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
    $container->get('config.factory')
    );
  }

  /**
   * Implements get_content method to return the JSON response.
   */
  public function getContent($api_key, $nid) {
    $response_array = [];
    $output = [];
    $api_value = $this->config->get('siteapikey');
    if ($api_value === 'No API Key yet') {
      return new JsonResponse($this->t('No API key set'));
    }
    $node_storage = $this->entityTypeManager()->getStorage('node');
    $node = $node_storage->load($nid);
    if ((isset($node) && $api_value === $api_key) && ($node->bundle() === 'page')) {
      $response_array[$node->get('title')->value] = [
        'title' => $node->get('title')->value,
        'nid' => $node->get('nid')->value,
        'body' => $node->get('body')->value,
      ];
      $output = new JsonResponse($response_array);
    }
    else {
      $output = new JsonResponse($this->t("Access Denied"));
    }
    return $output;
  }

}
