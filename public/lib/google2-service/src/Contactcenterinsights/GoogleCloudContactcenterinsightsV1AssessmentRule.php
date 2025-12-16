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

class GoogleCloudContactcenterinsightsV1AssessmentRule extends \Google\Model
{
  /**
   * If true, apply this rule to conversations. Otherwise, this rule is
   * inactive.
   *
   * @var bool
   */
  public $active;
  /**
   * Output only. The time at which this assessment rule was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Display Name of the assessment rule.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The resource name of the assessment rule. Format:
   * projects/{project}/locations/{location}/assessmentRules/{assessment_rule}
   *
   * @var string
   */
  public $name;
  protected $sampleRuleType = GoogleCloudContactcenterinsightsV1SampleRule::class;
  protected $sampleRuleDataType = '';
  protected $scheduleInfoType = GoogleCloudContactcenterinsightsV1ScheduleInfo::class;
  protected $scheduleInfoDataType = '';
  /**
   * Output only. The most recent time at which this assessment rule was
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * If true, apply this rule to conversations. Otherwise, this rule is
   * inactive.
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
   * Output only. The time at which this assessment rule was created.
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
   * Display Name of the assessment rule.
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
   * Identifier. The resource name of the assessment rule. Format:
   * projects/{project}/locations/{location}/assessmentRules/{assessment_rule}
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
   * The sample rule for the assessment rule.
   *
   * @param GoogleCloudContactcenterinsightsV1SampleRule $sampleRule
   */
  public function setSampleRule(GoogleCloudContactcenterinsightsV1SampleRule $sampleRule)
  {
    $this->sampleRule = $sampleRule;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SampleRule
   */
  public function getSampleRule()
  {
    return $this->sampleRule;
  }
  /**
   * Schedule info for the assessment rule.
   *
   * @param GoogleCloudContactcenterinsightsV1ScheduleInfo $scheduleInfo
   */
  public function setScheduleInfo(GoogleCloudContactcenterinsightsV1ScheduleInfo $scheduleInfo)
  {
    $this->scheduleInfo = $scheduleInfo;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1ScheduleInfo
   */
  public function getScheduleInfo()
  {
    return $this->scheduleInfo;
  }
  /**
   * Output only. The most recent time at which this assessment rule was
   * updated.
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
class_alias(GoogleCloudContactcenterinsightsV1AssessmentRule::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1AssessmentRule');
