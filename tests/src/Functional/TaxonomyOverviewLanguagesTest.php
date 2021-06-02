<?php

namespace Drupal\Tests\admin_title\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests "Translation" column on taxonomy overview pages.
 *
 * @group admin_title
 */
class TaxonomyOverviewLanguagesTest extends BrowserTestBase {

  use TaxonomyTestTrait;
  use StringTranslationTrait;

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
    'taxonomy',
    'content_translation',
    'admin_title',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer taxonomy',
      'administer content translation',
    ]);

    ConfigurableLanguage::createFromLangcode('de')->save();
    ConfigurableLanguage::createFromLangcode('fr')->save();
  }

  /**
   * Tests "Translation" column on taxonomy overview pages.
   *
   * @see admin_title_form_taxonomy_overview_terms_alter()
   */
  public function testTaxonomyOverviewLanguages() {
    $this->drupalLogin($this->adminUser);

    /** @var \Drupal\taxonomy\Entity\Vocabulary $vocabulary */
    $vocabulary = $this->createVocabulary();
    $edit = [
      'default_language[content_translation]' => TRUE,
    ];
    $this->drupalPostForm('admin/structure/taxonomy/manage/' . $vocabulary->id(), $edit, $this->t('Save'));

    $this->createTerm($vocabulary, ['name' => 'EN', 'langcode' => 'en']);
    $this->createTerm($vocabulary, ['name' => 'EN de', 'langcode' => 'en'])
      ->addTranslation('de', ['name' => 'en DE'])
      ->save();
    $this->createTerm($vocabulary, ['name' => 'DE en fr', 'langcode' => 'de'])
      ->addTranslation('en', ['name' => 'de EN fr'])
      ->addTranslation('fr', ['name' => 'de en FR'])
      ->save();

    $this->drupalGet('admin/structure/taxonomy/manage/' . $vocabulary->id() . '/overview');

    $this->assertSession()->responseContains('<th>Languages</th>');
    $this->assertSession()->responseContains('<td><em>English</em></td>');
    $this->assertSession()->responseContains('<td><em>English</em>, German</td>');
    $this->assertSession()->responseContains('<td>English, French, <em>German</em></td>');
  }

}
