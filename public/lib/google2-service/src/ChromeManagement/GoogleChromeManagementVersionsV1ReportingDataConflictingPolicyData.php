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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1ReportingDataConflictingPolicyData extends \Google\Model
{
  /**
   * Represents an unspecified policy source.
   */
  public const SOURCE_POLICY_SOURCE_UNSPECIFIED = 'POLICY_SOURCE_UNSPECIFIED';
  /**
   * Represents a machine level platform policy.
   */
  public const SOURCE_MACHINE_PLATFORM = 'MACHINE_PLATFORM';
  /**
   * Represents a user level platform policy.
   */
  public const SOURCE_USER_PLATFORM = 'USER_PLATFORM';
  /**
   * Represents a machine level user cloud policy.
   */
  public const SOURCE_MACHINE_LEVEL_USER_CLOUD = 'MACHINE_LEVEL_USER_CLOUD';
  /**
   * Represents a user level cloud policy.
   */
  public const SOURCE_USER_CLOUD = 'USER_CLOUD';
  /**
   * Represents a machine level merged policy.
   */
  public const SOURCE_MACHINE_MERGED = 'MACHINE_MERGED';
  /**
   * Output only. Source of the policy.
   *
   * @var string
   */
  public $source;

  /**
   * Output only. Source of the policy.
   *
   * Accepted values: POLICY_SOURCE_UNSPECIFIED, MACHINE_PLATFORM,
   * USER_PLATFORM, MACHINE_LEVEL_USER_CLOUD, USER_CLOUD, MACHINE_MERGED
   *
   * @param self::SOURCE_* $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return self::SOURCE_*
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ReportingDataConflictingPolicyData::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ReportingDataConflictingPolicyData');
