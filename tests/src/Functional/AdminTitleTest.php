<?php

namespace Drupal\Tests\admin_title\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\KernelTests\AssertContentTrait;
use Drupal\Tests\node\Traits\ContentTypeCreationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the Admin Title module.
 *
 * @group admin_title
 */
class AdminTitleTest extends BrowserTestBase {
  use AssertContentTrait;
  use ContentTypeCreationTrait;

  /**
   * The admin user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'content_translation',
    'node',
    'block',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer languages',
      'access administration pages',
      'administer content types',
    ]);
  }

  /**
   * Test admin title.
   */
  public function testAdminTitle() {
    $this->drupalLogin($this->adminUser);
    $language = ConfigurableLanguage::createFromLangcode('gsw-berne');
    $language->save();

    $this->createContentType([
      'type' => 'article',
      'Article'
    ]);

    // Enable entity translations for article.
    $content_translation_manager = \Drupal::service('content_translation.manager');
    $content_translation_manager->setEnabled('node', 'article', TRUE);

    // Apply schema updates.
    drupal_static_reset();
    \Drupal::entityTypeManager()->clearCachedDefinitions();
    \Drupal::service('router.builder')->rebuild();
    \Drupal::service('entity.definition_update_manager')->applyUpdates();

    // Create a field.
    $field_storage = FieldStorageConfig::create(array(
      'field_name' => 'field_admin_title',
      'entity_type' => 'node',
      'type' => 'text',
      'cardinality' => mt_rand(1, 5),
      'translatable' => TRUE,
    ));
    $field_storage->save();

    // Create an instance of the previously created field.
    $field = FieldConfig::create(array(
      'field_name' => 'field_admin_title',
      'entity_type' => 'node',
      'bundle' => 'article',
      'label' => $this->randomMachineName(10),
      'description' => $this->randomString(30),
      'widget' => array(
        'type' => 'text_textfield',
        'label' => $this->randomString(10),
      ),
    ));
    $field->save();

    $this->drupalGet('<front>');
  }

}
