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

class GoogleCloudDatacatalogV1TagTemplate extends \Google\Model
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
   * Optional. Transfer status of the TagTemplate
   *
   * @var string
   */
  public $dataplexTransferStatus;
  /**
   * Display name for this template. Defaults to an empty string. The name must
   * contain only Unicode letters, numbers (0-9), underscores (_), dashes (-),
   * spaces ( ), and can't start or end with spaces. The maximum length is 200
   * characters.
   *
   * @var string
   */
  public $displayName;
  protected $fieldsType = GoogleCloudDatacatalogV1TagTemplateField::class;
  protected $fieldsDataType = 'map';
  /**
   * Indicates whether tags created with this template are public. Public tags
   * do not require tag template access to appear in ListTags API response.
   * Additionally, you can search for a public tag by value with a simple search
   * query in addition to using a ``tag:`` predicate.
   *
   * @var bool
   */
  public $isPubliclyReadable;
  /**
   * Identifier. The resource name of the tag template in URL format. Note: The
   * tag template itself and its child resources might not be stored in the
   * location specified in its name.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. Transfer status of the TagTemplate
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
   * Display name for this template. Defaults to an empty string. The name must
   * contain only Unicode letters, numbers (0-9), underscores (_), dashes (-),
   * spaces ( ), and can't start or end with spaces. The maximum length is 200
   * characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. Map of tag template field IDs to the settings for the field. This
   * map is an exhaustive list of the allowed fields. The map must contain at
   * least one field and at most 500 fields. The keys to this map are tag
   * template field IDs. The IDs have the following limitations: * Can contain
   * uppercase and lowercase letters, numbers (0-9) and underscores (_). * Must
   * be at least 1 character and at most 64 characters long. * Must start with a
   * letter or underscore.
   *
   * @param GoogleCloudDatacatalogV1TagTemplateField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleCloudDatacatalogV1TagTemplateField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Indicates whether tags created with this template are public. Public tags
   * do not require tag template access to appear in ListTags API response.
   * Additionally, you can search for a public tag by value with a simple search
   * query in addition to using a ``tag:`` predicate.
   *
   * @param bool $isPubliclyReadable
   */
  public function setIsPubliclyReadable($isPubliclyReadable)
  {
    $this->isPubliclyReadable = $isPubliclyReadable;
  }
  /**
   * @return bool
   */
  public function getIsPubliclyReadable()
  {
    return $this->isPubliclyReadable;
  }
  /**
   * Identifier. The resource name of the tag template in URL format. Note: The
   * tag template itself and its child resources might not be stored in the
   * location specified in its name.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1TagTemplate::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1TagTemplate');
