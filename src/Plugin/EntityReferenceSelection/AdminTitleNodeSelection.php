<?php

namespace Drupal\admin_title\Plugin\EntityReferenceSelection;

use Drupal\admin_title\Helper;
use Drupal\Component\Utility\Html;
use Drupal\node\Plugin\EntityReferenceSelection\NodeSelection;

/**
 * Plugin implementation of the 'selection' entity_reference.
 *
 * @EntityReferenceSelection(
 *   id = "default:node_admin_title",
 *   label = @Translation("Node selection with Admin Title support"),
 *   entity_types = {"node"},
 *   group = "default",
 *   weight = 10,
 * )
 */
class AdminTitleNodeSelection extends NodeSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator);

    Helper::addAdminTitleToEntityQuery($query, $this->configuration['target_type']);

    return $query;
  }


  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $options = parent::getReferenceableEntities($match, $match_operator, $limit);

    $target_type = $this->configuration['target_type'];
    foreach ($options as $bundle => $id_to_label_map) {
      $entities = $this->entityManager->getStorage($target_type)->loadMultiple(array_keys($id_to_label_map));
      foreach ($entities as $entity_id => $entity) {
        $options[$bundle][$entity_id] = Html::escape(_admin_title_get_admin_title($entity, TRUE, TRUE));
      }
    }

    return $options;
  }

}
