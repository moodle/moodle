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

class Values extends \Google\Model
{
  /**
   * Required. First recall bit value.
   *
   * @var bool
   */
  public $bitFirst;
  /**
   * Required. Second recall bit value.
   *
   * @var bool
   */
  public $bitSecond;
  /**
   * Required. Third recall bit value.
   *
   * @var bool
   */
  public $bitThird;

  /**
   * Required. First recall bit value.
   *
   * @param bool $bitFirst
   */
  public function setBitFirst($bitFirst)
  {
    $this->bitFirst = $bitFirst;
  }
  /**
   * @return bool
   */
  public function getBitFirst()
  {
    return $this->bitFirst;
  }
  /**
   * Required. Second recall bit value.
   *
   * @param bool $bitSecond
   */
  public function setBitSecond($bitSecond)
  {
    $this->bitSecond = $bitSecond;
  }
  /**
   * @return bool
   */
  public function getBitSecond()
  {
    return $this->bitSecond;
  }
  /**
   * Required. Third recall bit value.
   *
   * @param bool $bitThird
   */
  public function setBitThird($bitThird)
  {
    $this->bitThird = $bitThird;
  }
  /**
   * @return bool
   */
  public function getBitThird()
  {
    return $this->bitThird;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Values::class, 'Google_Service_PlayIntegrity_Values');
