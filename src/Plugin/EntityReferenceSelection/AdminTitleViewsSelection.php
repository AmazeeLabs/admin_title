<?php

namespace Drupal\admin_title\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\views\Plugin\EntityReferenceSelection\ViewsSelection;

/**
 * Plugin implementation of the 'selection' entity_reference.
 *
 * The plugin only updates resulting entity labels to admin titles. If you like
 * to search for admin titles, you need to build your view in this way.
 *
 * @EntityReferenceSelection(
 *   id = "views_admin_title",
 *   label = @Translation("Views: Filter by an entity reference view (with admin title support)"),
 *   group = "views_admin_title",
 *   weight = 0
 * )
 */
class AdminTitleViewsSelection extends ViewsSelection {

  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $return = parent::getReferenceableEntities($match, $match_operator, $limit);
    if ($entity_type = $this->view->getBaseEntityType()) {
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type->id());
      foreach ($return as $bundle => $items) {
        foreach ($items as $entity_id => $item) {
          if ($entity = $storage->load($entity_id)) {
            $return[$bundle][$entity_id] = _admin_title_get_admin_title($entity, TRUE, TRUE);
          }
        }
      }
    }
    return $return;
  }

}
