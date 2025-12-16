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

class RemarketingConfig extends \Google\Model
{
  /**
   * Output only. The ID of the advertiser.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Output only. Whether the Floodlight activity remarketing user list is
   * available to the identified advertiser.
   *
   * @var bool
   */
  public $remarketingEnabled;

  /**
   * Output only. The ID of the advertiser.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Output only. Whether the Floodlight activity remarketing user list is
   * available to the identified advertiser.
   *
   * @param bool $remarketingEnabled
   */
  public function setRemarketingEnabled($remarketingEnabled)
  {
    $this->remarketingEnabled = $remarketingEnabled;
  }
  /**
   * @return bool
   */
  public function getRemarketingEnabled()
  {
    return $this->remarketingEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemarketingConfig::class, 'Google_Service_DisplayVideo_RemarketingConfig');
