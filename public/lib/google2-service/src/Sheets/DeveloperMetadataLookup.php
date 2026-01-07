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

class DeveloperMetadataLookup extends \Google\Model
{
  /**
   * Default value. This value must not be used.
   */
  public const LOCATION_MATCHING_STRATEGY_DEVELOPER_METADATA_LOCATION_MATCHING_STRATEGY_UNSPECIFIED = 'DEVELOPER_METADATA_LOCATION_MATCHING_STRATEGY_UNSPECIFIED';
  /**
   * Indicates that a specified location should be matched exactly. For example,
   * if row three were specified as a location this matching strategy would only
   * match developer metadata also associated on row three. Metadata associated
   * on other locations would not be considered.
   */
  public const LOCATION_MATCHING_STRATEGY_EXACT_LOCATION = 'EXACT_LOCATION';
  /**
   * Indicates that a specified location should match that exact location as
   * well as any intersecting locations. For example, if row three were
   * specified as a location this matching strategy would match developer
   * metadata associated on row three as well as metadata associated on
   * locations that intersect row three. If, for instance, there was developer
   * metadata associated on column B, this matching strategy would also match
   * that location because column B intersects row three.
   */
  public const LOCATION_MATCHING_STRATEGY_INTERSECTING_LOCATION = 'INTERSECTING_LOCATION';
  /**
   * Default value.
   */
  public const LOCATION_TYPE_DEVELOPER_METADATA_LOCATION_TYPE_UNSPECIFIED = 'DEVELOPER_METADATA_LOCATION_TYPE_UNSPECIFIED';
  /**
   * Developer metadata associated on an entire row dimension.
   */
  public const LOCATION_TYPE_ROW = 'ROW';
  /**
   * Developer metadata associated on an entire column dimension.
   */
  public const LOCATION_TYPE_COLUMN = 'COLUMN';
  /**
   * Developer metadata associated on an entire sheet.
   */
  public const LOCATION_TYPE_SHEET = 'SHEET';
  /**
   * Developer metadata associated on the entire spreadsheet.
   */
  public const LOCATION_TYPE_SPREADSHEET = 'SPREADSHEET';
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
  /**
   * Determines how this lookup matches the location. If this field is specified
   * as EXACT, only developer metadata associated on the exact location
   * specified is matched. If this field is specified to INTERSECTING, developer
   * metadata associated on intersecting locations is also matched. If left
   * unspecified, this field assumes a default value of INTERSECTING. If this
   * field is specified, a metadataLocation must also be specified.
   *
   * @var string
   */
  public $locationMatchingStrategy;
  /**
   * Limits the selected developer metadata to those entries which are
   * associated with locations of the specified type. For example, when this
   * field is specified as ROW this lookup only considers developer metadata
   * associated on rows. If the field is left unspecified, all location types
   * are considered. This field cannot be specified as SPREADSHEET when the
   * locationMatchingStrategy is specified as INTERSECTING or when the
   * metadataLocation is specified as a non-spreadsheet location: spreadsheet
   * metadata cannot intersect any other developer metadata location. This field
   * also must be left unspecified when the locationMatchingStrategy is
   * specified as EXACT.
   *
   * @var string
   */
  public $locationType;
  /**
   * Limits the selected developer metadata to that which has a matching
   * DeveloperMetadata.metadata_id.
   *
   * @var int
   */
  public $metadataId;
  /**
   * Limits the selected developer metadata to that which has a matching
   * DeveloperMetadata.metadata_key.
   *
   * @var string
   */
  public $metadataKey;
  protected $metadataLocationType = DeveloperMetadataLocation::class;
  protected $metadataLocationDataType = '';
  /**
   * Limits the selected developer metadata to that which has a matching
   * DeveloperMetadata.metadata_value.
   *
   * @var string
   */
  public $metadataValue;
  /**
   * Limits the selected developer metadata to that which has a matching
   * DeveloperMetadata.visibility. If left unspecified, all developer metadata
   * visible to the requesting project is considered.
   *
   * @var string
   */
  public $visibility;

  /**
   * Determines how this lookup matches the location. If this field is specified
   * as EXACT, only developer metadata associated on the exact location
   * specified is matched. If this field is specified to INTERSECTING, developer
   * metadata associated on intersecting locations is also matched. If left
   * unspecified, this field assumes a default value of INTERSECTING. If this
   * field is specified, a metadataLocation must also be specified.
   *
   * Accepted values: DEVELOPER_METADATA_LOCATION_MATCHING_STRATEGY_UNSPECIFIED,
   * EXACT_LOCATION, INTERSECTING_LOCATION
   *
   * @param self::LOCATION_MATCHING_STRATEGY_* $locationMatchingStrategy
   */
  public function setLocationMatchingStrategy($locationMatchingStrategy)
  {
    $this->locationMatchingStrategy = $locationMatchingStrategy;
  }
  /**
   * @return self::LOCATION_MATCHING_STRATEGY_*
   */
  public function getLocationMatchingStrategy()
  {
    return $this->locationMatchingStrategy;
  }
  /**
   * Limits the selected developer metadata to those entries which are
   * associated with locations of the specified type. For example, when this
   * field is specified as ROW this lookup only considers developer metadata
   * associated on rows. If the field is left unspecified, all location types
   * are considered. This field cannot be specified as SPREADSHEET when the
   * locationMatchingStrategy is specified as INTERSECTING or when the
   * metadataLocation is specified as a non-spreadsheet location: spreadsheet
   * metadata cannot intersect any other developer metadata location. This field
   * also must be left unspecified when the locationMatchingStrategy is
   * specified as EXACT.
   *
   * Accepted values: DEVELOPER_METADATA_LOCATION_TYPE_UNSPECIFIED, ROW, COLUMN,
   * SHEET, SPREADSHEET
   *
   * @param self::LOCATION_TYPE_* $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return self::LOCATION_TYPE_*
   */
  public function getLocationType()
  {
    return $this->locationType;
  }
  /**
   * Limits the selected developer metadata to that which has a matching
   * DeveloperMetadata.metadata_id.
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
   * Limits the selected developer metadata to that which has a matching
   * DeveloperMetadata.metadata_key.
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
   * Limits the selected developer metadata to those entries associated with the
   * specified location. This field either matches exact locations or all
   * intersecting locations according the specified locationMatchingStrategy.
   *
   * @param DeveloperMetadataLocation $metadataLocation
   */
  public function setMetadataLocation(DeveloperMetadataLocation $metadataLocation)
  {
    $this->metadataLocation = $metadataLocation;
  }
  /**
   * @return DeveloperMetadataLocation
   */
  public function getMetadataLocation()
  {
    return $this->metadataLocation;
  }
  /**
   * Limits the selected developer metadata to that which has a matching
   * DeveloperMetadata.metadata_value.
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
   * Limits the selected developer metadata to that which has a matching
   * DeveloperMetadata.visibility. If left unspecified, all developer metadata
   * visible to the requesting project is considered.
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
class_alias(DeveloperMetadataLookup::class, 'Google_Service_Sheets_DeveloperMetadataLookup');
