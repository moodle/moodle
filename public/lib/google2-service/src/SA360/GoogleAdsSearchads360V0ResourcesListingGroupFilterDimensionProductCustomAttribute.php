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

class GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCustomAttribute extends \Google\Model
{
  /**
   * Not specified.
   */
  public const INDEX_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const INDEX_UNKNOWN = 'UNKNOWN';
  /**
   * First listing group filter custom attribute.
   */
  public const INDEX_INDEX0 = 'INDEX0';
  /**
   * Second listing group filter custom attribute.
   */
  public const INDEX_INDEX1 = 'INDEX1';
  /**
   * Third listing group filter custom attribute.
   */
  public const INDEX_INDEX2 = 'INDEX2';
  /**
   * Fourth listing group filter custom attribute.
   */
  public const INDEX_INDEX3 = 'INDEX3';
  /**
   * Fifth listing group filter custom attribute.
   */
  public const INDEX_INDEX4 = 'INDEX4';
  /**
   * Indicates the index of the custom attribute.
   *
   * @var string
   */
  public $index;
  /**
   * String value of the product custom attribute.
   *
   * @var string
   */
  public $value;

  /**
   * Indicates the index of the custom attribute.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, INDEX0, INDEX1, INDEX2, INDEX3,
   * INDEX4
   *
   * @param self::INDEX_* $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return self::INDEX_*
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * String value of the product custom attribute.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCustomAttribute::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCustomAttribute');
