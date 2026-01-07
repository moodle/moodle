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

namespace Google\Service\AnalyticsData;

class DimensionMetadata extends \Google\Collection
{
  protected $collection_key = 'deprecatedApiNames';
  /**
   * This dimension's name. Useable in [Dimension](#Dimension)'s `name`. For
   * example, `eventName`.
   *
   * @var string
   */
  public $apiName;
  /**
   * The display name of the category that this dimension belongs to. Similar
   * dimensions and metrics are categorized together.
   *
   * @var string
   */
  public $category;
  /**
   * True if the dimension is custom to this property. This includes user,
   * event, & item scoped custom dimensions; to learn more about custom
   * dimensions, see https://support.google.com/analytics/answer/14240153. This
   * also include custom channel groups; to learn more about custom channel
   * groups, see https://support.google.com/analytics/answer/13051316.
   *
   * @var bool
   */
  public $customDefinition;
  /**
   * Still usable but deprecated names for this dimension. If populated, this
   * dimension is available by either `apiName` or one of `deprecatedApiNames`
   * for a period of time. After the deprecation period, the dimension will be
   * available only by `apiName`.
   *
   * @var string[]
   */
  public $deprecatedApiNames;
  /**
   * Description of how this dimension is used and calculated.
   *
   * @var string
   */
  public $description;
  /**
   * This dimension's name within the Google Analytics user interface. For
   * example, `Event name`.
   *
   * @var string
   */
  public $uiName;

  /**
   * This dimension's name. Useable in [Dimension](#Dimension)'s `name`. For
   * example, `eventName`.
   *
   * @param string $apiName
   */
  public function setApiName($apiName)
  {
    $this->apiName = $apiName;
  }
  /**
   * @return string
   */
  public function getApiName()
  {
    return $this->apiName;
  }
  /**
   * The display name of the category that this dimension belongs to. Similar
   * dimensions and metrics are categorized together.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * True if the dimension is custom to this property. This includes user,
   * event, & item scoped custom dimensions; to learn more about custom
   * dimensions, see https://support.google.com/analytics/answer/14240153. This
   * also include custom channel groups; to learn more about custom channel
   * groups, see https://support.google.com/analytics/answer/13051316.
   *
   * @param bool $customDefinition
   */
  public function setCustomDefinition($customDefinition)
  {
    $this->customDefinition = $customDefinition;
  }
  /**
   * @return bool
   */
  public function getCustomDefinition()
  {
    return $this->customDefinition;
  }
  /**
   * Still usable but deprecated names for this dimension. If populated, this
   * dimension is available by either `apiName` or one of `deprecatedApiNames`
   * for a period of time. After the deprecation period, the dimension will be
   * available only by `apiName`.
   *
   * @param string[] $deprecatedApiNames
   */
  public function setDeprecatedApiNames($deprecatedApiNames)
  {
    $this->deprecatedApiNames = $deprecatedApiNames;
  }
  /**
   * @return string[]
   */
  public function getDeprecatedApiNames()
  {
    return $this->deprecatedApiNames;
  }
  /**
   * Description of how this dimension is used and calculated.
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
   * This dimension's name within the Google Analytics user interface. For
   * example, `Event name`.
   *
   * @param string $uiName
   */
  public function setUiName($uiName)
  {
    $this->uiName = $uiName;
  }
  /**
   * @return string
   */
  public function getUiName()
  {
    return $this->uiName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DimensionMetadata::class, 'Google_Service_AnalyticsData_DimensionMetadata');
