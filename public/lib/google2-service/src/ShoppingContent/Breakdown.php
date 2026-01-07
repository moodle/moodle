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

class Breakdown extends \Google\Collection
{
  protected $collection_key = 'regions';
  /**
   * Human readable, localized description of issue's effect on different
   * targets. Should be rendered as a list. For example: * "Products not showing
   * in ads" * "Products not showing organically"
   *
   * @var string[]
   */
  public $details;
  protected $regionsType = BreakdownRegion::class;
  protected $regionsDataType = 'array';

  /**
   * Human readable, localized description of issue's effect on different
   * targets. Should be rendered as a list. For example: * "Products not showing
   * in ads" * "Products not showing organically"
   *
   * @param string[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Lists of regions. Should be rendered as a title for this group of details.
   * The full list should be shown to merchant. If the list is too long, it is
   * recommended to make it expandable.
   *
   * @param BreakdownRegion[] $regions
   */
  public function setRegions($regions)
  {
    $this->regions = $regions;
  }
  /**
   * @return BreakdownRegion[]
   */
  public function getRegions()
  {
    return $this->regions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Breakdown::class, 'Google_Service_ShoppingContent_Breakdown');
