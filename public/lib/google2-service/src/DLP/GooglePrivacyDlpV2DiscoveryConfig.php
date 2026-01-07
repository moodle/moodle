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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DiscoveryConfig extends \Google\Collection
{
  /**
   * Unused
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The discovery config is currently active.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The discovery config is paused temporarily.
   */
  public const STATUS_PAUSED = 'PAUSED';
  protected $collection_key = 'targets';
  protected $actionsType = GooglePrivacyDlpV2DataProfileAction::class;
  protected $actionsDataType = 'array';
  /**
   * Output only. The creation timestamp of a DiscoveryConfig.
   *
   * @var string
   */
  public $createTime;
  /**
   * Display name (max 100 chars)
   *
   * @var string
   */
  public $displayName;
  protected $errorsType = GooglePrivacyDlpV2Error::class;
  protected $errorsDataType = 'array';
  /**
   * Detection logic for profile generation. Not all template features are used
   * by Discovery. FindingLimits, include_quote and exclude_info_types have no
   * impact on Discovery. Multiple templates may be provided if there is data in
   * multiple regions. At most one template must be specified per-region
   * (including "global"). Each region is scanned using the applicable template.
   * If no region-specific template is specified, but a "global" template is
   * specified, it will be copied to that region and used instead. If no global
   * or region-specific template is provided for a region with data, that
   * region's data will not be scanned. For more information, see
   * https://cloud.google.com/sensitive-data-protection/docs/data-profiles#data-
   * residency.
   *
   * @var string[]
   */
  public $inspectTemplates;
  /**
   * Output only. The timestamp of the last time this config was executed.
   *
   * @var string
   */
  public $lastRunTime;
  /**
   * Unique resource name for the DiscoveryConfig, assigned by the service when
   * the DiscoveryConfig is created, for example `projects/dlp-test-
   * project/locations/global/discoveryConfigs/53234423`.
   *
   * @var string
   */
  public $name;
  protected $orgConfigType = GooglePrivacyDlpV2OrgConfig::class;
  protected $orgConfigDataType = '';
  protected $otherCloudStartingLocationType = GooglePrivacyDlpV2OtherCloudDiscoveryStartingLocation::class;
  protected $otherCloudStartingLocationDataType = '';
  protected $processingLocationType = GooglePrivacyDlpV2ProcessingLocation::class;
  protected $processingLocationDataType = '';
  /**
   * Required. A status for this configuration.
   *
   * @var string
   */
  public $status;
  protected $targetsType = GooglePrivacyDlpV2DiscoveryTarget::class;
  protected $targetsDataType = 'array';
  /**
   * Output only. The last update timestamp of a DiscoveryConfig.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Actions to execute at the completion of scanning.
   *
   * @param GooglePrivacyDlpV2DataProfileAction[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return GooglePrivacyDlpV2DataProfileAction[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Output only. The creation timestamp of a DiscoveryConfig.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Display name (max 100 chars)
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
   * Output only. A stream of errors encountered when the config was activated.
   * Repeated errors may result in the config automatically being paused. Output
   * only field. Will return the last 100 errors. Whenever the config is
   * modified this list will be cleared.
   *
   * @param GooglePrivacyDlpV2Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GooglePrivacyDlpV2Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Detection logic for profile generation. Not all template features are used
   * by Discovery. FindingLimits, include_quote and exclude_info_types have no
   * impact on Discovery. Multiple templates may be provided if there is data in
   * multiple regions. At most one template must be specified per-region
   * (including "global"). Each region is scanned using the applicable template.
   * If no region-specific template is specified, but a "global" template is
   * specified, it will be copied to that region and used instead. If no global
   * or region-specific template is provided for a region with data, that
   * region's data will not be scanned. For more information, see
   * https://cloud.google.com/sensitive-data-protection/docs/data-profiles#data-
   * residency.
   *
   * @param string[] $inspectTemplates
   */
  public function setInspectTemplates($inspectTemplates)
  {
    $this->inspectTemplates = $inspectTemplates;
  }
  /**
   * @return string[]
   */
  public function getInspectTemplates()
  {
    return $this->inspectTemplates;
  }
  /**
   * Output only. The timestamp of the last time this config was executed.
   *
   * @param string $lastRunTime
   */
  public function setLastRunTime($lastRunTime)
  {
    $this->lastRunTime = $lastRunTime;
  }
  /**
   * @return string
   */
  public function getLastRunTime()
  {
    return $this->lastRunTime;
  }
  /**
   * Unique resource name for the DiscoveryConfig, assigned by the service when
   * the DiscoveryConfig is created, for example `projects/dlp-test-
   * project/locations/global/discoveryConfigs/53234423`.
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
   * Only set when the parent is an org.
   *
   * @param GooglePrivacyDlpV2OrgConfig $orgConfig
   */
  public function setOrgConfig(GooglePrivacyDlpV2OrgConfig $orgConfig)
  {
    $this->orgConfig = $orgConfig;
  }
  /**
   * @return GooglePrivacyDlpV2OrgConfig
   */
  public function getOrgConfig()
  {
    return $this->orgConfig;
  }
  /**
   * Must be set only when scanning other clouds.
   *
   * @param GooglePrivacyDlpV2OtherCloudDiscoveryStartingLocation $otherCloudStartingLocation
   */
  public function setOtherCloudStartingLocation(GooglePrivacyDlpV2OtherCloudDiscoveryStartingLocation $otherCloudStartingLocation)
  {
    $this->otherCloudStartingLocation = $otherCloudStartingLocation;
  }
  /**
   * @return GooglePrivacyDlpV2OtherCloudDiscoveryStartingLocation
   */
  public function getOtherCloudStartingLocation()
  {
    return $this->otherCloudStartingLocation;
  }
  /**
   * Optional. Processing location configuration. Vertex AI dataset scanning
   * will set processing_location.image_fallback_type to MultiRegionProcessing
   * by default.
   *
   * @param GooglePrivacyDlpV2ProcessingLocation $processingLocation
   */
  public function setProcessingLocation(GooglePrivacyDlpV2ProcessingLocation $processingLocation)
  {
    $this->processingLocation = $processingLocation;
  }
  /**
   * @return GooglePrivacyDlpV2ProcessingLocation
   */
  public function getProcessingLocation()
  {
    return $this->processingLocation;
  }
  /**
   * Required. A status for this configuration.
   *
   * Accepted values: STATUS_UNSPECIFIED, RUNNING, PAUSED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Target to match against for determining what to scan and how frequently.
   *
   * @param GooglePrivacyDlpV2DiscoveryTarget[] $targets
   */
  public function setTargets($targets)
  {
    $this->targets = $targets;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryTarget[]
   */
  public function getTargets()
  {
    return $this->targets;
  }
  /**
   * Output only. The last update timestamp of a DiscoveryConfig.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryConfig');
