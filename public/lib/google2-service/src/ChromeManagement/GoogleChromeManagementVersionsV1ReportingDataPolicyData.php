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

class GoogleChromeManagementVersionsV1ReportingDataPolicyData extends \Google\Collection
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
  protected $collection_key = 'conflicts';
  protected $conflictsType = GoogleChromeManagementVersionsV1ReportingDataConflictingPolicyData::class;
  protected $conflictsDataType = 'array';
  /**
   * Output only. Error message of the policy, if any.
   *
   * @var string
   */
  public $error;
  /**
   * Output only. Name of the policy.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Source of the policy.
   *
   * @var string
   */
  public $source;
  /**
   * Output only. Value of the policy.
   *
   * @var string
   */
  public $value;

  /**
   * Output only. Conflicting policy information.
   *
   * @param GoogleChromeManagementVersionsV1ReportingDataConflictingPolicyData[] $conflicts
   */
  public function setConflicts($conflicts)
  {
    $this->conflicts = $conflicts;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ReportingDataConflictingPolicyData[]
   */
  public function getConflicts()
  {
    return $this->conflicts;
  }
  /**
   * Output only. Error message of the policy, if any.
   *
   * @param string $error
   */
  public function setError($error)
  {
    $this->error = $error;
  }
  /**
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. Name of the policy.
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
  /**
   * Output only. Value of the policy.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ReportingDataPolicyData::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ReportingDataPolicyData');
