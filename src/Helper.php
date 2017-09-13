<?php

namespace Drupal\admin_title;

use Drupal\Core\Entity\Query\QueryInterface;

/**
 * Helper functions.
 */
class Helper {

  /**
   * Adds admin title condition to the entity query.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   * @param string $entity_type_id
   */
  public static function addAdminTitleToEntityQuery(QueryInterface $query, $entity_type_id) {
    $field_map = \Drupal::entityManager()->getFieldMapByFieldType('string');
    if (isset($field_map[$entity_type_id]['field_admin_title'])) {
      $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
      if ($label_key = $entity_type->getKey('label')) {

        // Unfortunately, there is no legal way to alter an entity query.
        // Actually, we might alter the resulting SQL query, but in this case
        // it's really hard to change the condition, because of all joins, etc.
        // So we use a hack to read and remove the original condition.
        $reflection = new \ReflectionClass($query);
        $property = $reflection->getProperty('condition');
        $property->setAccessible(true);
        /** @var \Drupal\Core\Entity\Query\Sql\Condition $condition */
        $condition = $property->getValue($query);
        $conditions =& $condition->conditions();

        $target_condition = NULL;
        if (is_array($conditions)) {
          foreach ($conditions as $key => $condition) {
            if ($condition['field'] === $label_key) {
              $target_condition = $condition;
              unset($conditions[$key]);
              break;
            }
          }
        }

        if ($target_condition) {
          $group = $query->orConditionGroup();
          $group->condition(
            $target_condition['field'],
            $target_condition['value'],
            $target_condition['operator'],
            $target_condition['langcode']
          );
          $group->condition(
            'field_admin_title',
            $target_condition['value'],
            $target_condition['operator'],
            $target_condition['langcode']
          );
          $query->condition($group);
        }

      }
    }
  }

}
