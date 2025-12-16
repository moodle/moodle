<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1SetConfigRequest extends \Google\Model
{
  /**
   * Default value. The default UI is Dataplex Universal Catalog.
   */
  public const CATALOG_UI_EXPERIENCE_CATALOG_UI_EXPERIENCE_UNSPECIFIED = 'CATALOG_UI_EXPERIENCE_UNSPECIFIED';
  /**
   * The UI is Dataplex Universal Catalog.
   */
  public const CATALOG_UI_EXPERIENCE_CATALOG_UI_EXPERIENCE_ENABLED = 'CATALOG_UI_EXPERIENCE_ENABLED';
  /**
   * The UI is Data Catalog.
   */
  public const CATALOG_UI_EXPERIENCE_CATALOG_UI_EXPERIENCE_DISABLED = 'CATALOG_UI_EXPERIENCE_DISABLED';
  /**
   * Default value. Migration of Tag Templates from Data Catalog to Dataplex
   * Universal Catalog is not performed. For projects that are under an
   * organization, the project inherits the organization's configuration when
   * you set the project-level configuration to unspecified
   * (`TAG_TEMPLATE_MIGRATION_UNSPECIFIED`). This means that when migration is
   * enabled at the organization level, and the project-level configuration is
   * unspecified, the project is migrated. To explicitly opt-in or opt-out
   * individual projects, set the project-level configuration to enabled
   * (`TAG_TEMPLATE_MIGRATION_ENABLED`) or disabled
   * (`TAG_TEMPLATE_MIGRATION_DISABLED`).
   */
  public const TAG_TEMPLATE_MIGRATION_TAG_TEMPLATE_MIGRATION_UNSPECIFIED = 'TAG_TEMPLATE_MIGRATION_UNSPECIFIED';
  /**
   * Migration of Tag Templates from Data Catalog to Dataplex Universal Catalog
   * is enabled.
   */
  public const TAG_TEMPLATE_MIGRATION_TAG_TEMPLATE_MIGRATION_ENABLED = 'TAG_TEMPLATE_MIGRATION_ENABLED';
  /**
   * Migration of Tag Templates from Data Catalog to Dataplex Universal Catalog
   * is disabled.
   */
  public const TAG_TEMPLATE_MIGRATION_TAG_TEMPLATE_MIGRATION_DISABLED = 'TAG_TEMPLATE_MIGRATION_DISABLED';
  /**
   * Opt-in status for the UI switch to Dataplex Universal Catalog.
   *
   * @var string
   */
  public $catalogUiExperience;
  /**
   * Opt-in status for the migration of Tag Templates to Dataplex Universal
   * Catalog.
   *
   * @var string
   */
  public $tagTemplateMigration;

  /**
   * Opt-in status for the UI switch to Dataplex Universal Catalog.
   *
   * Accepted values: CATALOG_UI_EXPERIENCE_UNSPECIFIED,
   * CATALOG_UI_EXPERIENCE_ENABLED, CATALOG_UI_EXPERIENCE_DISABLED
   *
   * @param self::CATALOG_UI_EXPERIENCE_* $catalogUiExperience
   */
  public function setCatalogUiExperience($catalogUiExperience)
  {
    $this->catalogUiExperience = $catalogUiExperience;
  }
  /**
   * @return self::CATALOG_UI_EXPERIENCE_*
   */
  public function getCatalogUiExperience()
  {
    return $this->catalogUiExperience;
  }
  /**
   * Opt-in status for the migration of Tag Templates to Dataplex Universal
   * Catalog.
   *
   * Accepted values: TAG_TEMPLATE_MIGRATION_UNSPECIFIED,
   * TAG_TEMPLATE_MIGRATION_ENABLED, TAG_TEMPLATE_MIGRATION_DISABLED
   *
   * @param self::TAG_TEMPLATE_MIGRATION_* $tagTemplateMigration
   */
  public function setTagTemplateMigration($tagTemplateMigration)
  {
    $this->tagTemplateMigration = $tagTemplateMigration;
  }
  /**
   * @return self::TAG_TEMPLATE_MIGRATION_*
   */
  public function getTagTemplateMigration()
  {
    return $this->tagTemplateMigration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1SetConfigRequest::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SetConfigRequest');
