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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2TransformationDescription extends \Google\Model
{
  /**
   * Unused
   */
  public const TYPE_TRANSFORMATION_TYPE_UNSPECIFIED = 'TRANSFORMATION_TYPE_UNSPECIFIED';
  /**
   * Record suppression
   */
  public const TYPE_RECORD_SUPPRESSION = 'RECORD_SUPPRESSION';
  /**
   * Replace value
   */
  public const TYPE_REPLACE_VALUE = 'REPLACE_VALUE';
  /**
   * Replace value using a dictionary.
   */
  public const TYPE_REPLACE_DICTIONARY = 'REPLACE_DICTIONARY';
  /**
   * Redact
   */
  public const TYPE_REDACT = 'REDACT';
  /**
   * Character mask
   */
  public const TYPE_CHARACTER_MASK = 'CHARACTER_MASK';
  /**
   * FFX-FPE
   */
  public const TYPE_CRYPTO_REPLACE_FFX_FPE = 'CRYPTO_REPLACE_FFX_FPE';
  /**
   * Fixed size bucketing
   */
  public const TYPE_FIXED_SIZE_BUCKETING = 'FIXED_SIZE_BUCKETING';
  /**
   * Bucketing
   */
  public const TYPE_BUCKETING = 'BUCKETING';
  /**
   * Replace with info type
   */
  public const TYPE_REPLACE_WITH_INFO_TYPE = 'REPLACE_WITH_INFO_TYPE';
  /**
   * Time part
   */
  public const TYPE_TIME_PART = 'TIME_PART';
  /**
   * Crypto hash
   */
  public const TYPE_CRYPTO_HASH = 'CRYPTO_HASH';
  /**
   * Date shift
   */
  public const TYPE_DATE_SHIFT = 'DATE_SHIFT';
  /**
   * Deterministic crypto
   */
  public const TYPE_CRYPTO_DETERMINISTIC_CONFIG = 'CRYPTO_DETERMINISTIC_CONFIG';
  /**
   * Redact image
   */
  public const TYPE_REDACT_IMAGE = 'REDACT_IMAGE';
  /**
   * A human-readable string representation of the `RecordCondition`
   * corresponding to this transformation. Set if a `RecordCondition` was used
   * to determine whether or not to apply this transformation. Examples: *
   * (age_field > 85) * (age_field <= 18) * (zip_field exists) * (zip_field ==
   * 01234) && (city_field != "Springville") * (zip_field == 01234) &&
   * (age_field <= 18) && (city_field exists)
   *
   * @var string
   */
  public $condition;
  /**
   * A description of the transformation. This is empty for a
   * RECORD_SUPPRESSION, or is the output of calling toString() on the
   * `PrimitiveTransformation` protocol buffer message for any other type of
   * transformation.
   *
   * @var string
   */
  public $description;
  protected $infoTypeType = GooglePrivacyDlpV2InfoType::class;
  protected $infoTypeDataType = '';
  /**
   * The transformation type.
   *
   * @var string
   */
  public $type;

  /**
   * A human-readable string representation of the `RecordCondition`
   * corresponding to this transformation. Set if a `RecordCondition` was used
   * to determine whether or not to apply this transformation. Examples: *
   * (age_field > 85) * (age_field <= 18) * (zip_field exists) * (zip_field ==
   * 01234) && (city_field != "Springville") * (zip_field == 01234) &&
   * (age_field <= 18) && (city_field exists)
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * A description of the transformation. This is empty for a
   * RECORD_SUPPRESSION, or is the output of calling toString() on the
   * `PrimitiveTransformation` protocol buffer message for any other type of
   * transformation.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Set if the transformation was limited to a specific `InfoType`.
   *
   * @param GooglePrivacyDlpV2InfoType $infoType
   */
  public function setInfoType(GooglePrivacyDlpV2InfoType $infoType)
  {
    $this->infoType = $infoType;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType
   */
  public function getInfoType()
  {
    return $this->infoType;
  }
  /**
   * The transformation type.
   *
   * Accepted values: TRANSFORMATION_TYPE_UNSPECIFIED, RECORD_SUPPRESSION,
   * REPLACE_VALUE, REPLACE_DICTIONARY, REDACT, CHARACTER_MASK,
   * CRYPTO_REPLACE_FFX_FPE, FIXED_SIZE_BUCKETING, BUCKETING,
   * REPLACE_WITH_INFO_TYPE, TIME_PART, CRYPTO_HASH, DATE_SHIFT,
   * CRYPTO_DETERMINISTIC_CONFIG, REDACT_IMAGE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TransformationDescription::class, 'Google_Service_DLP_GooglePrivacyDlpV2TransformationDescription');
