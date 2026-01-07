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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1Assessment extends \Google\Model
{
  /**
   * The state is unspecified. This value should not be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The default state of all new assessments.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The assessment has been published.
   */
  public const STATE_PUBLISHED = 'PUBLISHED';
  /**
   * The assessment has been appealed.
   */
  public const STATE_APPEALED = 'APPEALED';
  /**
   * The assessment has been finalized.
   */
  public const STATE_FINALIZED = 'FINALIZED';
  protected $agentInfoType = GoogleCloudContactcenterinsightsV1ConversationQualityMetadataAgentInfo::class;
  protected $agentInfoDataType = '';
  /**
   * Output only. The time at which the assessment was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The resource name of the assessment. Format: projects/{project}
   * /locations/{location}/conversations/{conversation}/assessments/{assessment}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the assessment.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time at which the assessment was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Information about the agent the assessment is for.
   *
   * @param GoogleCloudContactcenterinsightsV1ConversationQualityMetadataAgentInfo $agentInfo
   */
  public function setAgentInfo(GoogleCloudContactcenterinsightsV1ConversationQualityMetadataAgentInfo $agentInfo)
  {
    $this->agentInfo = $agentInfo;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1ConversationQualityMetadataAgentInfo
   */
  public function getAgentInfo()
  {
    return $this->agentInfo;
  }
  /**
   * Output only. The time at which the assessment was created.
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
   * Identifier. The resource name of the assessment. Format: projects/{project}
   * /locations/{location}/conversations/{conversation}/assessments/{assessment}
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
   * Output only. The state of the assessment.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, PUBLISHED, APPEALED, FINALIZED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The time at which the assessment was last updated.
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
class_alias(GoogleCloudContactcenterinsightsV1Assessment::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1Assessment');
