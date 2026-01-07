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

namespace Google\Service\Adsense;

class CustomChannel extends \Google\Model
{
  /**
   * Whether the custom channel is active and collecting data. See
   * https://support.google.com/adsense/answer/10077192.
   *
   * @var bool
   */
  public $active;
  /**
   * Required. Display name of the custom channel.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Resource name of the custom channel. Format:
   * accounts/{account}/adclients/{adclient}/customchannels/{customchannel}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Unique ID of the custom channel as used in the
   * `CUSTOM_CHANNEL_ID` reporting dimension.
   *
   * @var string
   */
  public $reportingDimensionId;

  /**
   * Whether the custom channel is active and collecting data. See
   * https://support.google.com/adsense/answer/10077192.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Required. Display name of the custom channel.
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
   * Output only. Resource name of the custom channel. Format:
   * accounts/{account}/adclients/{adclient}/customchannels/{customchannel}
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
   * Output only. Unique ID of the custom channel as used in the
   * `CUSTOM_CHANNEL_ID` reporting dimension.
   *
   * @param string $reportingDimensionId
   */
  public function setReportingDimensionId($reportingDimensionId)
  {
    $this->reportingDimensionId = $reportingDimensionId;
  }
  /**
   * @return string
   */
  public function getReportingDimensionId()
  {
    return $this->reportingDimensionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomChannel::class, 'Google_Service_Adsense_CustomChannel');
