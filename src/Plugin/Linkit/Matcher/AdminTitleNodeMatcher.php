<?php

namespace Drupal\admin_title\Plugin\Linkit\Matcher;

use Drupal\admin_title\Helper;
use Drupal\linkit\Plugin\Linkit\Matcher\NodeMatcher;

/**
 * A Linkit matcher for nodes with admin title support.
 *
 * @Matcher(
 *   id = "entity:admin_title_node",
 *   label = @Translation("Content with Admin Title support"),
 *   target_entity = "node",
 *   provider = "node",
 * )
 */
class AdminTitleNodeMatcher extends NodeMatcher {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($search_string) {
    $query = parent::buildEntityQuery($search_string);

    Helper::addAdminTitleToEntityQuery($query, $this->targetType);

    return $query;
  }

}
