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

namespace Google\Service\CloudDeploy;

class TimedPromoteReleaseOperation extends \Google\Model
{
  /**
   * Output only. The starting phase of the rollout created by this operation.
   *
   * @var string
   */
  public $phase;
  /**
   * Output only. The name of the release to be promoted.
   *
   * @var string
   */
  public $release;
  /**
   * Output only. The ID of the target that represents the promotion stage to
   * which the release will be promoted. The value of this field is the last
   * segment of a target name.
   *
   * @var string
   */
  public $targetId;

  /**
   * Output only. The starting phase of the rollout created by this operation.
   *
   * @param string $phase
   */
  public function setPhase($phase)
  {
    $this->phase = $phase;
  }
  /**
   * @return string
   */
  public function getPhase()
  {
    return $this->phase;
  }
  /**
   * Output only. The name of the release to be promoted.
   *
   * @param string $release
   */
  public function setRelease($release)
  {
    $this->release = $release;
  }
  /**
   * @return string
   */
  public function getRelease()
  {
    return $this->release;
  }
  /**
   * Output only. The ID of the target that represents the promotion stage to
   * which the release will be promoted. The value of this field is the last
   * segment of a target name.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimedPromoteReleaseOperation::class, 'Google_Service_CloudDeploy_TimedPromoteReleaseOperation');
