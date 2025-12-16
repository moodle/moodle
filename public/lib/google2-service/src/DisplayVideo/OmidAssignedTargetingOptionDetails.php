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

class OmidAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when omid targeting is not specified in this version.
   */
  public const OMID_OMID_UNSPECIFIED = 'OMID_UNSPECIFIED';
  /**
   * Open Measurement enabled mobile display inventory.
   */
  public const OMID_OMID_FOR_MOBILE_DISPLAY_ADS = 'OMID_FOR_MOBILE_DISPLAY_ADS';
  /**
   * Required. The type of Open Measurement enabled inventory.
   *
   * @var string
   */
  public $omid;

  /**
   * Required. The type of Open Measurement enabled inventory.
   *
   * Accepted values: OMID_UNSPECIFIED, OMID_FOR_MOBILE_DISPLAY_ADS
   *
   * @param self::OMID_* $omid
   */
  public function setOmid($omid)
  {
    $this->omid = $omid;
  }
  /**
   * @return self::OMID_*
   */
  public function getOmid()
  {
    return $this->omid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OmidAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_OmidAssignedTargetingOptionDetails');
