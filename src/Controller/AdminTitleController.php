<?php

namespace Drupal\admin_title\Controller;

use Drupal\content_translation\Controller\ContentTranslationController;
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
    /** @var \Drupal\node\Entity\Node $entity */
    $entity = $build['#entity'];
    if ($entity->bundle() == 'article' && $entity->hasField('field_admin_title')) {
      $admin_title = $entity->get('field_admin_title')->value;
      $build['#title'] = t('Translations of %label', array('%label' => $admin_title));
      $original = $entity->getUntranslated()->language()->getId();
      foreach ($build['content_translation_overview']['#rows'] as $delta => $row) {
        /** @var \Drupal\Core\Language\Language $language */
        $language = $row[4]['data']['#links']['edit']['language'];
        $translated_entity = $entity->getTranslation($language->getId());
        /** @var \Drupal\Core\Url $url */
        $url = $translated_entity->urlInfo();
        $translated_admin_title = $translated_entity->get('field_admin_title')->value;
        if (!empty($url)) {
          $row_title = Link::fromTextAndUrl($translated_admin_title, $url);
        }
        else {
          $row_title = $original == $language->getId() ? $admin_title : $this->t('n/a');
        }
        $build['content_translation_overview']['#rows'][$delta][1] = $row_title;
      }
    }
    return $build;
  }

}
