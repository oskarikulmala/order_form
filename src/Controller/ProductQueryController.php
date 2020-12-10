<?php

namespace Drupal\order_form\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
   * Anything with dependencies needs to implement ContainerInjectionInterface in one way or another (directly or inherited via the parent class).
   *
   */

class ProductQueryController extends ControllerBase implements ContainerInjectionInterface {
/**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entity_query;

  protected $entity_manager;

  /**
     * The create() method on the controller gets the container passed in, and then it can inject anything in the container into the constructor.
     */
  public function __construct(QueryFactory $entity_query, EntityManager $entity_manager) {
    $this->entity_query = $entity_query;
    $this->entity_manager = $entity_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query'),
      $container->get('entity.manager')
    );
  }

  /**
     * Find all products and return them as array
     * @return static[] An array of entity objects indexed by their IDs.

     */
  public function getProducts() {
    $nids = $this->productQuery();

    /** @var $node_storage \Drupal\node\NodeStorage */
    $node_storage = $this->entity_manager->getStorage('node');
    return $nodes = $node_storage->loadMultiple($nids);
  }

  /**
     * Build and execute query.
     */
  private function productQuery() {
    // Use the factory to create a query object for node entities.
    $query = $this->entity_query->get('node')
      ->condition('type', 'tuote');
    return $query->execute();
  }
}
