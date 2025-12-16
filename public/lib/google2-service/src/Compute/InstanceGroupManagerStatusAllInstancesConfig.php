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

namespace Google\Service\Compute;

class InstanceGroupManagerStatusAllInstancesConfig extends \Google\Model
{
  /**
   * Output only. [Output Only] Current all-instances configuration revision.
   * This value is in RFC3339 text format.
   *
   * @var string
   */
  public $currentRevision;
  /**
   * Output only. [Output Only] A bit indicating whether this configuration has
   * been applied to all managed instances in the group.
   *
   * @var bool
   */
  public $effective;

  /**
   * Output only. [Output Only] Current all-instances configuration revision.
   * This value is in RFC3339 text format.
   *
   * @param string $currentRevision
   */
  public function setCurrentRevision($currentRevision)
  {
    $this->currentRevision = $currentRevision;
  }
  /**
   * @return string
   */
  public function getCurrentRevision()
  {
    return $this->currentRevision;
  }
  /**
   * Output only. [Output Only] A bit indicating whether this configuration has
   * been applied to all managed instances in the group.
   *
   * @param bool $effective
   */
  public function setEffective($effective)
  {
    $this->effective = $effective;
  }
  /**
   * @return bool
   */
  public function getEffective()
  {
    return $this->effective;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerStatusAllInstancesConfig::class, 'Google_Service_Compute_InstanceGroupManagerStatusAllInstancesConfig');
