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

      // Get column indexes. They are not constant.
      $translation_key = NULL;
      $operations_key = NULL;
      $translation_string = (string) $this->t('Translation');
      $operations_string = (string) $this->t('Operations');
      foreach ($build['content_translation_overview']['#header'] as $key => $header) {
        $header_string = (string) $header;
        if ($header_string == $translation_string) {
          $translation_key = $key;
        }
        elseif ($header_string == $operations_string) {
          $operations_key = $key;
        }
      }
      if ($translation_key === NULL || $operations_key === NULL) {
        return $build;
      }

      // Update Translation column.
      foreach ($build['content_translation_overview']['#rows'] as $delta => $row) {
        if (isset($row[$operations_key]['data']['#links']['edit']['language'])) {
          $langcode = $row[$operations_key]['data']['#links']['edit']['language']->getId();
          $translation = $entity->getTranslation($langcode);
          $admin_title = _admin_title_get_admin_title($translation, FALSE, FALSE);
          if ($admin_title !== NULL) {
            $link = Link::fromTextAndUrl($admin_title, $translation->toUrl());
            $build['content_translation_overview']['#rows'][$delta][$translation_key] = $link;
          }
        }
      }
    }
    return $build;
  }

}
