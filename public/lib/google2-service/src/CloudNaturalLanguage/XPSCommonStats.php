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

namespace Google\Service\CloudNaturalLanguage;

class XPSCommonStats extends \Google\Model
{
  /**
   * @var string
   */
  public $distinctValueCount;
  /**
   * @var string
   */
  public $nullValueCount;
  /**
   * @var string
   */
  public $validValueCount;

  /**
   * @param string $distinctValueCount
   */
  public function setDistinctValueCount($distinctValueCount)
  {
    $this->distinctValueCount = $distinctValueCount;
  }
  /**
   * @return string
   */
  public function getDistinctValueCount()
  {
    return $this->distinctValueCount;
  }
  /**
   * @param string $nullValueCount
   */
  public function setNullValueCount($nullValueCount)
  {
    $this->nullValueCount = $nullValueCount;
  }
  /**
   * @return string
   */
  public function getNullValueCount()
  {
    return $this->nullValueCount;
  }
  /**
   * @param string $validValueCount
   */
  public function setValidValueCount($validValueCount)
  {
    $this->validValueCount = $validValueCount;
  }
  /**
   * @return string
   */
  public function getValidValueCount()
  {
    return $this->validValueCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSCommonStats::class, 'Google_Service_CloudNaturalLanguage_XPSCommonStats');
