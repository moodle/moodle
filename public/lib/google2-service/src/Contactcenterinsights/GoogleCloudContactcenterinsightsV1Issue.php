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

class GoogleCloudContactcenterinsightsV1Issue extends \Google\Collection
{
  protected $collection_key = 'sampleUtterances';
  /**
   * Output only. The time at which this issue was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Representative description of the issue.
   *
   * @var string
   */
  public $displayDescription;
  /**
   * The representative name for the issue.
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. The resource name of the issue. Format: projects/{project}/locat
   * ions/{location}/issueModels/{issue_model}/issues/{issue}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Resource names of the sample representative utterances that
   * match to this issue.
   *
   * @var string[]
   */
  public $sampleUtterances;
  /**
   * Output only. The most recent time that this issue was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which this issue was created.
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
   * Representative description of the issue.
   *
   * @param string $displayDescription
   */
  public function setDisplayDescription($displayDescription)
  {
    $this->displayDescription = $displayDescription;
  }
  /**
   * @return string
   */
  public function getDisplayDescription()
  {
    return $this->displayDescription;
  }
  /**
   * The representative name for the issue.
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
   * Immutable. The resource name of the issue. Format: projects/{project}/locat
   * ions/{location}/issueModels/{issue_model}/issues/{issue}
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
   * Output only. Resource names of the sample representative utterances that
   * match to this issue.
   *
   * @param string[] $sampleUtterances
   */
  public function setSampleUtterances($sampleUtterances)
  {
    $this->sampleUtterances = $sampleUtterances;
  }
  /**
   * @return string[]
   */
  public function getSampleUtterances()
  {
    return $this->sampleUtterances;
  }
  /**
   * Output only. The most recent time that this issue was updated.
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
class_alias(GoogleCloudContactcenterinsightsV1Issue::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1Issue');
