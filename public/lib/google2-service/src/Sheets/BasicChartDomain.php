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

class BasicChartDomain extends \Google\Model
{
  protected $domainType = ChartData::class;
  protected $domainDataType = '';
  /**
   * True to reverse the order of the domain values (horizontal axis).
   *
   * @var bool
   */
  public $reversed;

  /**
   * The data of the domain. For example, if charting stock prices over time,
   * this is the data representing the dates.
   *
   * @param ChartData $domain
   */
  public function setDomain(ChartData $domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return ChartData
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * True to reverse the order of the domain values (horizontal axis).
   *
   * @param bool $reversed
   */
  public function setReversed($reversed)
  {
    $this->reversed = $reversed;
  }
  /**
   * @return bool
   */
  public function getReversed()
  {
    return $this->reversed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicChartDomain::class, 'Google_Service_Sheets_BasicChartDomain');
