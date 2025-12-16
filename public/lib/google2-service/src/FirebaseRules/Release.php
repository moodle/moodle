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

namespace Google\Service\FirebaseRules;

class Release extends \Google\Model
{
  /**
   * Output only. Time the release was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Format: `projects/{project_id}/releases/{release_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. Name of the `Ruleset` referred to by this `Release`. The
   * `Ruleset` must exist for the `Release` to be created.
   *
   * @var string
   */
  public $rulesetName;
  /**
   * Output only. Time the release was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time the release was created.
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
   * Required. Format: `projects/{project_id}/releases/{release_id}`
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
   * Required. Name of the `Ruleset` referred to by this `Release`. The
   * `Ruleset` must exist for the `Release` to be created.
   *
   * @param string $rulesetName
   */
  public function setRulesetName($rulesetName)
  {
    $this->rulesetName = $rulesetName;
  }
  /**
   * @return string
   */
  public function getRulesetName()
  {
    return $this->rulesetName;
  }
  /**
   * Output only. Time the release was updated.
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
class_alias(Release::class, 'Google_Service_FirebaseRules_Release');
