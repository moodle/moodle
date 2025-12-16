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

class GooglePrivacyDlpV2DataProfileJobConfig extends \Google\Collection
{
  protected $collection_key = 'inspectTemplates';
  protected $dataProfileActionsType = GooglePrivacyDlpV2DataProfileAction::class;
  protected $dataProfileActionsDataType = 'array';
  /**
   * Detection logic for profile generation. Not all template features are used
   * by profiles. FindingLimits, include_quote and exclude_info_types have no
   * impact on data profiling. Multiple templates may be provided if there is
   * data in multiple regions. At most one template must be specified per-region
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
  protected $locationType = GooglePrivacyDlpV2DataProfileLocation::class;
  protected $locationDataType = '';
  protected $otherCloudStartingLocationType = GooglePrivacyDlpV2OtherCloudDiscoveryStartingLocation::class;
  protected $otherCloudStartingLocationDataType = '';
  /**
   * The project that will run the scan. The DLP service account that exists
   * within this project must have access to all resources that are profiled,
   * and the DLP API must be enabled.
   *
   * @var string
   */
  public $projectId;

  /**
   * Actions to execute at the completion of the job.
   *
   * @param GooglePrivacyDlpV2DataProfileAction[] $dataProfileActions
   */
  public function setDataProfileActions($dataProfileActions)
  {
    $this->dataProfileActions = $dataProfileActions;
  }
  /**
   * @return GooglePrivacyDlpV2DataProfileAction[]
   */
  public function getDataProfileActions()
  {
    return $this->dataProfileActions;
  }
  /**
   * Detection logic for profile generation. Not all template features are used
   * by profiles. FindingLimits, include_quote and exclude_info_types have no
   * impact on data profiling. Multiple templates may be provided if there is
   * data in multiple regions. At most one template must be specified per-region
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
   * The data to scan.
   *
   * @param GooglePrivacyDlpV2DataProfileLocation $location
   */
  public function setLocation(GooglePrivacyDlpV2DataProfileLocation $location)
  {
    $this->location = $location;
  }
  /**
   * @return GooglePrivacyDlpV2DataProfileLocation
   */
  public function getLocation()
  {
    return $this->location;
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
   * The project that will run the scan. The DLP service account that exists
   * within this project must have access to all resources that are profiled,
   * and the DLP API must be enabled.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DataProfileJobConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2DataProfileJobConfig');
