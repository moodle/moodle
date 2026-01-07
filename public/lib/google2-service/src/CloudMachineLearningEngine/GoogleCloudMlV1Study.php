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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1Study extends \Google\Model
{
  /**
   * The study state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The study is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The study is stopped due to an internal error.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The study is done when the service exhausts the parameter search space or
   * max_trial_count is reached.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Output only. Time at which the study was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. A human readable reason why the Study is inactive. This should
   * be empty if a study is ACTIVE or COMPLETED.
   *
   * @var string
   */
  public $inactiveReason;
  /**
   * Output only. The name of a study.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The detailed state of a study.
   *
   * @var string
   */
  public $state;
  protected $studyConfigType = GoogleCloudMlV1StudyConfig::class;
  protected $studyConfigDataType = '';

  /**
   * Output only. Time at which the study was created.
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
   * Output only. A human readable reason why the Study is inactive. This should
   * be empty if a study is ACTIVE or COMPLETED.
   *
   * @param string $inactiveReason
   */
  public function setInactiveReason($inactiveReason)
  {
    $this->inactiveReason = $inactiveReason;
  }
  /**
   * @return string
   */
  public function getInactiveReason()
  {
    return $this->inactiveReason;
  }
  /**
   * Output only. The name of a study.
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
   * Output only. The detailed state of a study.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INACTIVE, COMPLETED
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
   * Required. Configuration of the study.
   *
   * @param GoogleCloudMlV1StudyConfig $studyConfig
   */
  public function setStudyConfig(GoogleCloudMlV1StudyConfig $studyConfig)
  {
    $this->studyConfig = $studyConfig;
  }
  /**
   * @return GoogleCloudMlV1StudyConfig
   */
  public function getStudyConfig()
  {
    return $this->studyConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1Study::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1Study');
