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

namespace Google\Service\Dfareporting;

class FieldError extends \Google\Collection
{
  /**
   * The ingestion error is unknown.
   */
  public const INGESTION_ERROR_UNKNOWN_PARSING_ERROR = 'UNKNOWN_PARSING_ERROR';
  /**
   * The ingestion error when the ID value is missing.
   */
  public const INGESTION_ERROR_MISSING_ID = 'MISSING_ID';
  /**
   * The ingestion error when the element value name used for reporting is
   * missing.
   */
  public const INGESTION_ERROR_MISSING_REPORTING_LABEL = 'MISSING_REPORTING_LABEL';
  /**
   * The ingestion error when a required value is empty
   */
  public const INGESTION_ERROR_EMPTY_VALUE = 'EMPTY_VALUE';
  /**
   * The ingestion error when asset retrieval fails for a particular image or
   * asset.
   */
  public const INGESTION_ERROR_ASSET_DOWNLOAD_ERROR = 'ASSET_DOWNLOAD_ERROR';
  /**
   * The ingestion error when the ID value exceeds the string length limit.
   */
  public const INGESTION_ERROR_ID_TOO_LONG = 'ID_TOO_LONG';
  /**
   * The ingestion error when the ID value is duplicate.
   */
  public const INGESTION_ERROR_DUPLICATE_ID = 'DUPLICATE_ID';
  /**
   * The ingestion error when parsing the field fails.
   */
  public const INGESTION_ERROR_PARSING_ERROR = 'PARSING_ERROR';
  /**
   * The ingestion error when parsing the country code fails.
   */
  public const INGESTION_ERROR_COUNTRY_PARSING_ERROR = 'COUNTRY_PARSING_ERROR';
  /**
   * The ingestion error when parsing the long value fails.
   */
  public const INGESTION_ERROR_LONG_PARSING_ERROR = 'LONG_PARSING_ERROR';
  /**
   * The ingestion error when parsing the boolean value fails.
   */
  public const INGESTION_ERROR_BOOL_PARSING_ERROR = 'BOOL_PARSING_ERROR';
  /**
   * The ingestion error when parsing the expanded url fails.
   */
  public const INGESTION_ERROR_EXPANDED_URL_PARSING_ERROR = 'EXPANDED_URL_PARSING_ERROR';
  /**
   * The ingestion error when parsing the float value fails.
   */
  public const INGESTION_ERROR_FLOAT_PARSING_ERROR = 'FLOAT_PARSING_ERROR';
  /**
   * The ingestion error when parsing the datetime value fails.
   */
  public const INGESTION_ERROR_DATETIME_PARSING_ERROR = 'DATETIME_PARSING_ERROR';
  /**
   * The ingestion error when the preference value is not a positive float.
   */
  public const INGESTION_ERROR_INVALID_PREFERENCE_VALUE = 'INVALID_PREFERENCE_VALUE';
  /**
   * The ingestion error when a geo location is not found.
   */
  public const INGESTION_ERROR_GEO_NOT_FOUND_ERROR = 'GEO_NOT_FOUND_ERROR';
  /**
   * The ingestion error when parsing the geo field fails.
   */
  public const INGESTION_ERROR_GEO_PARSING_ERROR = 'GEO_PARSING_ERROR';
  /**
   * The ingestion error when a feed row has multiple geotargets with proximity
   * targeting enabled.
   */
  public const INGESTION_ERROR_GEO_PROXIMITY_TARGETING_MULTIPLE_LOCATION_ERROR = 'GEO_PROXIMITY_TARGETING_MULTIPLE_LOCATION_ERROR';
  /**
   * The ingestion error when parsing the postal code value fails.
   */
  public const INGESTION_ERROR_POSTAL_CODE_PARSING_ERROR = 'POSTAL_CODE_PARSING_ERROR';
  /**
   * The ingestion error when parsing the metro code value fails.
   */
  public const INGESTION_ERROR_METRO_CODE_PARSING_ERROR = 'METRO_CODE_PARSING_ERROR';
  /**
   * The ingestion error when parsing the datetime value fails.
   */
  public const INGESTION_ERROR_DATETIME_WITHOUT_TIMEZONE_PARSING_ERROR = 'DATETIME_WITHOUT_TIMEZONE_PARSING_ERROR';
  /**
   * The ingestion error when parsing the weight value fails.
   */
  public const INGESTION_ERROR_WEIGHT_PARSING_ERROR = 'WEIGHT_PARSING_ERROR';
  /**
   * The ingestion error when parsing the creative dimension value fails.
   */
  public const INGESTION_ERROR_CREATIVE_DIMENSION_PARSING_ERROR = 'CREATIVE_DIMENSION_PARSING_ERROR';
  /**
   * The ingestion error when a STRING_LIST type ID has multiple values.
   */
  public const INGESTION_ERROR_MULTIVALUE_ID = 'MULTIVALUE_ID';
  /**
   * The ingestion error when the end time is before the start time.
   */
  public const INGESTION_ERROR_ENDTIME_BEFORE_STARTTIME = 'ENDTIME_BEFORE_STARTTIME';
  /**
   * The ingestion error when the asset library handle is invalid.
   */
  public const INGESTION_ERROR_INVALID_ASSET_LIBRARY_HANDLE = 'INVALID_ASSET_LIBRARY_HANDLE';
  /**
   * The ingestion error when the asset library video handle is invalid.
   */
  public const INGESTION_ERROR_INVALID_ASSET_LIBRARY_VIDEO_HANDLE = 'INVALID_ASSET_LIBRARY_VIDEO_HANDLE';
  /**
   * The ingestion error when the asset library directory handle is invalid.
   */
  public const INGESTION_ERROR_INVALID_ASSET_LIBRARY_DIRECTORY_HANDLE = 'INVALID_ASSET_LIBRARY_DIRECTORY_HANDLE';
  /**
   * The ingestion error when a targeting key used but not defined for the CM360
   * Advertiser.
   */
  public const INGESTION_ERROR_DYNAMIC_TARGETING_KEY_NOT_DEFINED_FOR_ADVERTISER = 'DYNAMIC_TARGETING_KEY_NOT_DEFINED_FOR_ADVERTISER';
  /**
   * The ingestion error when the userlist ID is not accessible for the CM360
   * Advertiser.
   */
  public const INGESTION_ERROR_USERLIST_ID_NOT_ACCESSIBLE_FOR_ADVERTISER = 'USERLIST_ID_NOT_ACCESSIBLE_FOR_ADVERTISER';
  /**
   * The ingestion error when the end time is passed.
   */
  public const INGESTION_ERROR_ENDTIME_PASSED = 'ENDTIME_PASSED';
  /**
   * The ingestion error when the end time is in the near future (i.e., <7
   * days).
   */
  public const INGESTION_ERROR_ENDTIME_TOO_SOON = 'ENDTIME_TOO_SOON';
  /**
   * The ingestion error when a text field specifies a reference to an asset.
   */
  public const INGESTION_ERROR_TEXT_ASSET_REFERENCE = 'TEXT_ASSET_REFERENCE';
  /**
   * The ingestion error when Image field specifies a reference to an asset
   * hosted on SCS (s0.2mdn.net/s0qa.2mdn.net).
   */
  public const INGESTION_ERROR_IMAGE_ASSET_SCS_REFERENCE = 'IMAGE_ASSET_SCS_REFERENCE';
  /**
   * The ingestion error when a geo target is an airport.
   */
  public const INGESTION_ERROR_AIRPORT_GEO_TARGET = 'AIRPORT_GEO_TARGET';
  /**
   * The ingestion error when the geo target's canonical name does not match the
   * query string used to obtain it.
   */
  public const INGESTION_ERROR_CANONICAL_NAME_QUERY_MISMATCH = 'CANONICAL_NAME_QUERY_MISMATCH';
  /**
   * The ingestion error or warning when the default row is not set.
   */
  public const INGESTION_ERROR_NO_DEFAULT_ROW = 'NO_DEFAULT_ROW';
  /**
   * The ingestion error or warning when the default row is not active.
   */
  public const INGESTION_ERROR_NO_ACTIVE_DEFAULT_ROW = 'NO_ACTIVE_DEFAULT_ROW';
  /**
   * The ingestion error or warning when the default row is not in the date
   * range.
   */
  public const INGESTION_ERROR_NO_DEFAULT_ROW_IN_DATE_RANGE = 'NO_DEFAULT_ROW_IN_DATE_RANGE';
  /**
   * The ingestion error or warning when the default row is not in the date
   * range.
   */
  public const INGESTION_ERROR_NO_ACTIVE_DEFAULT_ROW_IN_DATE_RANGE = 'NO_ACTIVE_DEFAULT_ROW_IN_DATE_RANGE';
  /**
   * The ingestion error when when the payload of the record is above a
   * threshold.
   */
  public const INGESTION_ERROR_PAYLOAD_LIMIT_EXCEEDED = 'PAYLOAD_LIMIT_EXCEEDED';
  /**
   * The ingestion error or warning when the field is not SSL compliant.
   */
  public const INGESTION_ERROR_SSL_NOT_COMPLIANT = 'SSL_NOT_COMPLIANT';
  protected $collection_key = 'fieldValues';
  /**
   * Output only. The ID of the field.
   *
   * @var int
   */
  public $fieldId;
  /**
   * Output only. The name of the field.
   *
   * @var string
   */
  public $fieldName;
  /**
   * Output only. The list of values of the field.
   *
   * @var string[]
   */
  public $fieldValues;
  /**
   * Output only. The ingestion error of the field.
   *
   * @var string
   */
  public $ingestionError;
  /**
   * Output only. Incidcates whether the field has error or warning.
   *
   * @var bool
   */
  public $isError;

  /**
   * Output only. The ID of the field.
   *
   * @param int $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return int
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * Output only. The name of the field.
   *
   * @param string $fieldName
   */
  public function setFieldName($fieldName)
  {
    $this->fieldName = $fieldName;
  }
  /**
   * @return string
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }
  /**
   * Output only. The list of values of the field.
   *
   * @param string[] $fieldValues
   */
  public function setFieldValues($fieldValues)
  {
    $this->fieldValues = $fieldValues;
  }
  /**
   * @return string[]
   */
  public function getFieldValues()
  {
    return $this->fieldValues;
  }
  /**
   * Output only. The ingestion error of the field.
   *
   * Accepted values: UNKNOWN_PARSING_ERROR, MISSING_ID,
   * MISSING_REPORTING_LABEL, EMPTY_VALUE, ASSET_DOWNLOAD_ERROR, ID_TOO_LONG,
   * DUPLICATE_ID, PARSING_ERROR, COUNTRY_PARSING_ERROR, LONG_PARSING_ERROR,
   * BOOL_PARSING_ERROR, EXPANDED_URL_PARSING_ERROR, FLOAT_PARSING_ERROR,
   * DATETIME_PARSING_ERROR, INVALID_PREFERENCE_VALUE, GEO_NOT_FOUND_ERROR,
   * GEO_PARSING_ERROR, GEO_PROXIMITY_TARGETING_MULTIPLE_LOCATION_ERROR,
   * POSTAL_CODE_PARSING_ERROR, METRO_CODE_PARSING_ERROR,
   * DATETIME_WITHOUT_TIMEZONE_PARSING_ERROR, WEIGHT_PARSING_ERROR,
   * CREATIVE_DIMENSION_PARSING_ERROR, MULTIVALUE_ID, ENDTIME_BEFORE_STARTTIME,
   * INVALID_ASSET_LIBRARY_HANDLE, INVALID_ASSET_LIBRARY_VIDEO_HANDLE,
   * INVALID_ASSET_LIBRARY_DIRECTORY_HANDLE,
   * DYNAMIC_TARGETING_KEY_NOT_DEFINED_FOR_ADVERTISER,
   * USERLIST_ID_NOT_ACCESSIBLE_FOR_ADVERTISER, ENDTIME_PASSED,
   * ENDTIME_TOO_SOON, TEXT_ASSET_REFERENCE, IMAGE_ASSET_SCS_REFERENCE,
   * AIRPORT_GEO_TARGET, CANONICAL_NAME_QUERY_MISMATCH, NO_DEFAULT_ROW,
   * NO_ACTIVE_DEFAULT_ROW, NO_DEFAULT_ROW_IN_DATE_RANGE,
   * NO_ACTIVE_DEFAULT_ROW_IN_DATE_RANGE, PAYLOAD_LIMIT_EXCEEDED,
   * SSL_NOT_COMPLIANT
   *
   * @param self::INGESTION_ERROR_* $ingestionError
   */
  public function setIngestionError($ingestionError)
  {
    $this->ingestionError = $ingestionError;
  }
  /**
   * @return self::INGESTION_ERROR_*
   */
  public function getIngestionError()
  {
    return $this->ingestionError;
  }
  /**
   * Output only. Incidcates whether the field has error or warning.
   *
   * @param bool $isError
   */
  public function setIsError($isError)
  {
    $this->isError = $isError;
  }
  /**
   * @return bool
   */
  public function getIsError()
  {
    return $this->isError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldError::class, 'Google_Service_Dfareporting_FieldError');
