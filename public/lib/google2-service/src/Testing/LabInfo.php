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

namespace Google\Service\Testing;

class LabInfo extends \Google\Model
{
  /**
   * Lab name where the device is hosted. If empty, the device is hosted in a
   * Google owned lab.
   *
   * @var string
   */
  public $name;
  /**
   * The Unicode country/region code (CLDR) of the lab where the device is
   * hosted. E.g. "US" for United States, "CH" for Switzerland.
   *
   * @var string
   */
  public $regionCode;

  /**
   * Lab name where the device is hosted. If empty, the device is hosted in a
   * Google owned lab.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The Unicode country/region code (CLDR) of the lab where the device is
   * hosted. E.g. "US" for United States, "CH" for Switzerland.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabInfo::class, 'Google_Service_Testing_LabInfo');
