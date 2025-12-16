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

class GoogleCloudContactcenterinsightsV1mainIssueModel extends \Google\Model
{
  /**
   * Unspecified model type.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * Type V1.
   */
  public const MODEL_TYPE_TYPE_V1 = 'TYPE_V1';
  /**
   * Type V2.
   */
  public const MODEL_TYPE_TYPE_V2 = 'TYPE_V2';
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Model is not deployed but is ready to deploy.
   */
  public const STATE_UNDEPLOYED = 'UNDEPLOYED';
  /**
   * Model is being deployed.
   */
  public const STATE_DEPLOYING = 'DEPLOYING';
  /**
   * Model is deployed and is ready to be used. A model can only be used in
   * analysis if it's in this state.
   */
  public const STATE_DEPLOYED = 'DEPLOYED';
  /**
   * Model is being undeployed.
   */
  public const STATE_UNDEPLOYING = 'UNDEPLOYING';
  /**
   * Model is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Output only. The time at which this issue model was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The representative name for the issue model.
   *
   * @var string
   */
  public $displayName;
  protected $inputDataConfigType = GoogleCloudContactcenterinsightsV1mainIssueModelInputDataConfig::class;
  protected $inputDataConfigDataType = '';
  /**
   * Output only. Number of issues in this issue model.
   *
   * @var string
   */
  public $issueCount;
  /**
   * Language of the model.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Type of the model.
   *
   * @var string
   */
  public $modelType;
  /**
   * Immutable. The resource name of the issue model. Format:
   * projects/{project}/locations/{location}/issueModels/{issue_model}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the model.
   *
   * @var string
   */
  public $state;
  protected $trainingStatsType = GoogleCloudContactcenterinsightsV1mainIssueModelLabelStats::class;
  protected $trainingStatsDataType = '';
  /**
   * Output only. The most recent time at which the issue model was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which this issue model was created.
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
   * The representative name for the issue model.
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
   * Configs for the input data that used to create the issue model.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIssueModelInputDataConfig $inputDataConfig
   */
  public function setInputDataConfig(GoogleCloudContactcenterinsightsV1mainIssueModelInputDataConfig $inputDataConfig)
  {
    $this->inputDataConfig = $inputDataConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIssueModelInputDataConfig
   */
  public function getInputDataConfig()
  {
    return $this->inputDataConfig;
  }
  /**
   * Output only. Number of issues in this issue model.
   *
   * @param string $issueCount
   */
  public function setIssueCount($issueCount)
  {
    $this->issueCount = $issueCount;
  }
  /**
   * @return string
   */
  public function getIssueCount()
  {
    return $this->issueCount;
  }
  /**
   * Language of the model.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Type of the model.
   *
   * Accepted values: MODEL_TYPE_UNSPECIFIED, TYPE_V1, TYPE_V2
   *
   * @param self::MODEL_TYPE_* $modelType
   */
  public function setModelType($modelType)
  {
    $this->modelType = $modelType;
  }
  /**
   * @return self::MODEL_TYPE_*
   */
  public function getModelType()
  {
    return $this->modelType;
  }
  /**
   * Immutable. The resource name of the issue model. Format:
   * projects/{project}/locations/{location}/issueModels/{issue_model}
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
   * Output only. State of the model.
   *
   * Accepted values: STATE_UNSPECIFIED, UNDEPLOYED, DEPLOYING, DEPLOYED,
   * UNDEPLOYING, DELETING
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
   * Output only. Immutable. The issue model's label statistics on its training
   * data.
   *
   * @param GoogleCloudContactcenterinsightsV1mainIssueModelLabelStats $trainingStats
   */
  public function setTrainingStats(GoogleCloudContactcenterinsightsV1mainIssueModelLabelStats $trainingStats)
  {
    $this->trainingStats = $trainingStats;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainIssueModelLabelStats
   */
  public function getTrainingStats()
  {
    return $this->trainingStats;
  }
  /**
   * Output only. The most recent time at which the issue model was updated.
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
class_alias(GoogleCloudContactcenterinsightsV1mainIssueModel::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainIssueModel');
