<?php

namespace Drupal\admin_title\Element;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

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
        ? _admin_title_get_admin_title($entity, TRUE, TRUE, NULL, TRUE)
        : t('- Restricted access -');
      if (!$entity->isNew()) {
        $label .= ' (' . $entity->id() . ')';
      }
      $entity_labels[] = Tags::encode($label);
    }
    return implode(', ', $entity_labels);
  }

  /**
   * {@inheritdoc}
   */
  public static function processEntityAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form) {
    if ($element['#selection_handler'] === 'default:node') {

      // Add the admin title support to autocomplete.
      $element['#selection_handler'] = 'default:node_admin_title';

      // Default value should use admin title as well.
      if (isset($element['#default_value']) && isset($element['#value']) && $element['#default_value'] === $element['#value']) {
        $node_id = self::extractEntityIdFromAutocompleteInput($element['#default_value']);
        if ($node_id && ($node = Node::load($node_id))) {
          $element['#default_value'] = $element['#value'] = self::getEntityLabels([$node]);
        }
      }
    }

    return parent::processEntityAutocomplete($element, $form_state, $complete_form);
  }

}
