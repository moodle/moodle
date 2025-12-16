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

namespace Google\Service\PlayIntegrity;

class WriteDates extends \Google\Model
{
  /**
   * Optional. Write time in YYYYMM format (in UTC, e.g. 202402) for the first
   * bit. Note that this value won't be set if the first bit is false.
   *
   * @var int
   */
  public $yyyymmFirst;
  /**
   * Optional. Write time in YYYYMM format (in UTC, e.g. 202402) for the second
   * bit. Note that this value won't be set if the second bit is false.
   *
   * @var int
   */
  public $yyyymmSecond;
  /**
   * Optional. Write time in YYYYMM format (in UTC, e.g. 202402) for the third
   * bit. Note that this value won't be set if the third bit is false.
   *
   * @var int
   */
  public $yyyymmThird;

  /**
   * Optional. Write time in YYYYMM format (in UTC, e.g. 202402) for the first
   * bit. Note that this value won't be set if the first bit is false.
   *
   * @param int $yyyymmFirst
   */
  public function setYyyymmFirst($yyyymmFirst)
  {
    $this->yyyymmFirst = $yyyymmFirst;
  }
  /**
   * @return int
   */
  public function getYyyymmFirst()
  {
    return $this->yyyymmFirst;
  }
  /**
   * Optional. Write time in YYYYMM format (in UTC, e.g. 202402) for the second
   * bit. Note that this value won't be set if the second bit is false.
   *
   * @param int $yyyymmSecond
   */
  public function setYyyymmSecond($yyyymmSecond)
  {
    $this->yyyymmSecond = $yyyymmSecond;
  }
  /**
   * @return int
   */
  public function getYyyymmSecond()
  {
    return $this->yyyymmSecond;
  }
  /**
   * Optional. Write time in YYYYMM format (in UTC, e.g. 202402) for the third
   * bit. Note that this value won't be set if the third bit is false.
   *
   * @param int $yyyymmThird
   */
  public function setYyyymmThird($yyyymmThird)
  {
    $this->yyyymmThird = $yyyymmThird;
  }
  /**
   * @return int
   */
  public function getYyyymmThird()
  {
    return $this->yyyymmThird;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WriteDates::class, 'Google_Service_PlayIntegrity_WriteDates');
