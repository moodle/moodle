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

namespace Google\Service\Sheets;

class DeveloperMetadata extends \Google\Model
{
  /**
   * Default value.
   */
  public const VISIBILITY_DEVELOPER_METADATA_VISIBILITY_UNSPECIFIED = 'DEVELOPER_METADATA_VISIBILITY_UNSPECIFIED';
  /**
   * Document-visible metadata is accessible from any developer project with
   * access to the document.
   */
  public const VISIBILITY_DOCUMENT = 'DOCUMENT';
  /**
   * Project-visible metadata is only visible to and accessible by the developer
   * project that created the metadata.
   */
  public const VISIBILITY_PROJECT = 'PROJECT';
  protected $locationType = DeveloperMetadataLocation::class;
  protected $locationDataType = '';
  /**
   * The spreadsheet-scoped unique ID that identifies the metadata. IDs may be
   * specified when metadata is created, otherwise one will be randomly
   * generated and assigned. Must be positive.
   *
   * @var int
   */
  public $metadataId;
  /**
   * The metadata key. There may be multiple metadata in a spreadsheet with the
   * same key. Developer metadata must always have a key specified.
   *
   * @var string
   */
  public $metadataKey;
  /**
   * Data associated with the metadata's key.
   *
   * @var string
   */
  public $metadataValue;
  /**
   * The metadata visibility. Developer metadata must always have a visibility
   * specified.
   *
   * @var string
   */
  public $visibility;

  /**
   * The location where the metadata is associated.
   *
   * @param DeveloperMetadataLocation $location
   */
  public function setLocation(DeveloperMetadataLocation $location)
  {
    $this->location = $location;
  }
  /**
   * @return DeveloperMetadataLocation
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The spreadsheet-scoped unique ID that identifies the metadata. IDs may be
   * specified when metadata is created, otherwise one will be randomly
   * generated and assigned. Must be positive.
   *
   * @param int $metadataId
   */
  public function setMetadataId($metadataId)
  {
    $this->metadataId = $metadataId;
  }
  /**
   * @return int
   */
  public function getMetadataId()
  {
    return $this->metadataId;
  }
  /**
   * The metadata key. There may be multiple metadata in a spreadsheet with the
   * same key. Developer metadata must always have a key specified.
   *
   * @param string $metadataKey
   */
  public function setMetadataKey($metadataKey)
  {
    $this->metadataKey = $metadataKey;
  }
  /**
   * @return string
   */
  public function getMetadataKey()
  {
    return $this->metadataKey;
  }
  /**
   * Data associated with the metadata's key.
   *
   * @param string $metadataValue
   */
  public function setMetadataValue($metadataValue)
  {
    $this->metadataValue = $metadataValue;
  }
  /**
   * @return string
   */
  public function getMetadataValue()
  {
    return $this->metadataValue;
  }
  /**
   * The metadata visibility. Developer metadata must always have a visibility
   * specified.
   *
   * Accepted values: DEVELOPER_METADATA_VISIBILITY_UNSPECIFIED, DOCUMENT,
   * PROJECT
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeveloperMetadata::class, 'Google_Service_Sheets_DeveloperMetadata');
