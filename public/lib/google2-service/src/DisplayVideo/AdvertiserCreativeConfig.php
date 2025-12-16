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

class AdvertiserCreativeConfig extends \Google\Model
{
  /**
   * Whether or not the advertiser is enabled for dynamic creatives.
   *
   * @var bool
   */
  public $dynamicCreativeEnabled;
  /**
   * An ID for configuring campaign monitoring provided by Integral Ad Service
   * (IAS). The DV360 system will append an IAS "Campaign Monitor" tag
   * containing this ID to the creative tag.
   *
   * @var string
   */
  public $iasClientId;
  /**
   * Whether or not to disable Google's About this Ad feature that adds badging
   * (to identify the content as an ad) and transparency information (on
   * interaction with About this Ad) to your ads for Online Behavioral
   * Advertising (OBA) and regulatory requirements. About this Ad gives users
   * greater control over the ads they see and helps you explain why they're
   * seeing your ad. [Learn
   * more](//support.google.com/displayvideo/answer/14315795). If you choose to
   * set this field to `true`, note that ads served through Display & Video 360
   * must comply to the following: * Be Online Behavioral Advertising (OBA)
   * compliant, as per your contract with Google Marketing Platform. * In the
   * European Economic Area (EEA), include transparency information and a
   * mechanism for users to report illegal content in ads. If using an
   * alternative ad badging, transparency, and reporting solution, you must
   * ensure it includes the required transparency information and illegal
   * content flagging mechanism and that you notify Google of any illegal
   * content reports using the appropriate [form](//support.google.com/legal/tro
   * ubleshooter/1114905?sjid=6787484030557261960-
   * EU#ts=2981967%2C2982031%2C12980091).
   *
   * @var bool
   */
  public $obaComplianceDisabled;
  /**
   * By setting this field to `true`, you, on behalf of your company, authorize
   * Google to use video creatives associated with this Display & Video 360
   * advertiser to provide reporting and features related to the advertiser's
   * television campaigns. Applicable only when the advertiser has a CM360
   * hybrid ad server configuration.
   *
   * @var bool
   */
  public $videoCreativeDataSharingAuthorized;

  /**
   * Whether or not the advertiser is enabled for dynamic creatives.
   *
   * @param bool $dynamicCreativeEnabled
   */
  public function setDynamicCreativeEnabled($dynamicCreativeEnabled)
  {
    $this->dynamicCreativeEnabled = $dynamicCreativeEnabled;
  }
  /**
   * @return bool
   */
  public function getDynamicCreativeEnabled()
  {
    return $this->dynamicCreativeEnabled;
  }
  /**
   * An ID for configuring campaign monitoring provided by Integral Ad Service
   * (IAS). The DV360 system will append an IAS "Campaign Monitor" tag
   * containing this ID to the creative tag.
   *
   * @param string $iasClientId
   */
  public function setIasClientId($iasClientId)
  {
    $this->iasClientId = $iasClientId;
  }
  /**
   * @return string
   */
  public function getIasClientId()
  {
    return $this->iasClientId;
  }
  /**
   * Whether or not to disable Google's About this Ad feature that adds badging
   * (to identify the content as an ad) and transparency information (on
   * interaction with About this Ad) to your ads for Online Behavioral
   * Advertising (OBA) and regulatory requirements. About this Ad gives users
   * greater control over the ads they see and helps you explain why they're
   * seeing your ad. [Learn
   * more](//support.google.com/displayvideo/answer/14315795). If you choose to
   * set this field to `true`, note that ads served through Display & Video 360
   * must comply to the following: * Be Online Behavioral Advertising (OBA)
   * compliant, as per your contract with Google Marketing Platform. * In the
   * European Economic Area (EEA), include transparency information and a
   * mechanism for users to report illegal content in ads. If using an
   * alternative ad badging, transparency, and reporting solution, you must
   * ensure it includes the required transparency information and illegal
   * content flagging mechanism and that you notify Google of any illegal
   * content reports using the appropriate [form](//support.google.com/legal/tro
   * ubleshooter/1114905?sjid=6787484030557261960-
   * EU#ts=2981967%2C2982031%2C12980091).
   *
   * @param bool $obaComplianceDisabled
   */
  public function setObaComplianceDisabled($obaComplianceDisabled)
  {
    $this->obaComplianceDisabled = $obaComplianceDisabled;
  }
  /**
   * @return bool
   */
  public function getObaComplianceDisabled()
  {
    return $this->obaComplianceDisabled;
  }
  /**
   * By setting this field to `true`, you, on behalf of your company, authorize
   * Google to use video creatives associated with this Display & Video 360
   * advertiser to provide reporting and features related to the advertiser's
   * television campaigns. Applicable only when the advertiser has a CM360
   * hybrid ad server configuration.
   *
   * @param bool $videoCreativeDataSharingAuthorized
   */
  public function setVideoCreativeDataSharingAuthorized($videoCreativeDataSharingAuthorized)
  {
    $this->videoCreativeDataSharingAuthorized = $videoCreativeDataSharingAuthorized;
  }
  /**
   * @return bool
   */
  public function getVideoCreativeDataSharingAuthorized()
  {
    return $this->videoCreativeDataSharingAuthorized;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertiserCreativeConfig::class, 'Google_Service_DisplayVideo_AdvertiserCreativeConfig');
