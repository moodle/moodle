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

class GooglePrivacyDlpV2DataProfileConfigSnapshot extends \Google\Model
{
  protected $dataProfileJobType = GooglePrivacyDlpV2DataProfileJobConfig::class;
  protected $dataProfileJobDataType = '';
  protected $discoveryConfigType = GooglePrivacyDlpV2DiscoveryConfig::class;
  protected $discoveryConfigDataType = '';
  protected $inspectConfigType = GooglePrivacyDlpV2InspectConfig::class;
  protected $inspectConfigDataType = '';
  /**
   * Timestamp when the template was modified
   *
   * @var string
   */
  public $inspectTemplateModifiedTime;
  /**
   * Name of the inspection template used to generate this profile
   *
   * @var string
   */
  public $inspectTemplateName;

  /**
   * A copy of the configuration used to generate this profile. This is
   * deprecated, and the DiscoveryConfig field is preferred moving forward.
   * DataProfileJobConfig will still be written here for Discovery in BigQuery
   * for backwards compatibility, but will not be updated with new fields, while
   * DiscoveryConfig will.
   *
   * @deprecated
   * @param GooglePrivacyDlpV2DataProfileJobConfig $dataProfileJob
   */
  public function setDataProfileJob(GooglePrivacyDlpV2DataProfileJobConfig $dataProfileJob)
  {
    $this->dataProfileJob = $dataProfileJob;
  }
  /**
   * @deprecated
   * @return GooglePrivacyDlpV2DataProfileJobConfig
   */
  public function getDataProfileJob()
  {
    return $this->dataProfileJob;
  }
  /**
   * A copy of the configuration used to generate this profile.
   *
   * @param GooglePrivacyDlpV2DiscoveryConfig $discoveryConfig
   */
  public function setDiscoveryConfig(GooglePrivacyDlpV2DiscoveryConfig $discoveryConfig)
  {
    $this->discoveryConfig = $discoveryConfig;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryConfig
   */
  public function getDiscoveryConfig()
  {
    return $this->discoveryConfig;
  }
  /**
   * A copy of the inspection config used to generate this profile. This is a
   * copy of the inspect_template specified in `DataProfileJobConfig`.
   *
   * @param GooglePrivacyDlpV2InspectConfig $inspectConfig
   */
  public function setInspectConfig(GooglePrivacyDlpV2InspectConfig $inspectConfig)
  {
    $this->inspectConfig = $inspectConfig;
  }
  /**
   * @return GooglePrivacyDlpV2InspectConfig
   */
  public function getInspectConfig()
  {
    return $this->inspectConfig;
  }
  /**
   * Timestamp when the template was modified
   *
   * @param string $inspectTemplateModifiedTime
   */
  public function setInspectTemplateModifiedTime($inspectTemplateModifiedTime)
  {
    $this->inspectTemplateModifiedTime = $inspectTemplateModifiedTime;
  }
  /**
   * @return string
   */
  public function getInspectTemplateModifiedTime()
  {
    return $this->inspectTemplateModifiedTime;
  }
  /**
   * Name of the inspection template used to generate this profile
   *
   * @param string $inspectTemplateName
   */
  public function setInspectTemplateName($inspectTemplateName)
  {
    $this->inspectTemplateName = $inspectTemplateName;
  }
  /**
   * @return string
   */
  public function getInspectTemplateName()
  {
    return $this->inspectTemplateName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DataProfileConfigSnapshot::class, 'Google_Service_DLP_GooglePrivacyDlpV2DataProfileConfigSnapshot');
