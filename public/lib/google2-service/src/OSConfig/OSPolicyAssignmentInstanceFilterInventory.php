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

namespace Google\Service\OSConfig;

class OSPolicyAssignmentInstanceFilterInventory extends \Google\Model
{
  /**
   * Required. The OS short name
   *
   * @var string
   */
  public $osShortName;
  /**
   * The OS version Prefix matches are supported if asterisk(*) is provided as
   * the last character. For example, to match all versions with a major version
   * of `7`, specify the following value for this field `7.*` An empty string
   * matches all OS versions.
   *
   * @var string
   */
  public $osVersion;

  /**
   * Required. The OS short name
   *
   * @param string $osShortName
   */
  public function setOsShortName($osShortName)
  {
    $this->osShortName = $osShortName;
  }
  /**
   * @return string
   */
  public function getOsShortName()
  {
    return $this->osShortName;
  }
  /**
   * The OS version Prefix matches are supported if asterisk(*) is provided as
   * the last character. For example, to match all versions with a major version
   * of `7`, specify the following value for this field `7.*` An empty string
   * matches all OS versions.
   *
   * @param string $osVersion
   */
  public function setOsVersion($osVersion)
  {
    $this->osVersion = $osVersion;
  }
  /**
   * @return string
   */
  public function getOsVersion()
  {
    return $this->osVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyAssignmentInstanceFilterInventory::class, 'Google_Service_OSConfig_OSPolicyAssignmentInstanceFilterInventory');
