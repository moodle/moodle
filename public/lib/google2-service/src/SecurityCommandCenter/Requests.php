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

namespace Google\Service\SecurityCommandCenter;

class Requests extends \Google\Model
{
  /**
   * Allowed RPS (requests per second) over the long term.
   *
   * @var int
   */
  public $longTermAllowed;
  /**
   * Denied RPS (requests per second) over the long term.
   *
   * @var int
   */
  public $longTermDenied;
  /**
   * For 'Increasing deny ratio', the ratio is the denied traffic divided by the
   * allowed traffic. For 'Allowed traffic spike', the ratio is the allowed
   * traffic in the short term divided by allowed traffic in the long term.
   *
   * @var 
   */
  public $ratio;
  /**
   * Allowed RPS (requests per second) in the short term.
   *
   * @var int
   */
  public $shortTermAllowed;

  /**
   * Allowed RPS (requests per second) over the long term.
   *
   * @param int $longTermAllowed
   */
  public function setLongTermAllowed($longTermAllowed)
  {
    $this->longTermAllowed = $longTermAllowed;
  }
  /**
   * @return int
   */
  public function getLongTermAllowed()
  {
    return $this->longTermAllowed;
  }
  /**
   * Denied RPS (requests per second) over the long term.
   *
   * @param int $longTermDenied
   */
  public function setLongTermDenied($longTermDenied)
  {
    $this->longTermDenied = $longTermDenied;
  }
  /**
   * @return int
   */
  public function getLongTermDenied()
  {
    return $this->longTermDenied;
  }
  public function setRatio($ratio)
  {
    $this->ratio = $ratio;
  }
  public function getRatio()
  {
    return $this->ratio;
  }
  /**
   * Allowed RPS (requests per second) in the short term.
   *
   * @param int $shortTermAllowed
   */
  public function setShortTermAllowed($shortTermAllowed)
  {
    $this->shortTermAllowed = $shortTermAllowed;
  }
  /**
   * @return int
   */
  public function getShortTermAllowed()
  {
    return $this->shortTermAllowed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Requests::class, 'Google_Service_SecurityCommandCenter_Requests');
