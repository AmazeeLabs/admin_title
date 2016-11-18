<?php

namespace Drupal\admin_title\Element;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Entity\Element\EntityAutocomplete;

/**
 * Provides an entity autocomplete form element with admin title support.
 *
 * @FormElement("entity_autocomplete_admin_title")
 */
class AdminTitleEntityAutocomplete extends EntityAutocomplete {

  /**
   * {@inheritdoc}
   */
  public static function getEntityLabels(array $entities) {

    // Do exactly the same as the parent method, but use admin title.
    $entity_labels = array();
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    foreach ($entities as $entity) {
      $label = ($entity->access('view label'))
        ? _admin_title_get_admin_title($entity, TRUE, TRUE)
        : t('- Restricted access -');
      if (!$entity->isNew()) {
        $label .= ' (' . $entity->id() . ')';
      }
      $entity_labels[] = Tags::encode($label);
    }
    return implode(', ', $entity_labels);
  }

}
