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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesConversionCustomVariable extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const CARDINALITY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const CARDINALITY_UNKNOWN = 'UNKNOWN';
  /**
   * The conversion custom variable has cardinality below all limits. The
   * variable can be used for segmentation, and stats can accrue for new values
   * if the variable is enabled.
   */
  public const CARDINALITY_BELOW_ALL_LIMITS = 'BELOW_ALL_LIMITS';
  /**
   * The conversion custom variable has cardinality that exceeds the
   * segmentation limit, but does not exceed the stats limit. Segmentation will
   * be disabled, but stats can accrue for new values if the variable is
   * enabled.
   */
  public const CARDINALITY_EXCEEDS_SEGMENTATION_LIMIT_BUT_NOT_STATS_LIMIT = 'EXCEEDS_SEGMENTATION_LIMIT_BUT_NOT_STATS_LIMIT';
  /**
   * The conversion custom variable has exceeded the segmentation limits, and is
   * approaching the stats limits (> 90%). Segmentation will be disabled, but
   * stats can accrue for new values if the variable is enabled.
   */
  public const CARDINALITY_APPROACHES_STATS_LIMIT = 'APPROACHES_STATS_LIMIT';
  /**
   * The conversion custom variable has exceeded both the segmentation limits
   * and stats limits. Segmentation will be disabled, and stats for enabled
   * variables can accrue only if the existing values do not increase the
   * cardinality of the variable any further.
   */
  public const CARDINALITY_EXCEEDS_STATS_LIMIT = 'EXCEEDS_STATS_LIMIT';
  /**
   * Not specified.
   */
  public const FAMILY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const FAMILY_UNKNOWN = 'UNKNOWN';
  /**
   * The standard conversion custom variable. Customers are required to activate
   * before use.
   */
  public const FAMILY_STANDARD = 'STANDARD';
  /**
   * The conversion custom variable imported from a custom floodlight variable.
   */
  public const FAMILY_FLOODLIGHT = 'FLOODLIGHT';
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The conversion custom variable is pending activation and will not accrue
   * stats until set to ENABLED. This status can't be used in CREATE and UPDATE
   * requests.
   */
  public const STATUS_ACTIVATION_NEEDED = 'ACTIVATION_NEEDED';
  /**
   * The conversion custom variable is enabled and will accrue stats.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The conversion custom variable is paused and will not accrue stats until
   * set to ENABLED again.
   */
  public const STATUS_PAUSED = 'PAUSED';
  protected $collection_key = 'customColumnIds';
  /**
   * Output only. Cardinality of the conversion custom variable.
   *
   * @var string
   */
  public $cardinality;
  /**
   * Output only. The IDs of custom columns that use this conversion custom
   * variable.
   *
   * @var string[]
   */
  public $customColumnIds;
  /**
   * Output only. Family of the conversion custom variable.
   *
   * @var string
   */
  public $family;
  protected $floodlightConversionCustomVariableInfoType = GoogleAdsSearchads360V0ResourcesConversionCustomVariableFloodlightConversionCustomVariableInfo::class;
  protected $floodlightConversionCustomVariableInfoDataType = '';
  /**
   * Output only. The ID of the conversion custom variable.
   *
   * @var string
   */
  public $id;
  /**
   * Required. The name of the conversion custom variable. Name should be
   * unique. The maximum length of name is 100 characters. There should not be
   * any extra spaces before and after.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the customer that owns the conversion
   * custom variable.
   *
   * @var string
   */
  public $ownerCustomer;
  /**
   * Immutable. The resource name of the conversion custom variable. Conversion
   * custom variable resource names have the form: `customers/{customer_id}/conv
   * ersionCustomVariables/{conversion_custom_variable_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * The status of the conversion custom variable for conversion event accrual.
   *
   * @var string
   */
  public $status;
  /**
   * Required. Immutable. The tag of the conversion custom variable. Tag should
   * be unique and consist of a "u" character directly followed with a number
   * less than ormequal to 100. For example: "u4".
   *
   * @var string
   */
  public $tag;

  /**
   * Output only. Cardinality of the conversion custom variable.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, BELOW_ALL_LIMITS,
   * EXCEEDS_SEGMENTATION_LIMIT_BUT_NOT_STATS_LIMIT, APPROACHES_STATS_LIMIT,
   * EXCEEDS_STATS_LIMIT
   *
   * @param self::CARDINALITY_* $cardinality
   */
  public function setCardinality($cardinality)
  {
    $this->cardinality = $cardinality;
  }
  /**
   * @return self::CARDINALITY_*
   */
  public function getCardinality()
  {
    return $this->cardinality;
  }
  /**
   * Output only. The IDs of custom columns that use this conversion custom
   * variable.
   *
   * @param string[] $customColumnIds
   */
  public function setCustomColumnIds($customColumnIds)
  {
    $this->customColumnIds = $customColumnIds;
  }
  /**
   * @return string[]
   */
  public function getCustomColumnIds()
  {
    return $this->customColumnIds;
  }
  /**
   * Output only. Family of the conversion custom variable.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, STANDARD, FLOODLIGHT
   *
   * @param self::FAMILY_* $family
   */
  public function setFamily($family)
  {
    $this->family = $family;
  }
  /**
   * @return self::FAMILY_*
   */
  public function getFamily()
  {
    return $this->family;
  }
  /**
   * Output only. Fields for Search Ads 360 floodlight conversion custom
   * variables.
   *
   * @param GoogleAdsSearchads360V0ResourcesConversionCustomVariableFloodlightConversionCustomVariableInfo $floodlightConversionCustomVariableInfo
   */
  public function setFloodlightConversionCustomVariableInfo(GoogleAdsSearchads360V0ResourcesConversionCustomVariableFloodlightConversionCustomVariableInfo $floodlightConversionCustomVariableInfo)
  {
    $this->floodlightConversionCustomVariableInfo = $floodlightConversionCustomVariableInfo;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesConversionCustomVariableFloodlightConversionCustomVariableInfo
   */
  public function getFloodlightConversionCustomVariableInfo()
  {
    return $this->floodlightConversionCustomVariableInfo;
  }
  /**
   * Output only. The ID of the conversion custom variable.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. The name of the conversion custom variable. Name should be
   * unique. The maximum length of name is 100 characters. There should not be
   * any extra spaces before and after.
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
   * Output only. The resource name of the customer that owns the conversion
   * custom variable.
   *
   * @param string $ownerCustomer
   */
  public function setOwnerCustomer($ownerCustomer)
  {
    $this->ownerCustomer = $ownerCustomer;
  }
  /**
   * @return string
   */
  public function getOwnerCustomer()
  {
    return $this->ownerCustomer;
  }
  /**
   * Immutable. The resource name of the conversion custom variable. Conversion
   * custom variable resource names have the form: `customers/{customer_id}/conv
   * ersionCustomVariables/{conversion_custom_variable_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * The status of the conversion custom variable for conversion event accrual.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ACTIVATION_NEEDED, ENABLED, PAUSED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Required. Immutable. The tag of the conversion custom variable. Tag should
   * be unique and consist of a "u" character directly followed with a number
   * less than ormequal to 100. For example: "u4".
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesConversionCustomVariable::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesConversionCustomVariable');
