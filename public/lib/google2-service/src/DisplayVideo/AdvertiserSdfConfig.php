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

class AdvertiserSdfConfig extends \Google\Model
{
  /**
   * Whether or not this advertiser overrides the SDF configuration of its
   * parent partner. By default, an advertiser inherits the SDF configuration
   * from the parent partner. To override the partner configuration, set this
   * field to `true` and provide the new configuration in sdfConfig.
   *
   * @var bool
   */
  public $overridePartnerSdfConfig;
  protected $sdfConfigType = SdfConfig::class;
  protected $sdfConfigDataType = '';

  /**
   * Whether or not this advertiser overrides the SDF configuration of its
   * parent partner. By default, an advertiser inherits the SDF configuration
   * from the parent partner. To override the partner configuration, set this
   * field to `true` and provide the new configuration in sdfConfig.
   *
   * @param bool $overridePartnerSdfConfig
   */
  public function setOverridePartnerSdfConfig($overridePartnerSdfConfig)
  {
    $this->overridePartnerSdfConfig = $overridePartnerSdfConfig;
  }
  /**
   * @return bool
   */
  public function getOverridePartnerSdfConfig()
  {
    return $this->overridePartnerSdfConfig;
  }
  /**
   * The SDF configuration for the advertiser. * Required when
   * overridePartnerSdfConfig is `true`. * Output only when
   * overridePartnerSdfConfig is `false`.
   *
   * @param SdfConfig $sdfConfig
   */
  public function setSdfConfig(SdfConfig $sdfConfig)
  {
    $this->sdfConfig = $sdfConfig;
  }
  /**
   * @return SdfConfig
   */
  public function getSdfConfig()
  {
    return $this->sdfConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertiserSdfConfig::class, 'Google_Service_DisplayVideo_AdvertiserSdfConfig');
