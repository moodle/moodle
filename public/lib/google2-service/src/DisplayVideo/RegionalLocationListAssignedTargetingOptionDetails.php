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

namespace Google\Service\DisplayVideo;

class RegionalLocationListAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Indicates if this option is being negatively targeted.
   *
   * @var bool
   */
  public $negative;
  /**
   * Required. ID of the regional location list. Should refer to the
   * location_list_id field of a LocationList resource whose type is
   * `TARGETING_LOCATION_TYPE_REGIONAL`.
   *
   * @var string
   */
  public $regionalLocationListId;

  /**
   * Indicates if this option is being negatively targeted.
   *
   * @param bool $negative
   */
  public function setNegative($negative)
  {
    $this->negative = $negative;
  }
  /**
   * @return bool
   */
  public function getNegative()
  {
    return $this->negative;
  }
  /**
   * Required. ID of the regional location list. Should refer to the
   * location_list_id field of a LocationList resource whose type is
   * `TARGETING_LOCATION_TYPE_REGIONAL`.
   *
   * @param string $regionalLocationListId
   */
  public function setRegionalLocationListId($regionalLocationListId)
  {
    $this->regionalLocationListId = $regionalLocationListId;
  }
  /**
   * @return string
   */
  public function getRegionalLocationListId()
  {
    return $this->regionalLocationListId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionalLocationListAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_RegionalLocationListAssignedTargetingOptionDetails');
