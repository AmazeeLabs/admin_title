<?php

namespace Drupal\admin_title\Controller;

use Drupal\content_translation\Controller\ContentTranslationController;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Use the admin title for translations, alters content translation controller.
 */
class AdminTitleController extends ContentTranslationController {

  /**
   * {@inheritdoc}
   */
  public function overview(RouteMatchInterface $route_match, $entity_type_id = NULL) {
    $build = parent::overview($route_match, $entity_type_id);
    $entity = $build['#entity'];
    if ($entity instanceof ContentEntityInterface) {
      $args = _admin_title_get_common_args($entity);

      // Update page title.
      $build['#title'] = $args['@bundle'] === NULL
        ? $this->t('Translations of @entity_type "@title")', $args)
        : $this->t('Translations of @bundle "@title" (@entity_type)', $args);

      // Update Translation column.
      foreach ($build['content_translation_overview']['#rows'] as $delta => $row) {
        if (isset($row[3]['data']['#links']['edit']['language'])) {
          $entity = $entity->getTranslation($row[3]['data']['#links']['edit']['language']->getId());
          $admin_title_field = $entity->get('field_admin_title');
          if (!$admin_title_field->isEmpty() && isset($admin_title_field->value) && is_string($admin_title_field->value)) {
            $link = Link::fromTextAndUrl($admin_title_field->value, $entity->toUrl());
            $build['content_translation_overview']['#rows'][$delta][1] = $link;
          }
        }
      }
    }
    return $build;
  }

}
