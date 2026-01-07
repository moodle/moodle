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

class GoogleCloudApigeeV1RateRange extends \Google\Model
{
  /**
   * Ending value of the range. Set to 0 or `null` for the last range of values.
   *
   * @var string
   */
  public $end;
  protected $feeType = GoogleTypeMoney::class;
  protected $feeDataType = '';
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
  /**
   * Fee to charge when total number of API calls falls within this range.
   *
   * @param GoogleTypeMoney $fee
   */
  public function setFee(GoogleTypeMoney $fee)
  {
    $this->fee = $fee;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getFee()
  {
    return $this->fee;
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
class_alias(GoogleCloudApigeeV1RateRange::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RateRange');
