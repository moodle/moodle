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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1RevenueShareRange extends \Google\Model
{
  /**
   * Ending value of the range. Set to 0 or `null` for the last range of values.
   *
   * @var string
   */
  public $end;
  /**
   * Percentage of the revenue to be shared with the developer. For example, to
   * share 21 percent of the total revenue with the developer, set this value to
   * 21. Specify a decimal number with a maximum of two digits following the
   * decimal point.
   *
   * @var 
   */
  public $sharePercentage;
  /**
   * Starting value of the range. Set to 0 or `null` for the initial range of
   * values.
   *
   * @var string
   */
  public $start;

  /**
   * Ending value of the range. Set to 0 or `null` for the last range of values.
   *
   * @param string $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return string
   */
  public function getEnd()
  {
    return $this->end;
  }
  public function setSharePercentage($sharePercentage)
  {
    $this->sharePercentage = $sharePercentage;
  }
  public function getSharePercentage()
  {
    return $this->sharePercentage;
  }
  /**
   * Starting value of the range. Set to 0 or `null` for the initial range of
   * values.
   *
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1RevenueShareRange::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RevenueShareRange');
