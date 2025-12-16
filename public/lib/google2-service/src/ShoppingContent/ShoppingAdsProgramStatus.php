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

namespace Google\Service\ShoppingContent;

class ShoppingAdsProgramStatus extends \Google\Collection
{
  /**
   * State is unknown.
   */
  public const GLOBAL_STATE_PROGRAM_STATE_UNSPECIFIED = 'PROGRAM_STATE_UNSPECIFIED';
  /**
   * Program is not enabled for any country.
   */
  public const GLOBAL_STATE_NOT_ENABLED = 'NOT_ENABLED';
  /**
   * No products have been uploaded for any region. Upload products to Merchant
   * Center.
   */
  public const GLOBAL_STATE_NO_OFFERS_UPLOADED = 'NO_OFFERS_UPLOADED';
  /**
   * Program is enabled and offers are uploaded for at least one country.
   */
  public const GLOBAL_STATE_ENABLED = 'ENABLED';
  protected $collection_key = 'regionStatuses';
  /**
   * State of the program. `ENABLED` if there are offers for at least one
   * region.
   *
   * @var string
   */
  public $globalState;
  protected $regionStatusesType = ShoppingAdsProgramStatusRegionStatus::class;
  protected $regionStatusesDataType = 'array';

  /**
   * State of the program. `ENABLED` if there are offers for at least one
   * region.
   *
   * Accepted values: PROGRAM_STATE_UNSPECIFIED, NOT_ENABLED,
   * NO_OFFERS_UPLOADED, ENABLED
   *
   * @param self::GLOBAL_STATE_* $globalState
   */
  public function setGlobalState($globalState)
  {
    $this->globalState = $globalState;
  }
  /**
   * @return self::GLOBAL_STATE_*
   */
  public function getGlobalState()
  {
    return $this->globalState;
  }
  /**
   * Status of the program in each region. Regions with the same status and
   * review eligibility are grouped together in `regionCodes`.
   *
   * @param ShoppingAdsProgramStatusRegionStatus[] $regionStatuses
   */
  public function setRegionStatuses($regionStatuses)
  {
    $this->regionStatuses = $regionStatuses;
  }
  /**
   * @return ShoppingAdsProgramStatusRegionStatus[]
   */
  public function getRegionStatuses()
  {
    return $this->regionStatuses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShoppingAdsProgramStatus::class, 'Google_Service_ShoppingContent_ShoppingAdsProgramStatus');
