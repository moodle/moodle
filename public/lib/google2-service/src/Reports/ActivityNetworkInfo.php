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

namespace Google\Service\Reports;

class ActivityNetworkInfo extends \Google\Collection
{
  protected $collection_key = 'ipAsn';
  /**
   * IP Address of the user doing the action.
   *
   * @var int[]
   */
  public $ipAsn;
  /**
   * ISO 3166-1 alpha-2 region code of the user doing the action.
   *
   * @var string
   */
  public $regionCode;
  /**
   * ISO 3166-2 region code (states and provinces) for countries of the user
   * doing the action.
   *
   * @var string
   */
  public $subdivisionCode;

  /**
   * IP Address of the user doing the action.
   *
   * @param int[] $ipAsn
   */
  public function setIpAsn($ipAsn)
  {
    $this->ipAsn = $ipAsn;
  }
  /**
   * @return int[]
   */
  public function getIpAsn()
  {
    return $this->ipAsn;
  }
  /**
   * ISO 3166-1 alpha-2 region code of the user doing the action.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * ISO 3166-2 region code (states and provinces) for countries of the user
   * doing the action.
   *
   * @param string $subdivisionCode
   */
  public function setSubdivisionCode($subdivisionCode)
  {
    $this->subdivisionCode = $subdivisionCode;
  }
  /**
   * @return string
   */
  public function getSubdivisionCode()
  {
    return $this->subdivisionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivityNetworkInfo::class, 'Google_Service_Reports_ActivityNetworkInfo');
