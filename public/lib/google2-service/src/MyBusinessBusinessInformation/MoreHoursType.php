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

namespace Google\Service\MyBusinessBusinessInformation;

class MoreHoursType extends \Google\Model
{
  /**
   * Output only. The human-readable English display name for the hours type.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. A stable ID provided by Google for this hours type.
   *
   * @var string
   */
  public $hoursTypeId;
  /**
   * Output only. The human-readable localized display name for the hours type.
   *
   * @var string
   */
  public $localizedDisplayName;

  /**
   * Output only. The human-readable English display name for the hours type.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. A stable ID provided by Google for this hours type.
   *
   * @param string $hoursTypeId
   */
  public function setHoursTypeId($hoursTypeId)
  {
    $this->hoursTypeId = $hoursTypeId;
  }
  /**
   * @return string
   */
  public function getHoursTypeId()
  {
    return $this->hoursTypeId;
  }
  /**
   * Output only. The human-readable localized display name for the hours type.
   *
   * @param string $localizedDisplayName
   */
  public function setLocalizedDisplayName($localizedDisplayName)
  {
    $this->localizedDisplayName = $localizedDisplayName;
  }
  /**
   * @return string
   */
  public function getLocalizedDisplayName()
  {
    return $this->localizedDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MoreHoursType::class, 'Google_Service_MyBusinessBusinessInformation_MoreHoursType');
