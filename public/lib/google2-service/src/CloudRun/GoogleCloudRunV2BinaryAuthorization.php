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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2BinaryAuthorization extends \Google\Model
{
  /**
   * Optional. If present, indicates to use Breakglass using this justification.
   * If use_default is False, then it must be empty. For more information on
   * breakglass, see https://cloud.google.com/binary-authorization/docs/using-
   * breakglass
   *
   * @var string
   */
  public $breakglassJustification;
  /**
   * Optional. The path to a binary authorization policy. Format:
   * `projects/{project}/platforms/cloudRun/{policy-name}`
   *
   * @var string
   */
  public $policy;
  /**
   * Optional. If True, indicates to use the default project's binary
   * authorization policy. If False, binary authorization will be disabled.
   *
   * @var bool
   */
  public $useDefault;

  /**
   * Optional. If present, indicates to use Breakglass using this justification.
   * If use_default is False, then it must be empty. For more information on
   * breakglass, see https://cloud.google.com/binary-authorization/docs/using-
   * breakglass
   *
   * @param string $breakglassJustification
   */
  public function setBreakglassJustification($breakglassJustification)
  {
    $this->breakglassJustification = $breakglassJustification;
  }
  /**
   * @return string
   */
  public function getBreakglassJustification()
  {
    return $this->breakglassJustification;
  }
  /**
   * Optional. The path to a binary authorization policy. Format:
   * `projects/{project}/platforms/cloudRun/{policy-name}`
   *
   * @param string $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return string
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * Optional. If True, indicates to use the default project's binary
   * authorization policy. If False, binary authorization will be disabled.
   *
   * @param bool $useDefault
   */
  public function setUseDefault($useDefault)
  {
    $this->useDefault = $useDefault;
  }
  /**
   * @return bool
   */
  public function getUseDefault()
  {
    return $this->useDefault;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2BinaryAuthorization::class, 'Google_Service_CloudRun_GoogleCloudRunV2BinaryAuthorization');
