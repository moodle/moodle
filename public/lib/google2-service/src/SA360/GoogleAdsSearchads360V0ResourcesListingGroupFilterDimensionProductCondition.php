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

class GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCondition extends \Google\Model
{
  /**
   * Not specified.
   */
  public const CONDITION_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const CONDITION_UNKNOWN = 'UNKNOWN';
  /**
   * The product condition is new.
   */
  public const CONDITION_NEW = 'NEW';
  /**
   * The product condition is refurbished.
   */
  public const CONDITION_REFURBISHED = 'REFURBISHED';
  /**
   * The product condition is used.
   */
  public const CONDITION_USED = 'USED';
  /**
   * Value of the condition.
   *
   * @var string
   */
  public $condition;

  /**
   * Value of the condition.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, NEW, REFURBISHED, USED
   *
   * @param self::CONDITION_* $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return self::CONDITION_*
   */
  public function getCondition()
  {
    return $this->condition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCondition::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesListingGroupFilterDimensionProductCondition');
