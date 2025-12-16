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

class CmHybridConfig extends \Google\Collection
{
  protected $collection_key = 'cmSyncableSiteIds';
  /**
   * Required. Immutable. Account ID of the CM360 Floodlight configuration
   * linked with the DV360 advertiser.
   *
   * @var string
   */
  public $cmAccountId;
  /**
   * Output only. The set of CM360 Advertiser IDs sharing the CM360 Floodlight
   * configuration.
   *
   * @var string[]
   */
  public $cmAdvertiserIds;
  /**
   * Required. Immutable. ID of the CM360 Floodlight configuration linked with
   * the DV360 advertiser.
   *
   * @var string
   */
  public $cmFloodlightConfigId;
  /**
   * Required. Immutable. By setting this field to `true`, you, on behalf of
   * your company, authorize the sharing of information from the given
   * Floodlight configuration to this Display & Video 360 advertiser.
   *
   * @var bool
   */
  public $cmFloodlightLinkingAuthorized;
  /**
   * A list of CM360 sites whose placements will be synced to DV360 as
   * creatives. If absent or empty in CreateAdvertiser method, the system will
   * automatically create a CM360 site. Removing sites from this list may cause
   * DV360 creatives synced from CM360 to be deleted. At least one site must be
   * specified.
   *
   * @var string[]
   */
  public $cmSyncableSiteIds;
  /**
   * Whether or not to report DV360 cost to CM360.
   *
   * @var bool
   */
  public $dv360ToCmCostReportingEnabled;
  /**
   * Whether or not to include DV360 data in CM360 data transfer reports.
   *
   * @var bool
   */
  public $dv360ToCmDataSharingEnabled;

  /**
   * Required. Immutable. Account ID of the CM360 Floodlight configuration
   * linked with the DV360 advertiser.
   *
   * @param string $cmAccountId
   */
  public function setCmAccountId($cmAccountId)
  {
    $this->cmAccountId = $cmAccountId;
  }
  /**
   * @return string
   */
  public function getCmAccountId()
  {
    return $this->cmAccountId;
  }
  /**
   * Output only. The set of CM360 Advertiser IDs sharing the CM360 Floodlight
   * configuration.
   *
   * @param string[] $cmAdvertiserIds
   */
  public function setCmAdvertiserIds($cmAdvertiserIds)
  {
    $this->cmAdvertiserIds = $cmAdvertiserIds;
  }
  /**
   * @return string[]
   */
  public function getCmAdvertiserIds()
  {
    return $this->cmAdvertiserIds;
  }
  /**
   * Required. Immutable. ID of the CM360 Floodlight configuration linked with
   * the DV360 advertiser.
   *
   * @param string $cmFloodlightConfigId
   */
  public function setCmFloodlightConfigId($cmFloodlightConfigId)
  {
    $this->cmFloodlightConfigId = $cmFloodlightConfigId;
  }
  /**
   * @return string
   */
  public function getCmFloodlightConfigId()
  {
    return $this->cmFloodlightConfigId;
  }
  /**
   * Required. Immutable. By setting this field to `true`, you, on behalf of
   * your company, authorize the sharing of information from the given
   * Floodlight configuration to this Display & Video 360 advertiser.
   *
   * @param bool $cmFloodlightLinkingAuthorized
   */
  public function setCmFloodlightLinkingAuthorized($cmFloodlightLinkingAuthorized)
  {
    $this->cmFloodlightLinkingAuthorized = $cmFloodlightLinkingAuthorized;
  }
  /**
   * @return bool
   */
  public function getCmFloodlightLinkingAuthorized()
  {
    return $this->cmFloodlightLinkingAuthorized;
  }
  /**
   * A list of CM360 sites whose placements will be synced to DV360 as
   * creatives. If absent or empty in CreateAdvertiser method, the system will
   * automatically create a CM360 site. Removing sites from this list may cause
   * DV360 creatives synced from CM360 to be deleted. At least one site must be
   * specified.
   *
   * @param string[] $cmSyncableSiteIds
   */
  public function setCmSyncableSiteIds($cmSyncableSiteIds)
  {
    $this->cmSyncableSiteIds = $cmSyncableSiteIds;
  }
  /**
   * @return string[]
   */
  public function getCmSyncableSiteIds()
  {
    return $this->cmSyncableSiteIds;
  }
  /**
   * Whether or not to report DV360 cost to CM360.
   *
   * @param bool $dv360ToCmCostReportingEnabled
   */
  public function setDv360ToCmCostReportingEnabled($dv360ToCmCostReportingEnabled)
  {
    $this->dv360ToCmCostReportingEnabled = $dv360ToCmCostReportingEnabled;
  }
  /**
   * @return bool
   */
  public function getDv360ToCmCostReportingEnabled()
  {
    return $this->dv360ToCmCostReportingEnabled;
  }
  /**
   * Whether or not to include DV360 data in CM360 data transfer reports.
   *
   * @param bool $dv360ToCmDataSharingEnabled
   */
  public function setDv360ToCmDataSharingEnabled($dv360ToCmDataSharingEnabled)
  {
    $this->dv360ToCmDataSharingEnabled = $dv360ToCmDataSharingEnabled;
  }
  /**
   * @return bool
   */
  public function getDv360ToCmDataSharingEnabled()
  {
    return $this->dv360ToCmDataSharingEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CmHybridConfig::class, 'Google_Service_DisplayVideo_CmHybridConfig');
