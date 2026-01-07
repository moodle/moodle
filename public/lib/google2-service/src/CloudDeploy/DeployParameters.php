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

class DeployParameters extends \Google\Model
{
  /**
   * Optional. Deploy parameters are applied to targets with match labels. If
   * unspecified, deploy parameters are applied to all targets (including child
   * targets of a multi-target).
   *
   * @var string[]
   */
  public $matchTargetLabels;
  /**
   * Required. Values are deploy parameters in key-value pairs.
   *
   * @var string[]
   */
  public $values;

  /**
   * Optional. Deploy parameters are applied to targets with match labels. If
   * unspecified, deploy parameters are applied to all targets (including child
   * targets of a multi-target).
   *
   * @param string[] $matchTargetLabels
   */
  public function setMatchTargetLabels($matchTargetLabels)
  {
    $this->matchTargetLabels = $matchTargetLabels;
  }
  /**
   * @return string[]
   */
  public function getMatchTargetLabels()
  {
    return $this->matchTargetLabels;
  }
  /**
   * Required. Values are deploy parameters in key-value pairs.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeployParameters::class, 'Google_Service_CloudDeploy_DeployParameters');
