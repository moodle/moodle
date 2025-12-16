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

class GoogleCloudDatacatalogV1Tag extends \Google\Model
{
  /**
   * Default value. TagTemplate and its tags are only visible and editable in
   * Data Catalog.
   */
  public const DATAPLEX_TRANSFER_STATUS_DATAPLEX_TRANSFER_STATUS_UNSPECIFIED = 'DATAPLEX_TRANSFER_STATUS_UNSPECIFIED';
  /**
   * TagTemplate and its tags are auto-copied to Dataplex Universal Catalog
   * service. Visible in both services. Editable in Data Catalog, read-only in
   * Dataplex Universal Catalog. Deprecated: Individual TagTemplate migration is
   * deprecated in favor of organization or project wide TagTemplate migration
   * opt-in.
   *
   * @deprecated
   */
  public const DATAPLEX_TRANSFER_STATUS_MIGRATED = 'MIGRATED';
  /**
   * TagTemplate and its tags are auto-copied to Dataplex Universal Catalog
   * service. Visible in both services. Editable in Dataplex Universal Catalog,
   * read-only in Data Catalog.
   */
  public const DATAPLEX_TRANSFER_STATUS_TRANSFERRED = 'TRANSFERRED';
  /**
   * Resources like entry can have schemas associated with them. This scope
   * allows you to attach tags to an individual column based on that schema. To
   * attach a tag to a nested column, separate column names with a dot (`.`).
   * Example: `column.nested_column`.
   *
   * @var string
   */
  public $column;
  /**
   * Output only. Denotes the transfer status of the Tag Template.
   *
   * @var string
   */
  public $dataplexTransferStatus;
  protected $fieldsType = GoogleCloudDatacatalogV1TagField::class;
  protected $fieldsDataType = 'map';
  /**
   * Identifier. The resource name of the tag in URL format where tag ID is a
   * system-generated identifier. Note: The tag itself might not be stored in
   * the location specified in its name.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The resource name of the tag template this tag uses. Example:
   * `projects/{PROJECT_ID}/locations/{LOCATION}/tagTemplates/{TAG_TEMPLATE_ID}`
   * This field cannot be modified after creation.
   *
   * @var string
   */
  public $template;
  /**
   * Output only. The display name of the tag template.
   *
   * @var string
   */
  public $templateDisplayName;

  /**
   * Resources like entry can have schemas associated with them. This scope
   * allows you to attach tags to an individual column based on that schema. To
   * attach a tag to a nested column, separate column names with a dot (`.`).
   * Example: `column.nested_column`.
   *
   * @param string $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return string
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * Output only. Denotes the transfer status of the Tag Template.
   *
   * Accepted values: DATAPLEX_TRANSFER_STATUS_UNSPECIFIED, MIGRATED,
   * TRANSFERRED
   *
   * @param self::DATAPLEX_TRANSFER_STATUS_* $dataplexTransferStatus
   */
  public function setDataplexTransferStatus($dataplexTransferStatus)
  {
    $this->dataplexTransferStatus = $dataplexTransferStatus;
  }
  /**
   * @return self::DATAPLEX_TRANSFER_STATUS_*
   */
  public function getDataplexTransferStatus()
  {
    return $this->dataplexTransferStatus;
  }
  /**
   * Required. Maps the ID of a tag field to its value and additional
   * information about that field. Tag template defines valid field IDs. A tag
   * must have at least 1 field and at most 500 fields.
   *
   * @param GoogleCloudDatacatalogV1TagField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleCloudDatacatalogV1TagField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Identifier. The resource name of the tag in URL format where tag ID is a
   * system-generated identifier. Note: The tag itself might not be stored in
   * the location specified in its name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The resource name of the tag template this tag uses. Example:
   * `projects/{PROJECT_ID}/locations/{LOCATION}/tagTemplates/{TAG_TEMPLATE_ID}`
   * This field cannot be modified after creation.
   *
   * @param string $template
   */
  public function setTemplate($template)
  {
    $this->template = $template;
  }
  /**
   * @return string
   */
  public function getTemplate()
  {
    return $this->template;
  }
  /**
   * Output only. The display name of the tag template.
   *
   * @param string $templateDisplayName
   */
  public function setTemplateDisplayName($templateDisplayName)
  {
    $this->templateDisplayName = $templateDisplayName;
  }
  /**
   * @return string
   */
  public function getTemplateDisplayName()
  {
    return $this->templateDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1Tag::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1Tag');
