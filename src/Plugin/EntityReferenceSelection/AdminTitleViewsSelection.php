<?php

namespace Drupal\admin_title\Plugin\EntityReferenceSelection;

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
    if ($entity_type_id = $this->getEntityTypeId()) {
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type_id);
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

  /**
   * Returns target entity type ID.
   *
   * ViewExecutable::getBaseEntityType() was introduced in Drupal 8.2. This
   * function is a fallback to support Drupal < 8.2.
   *
   * @return string|null
   */
  protected function getEntityTypeId() {
    if (is_callable([$this->view, 'getBaseEntityType'])) {
      $entity_type = $this->view->getBaseEntityType();
      if ($entity_type && is_callable([$entity_type, 'id'])) {
        return $entity_type->id();
      }
    }
    if (isset($this->configuration['target_type'])) {
      return $this->configuration['target_type'];
    }
    return NULL;
  }

}
