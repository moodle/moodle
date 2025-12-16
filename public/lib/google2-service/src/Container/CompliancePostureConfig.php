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

namespace Google\Service\Container;

class CompliancePostureConfig extends \Google\Collection
{
  /**
   * Default value not specified.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Disables Compliance Posture features on the cluster.
   */
  public const MODE_DISABLED = 'DISABLED';
  /**
   * Enables Compliance Posture features on the cluster.
   */
  public const MODE_ENABLED = 'ENABLED';
  protected $collection_key = 'complianceStandards';
  protected $complianceStandardsType = ComplianceStandard::class;
  protected $complianceStandardsDataType = 'array';
  /**
   * Defines the enablement mode for Compliance Posture.
   *
   * @var string
   */
  public $mode;

  /**
   * List of enabled compliance standards.
   *
   * @param ComplianceStandard[] $complianceStandards
   */
  public function setComplianceStandards($complianceStandards)
  {
    $this->complianceStandards = $complianceStandards;
  }
  /**
   * @return ComplianceStandard[]
   */
  public function getComplianceStandards()
  {
    return $this->complianceStandards;
  }
  /**
   * Defines the enablement mode for Compliance Posture.
   *
   * Accepted values: MODE_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompliancePostureConfig::class, 'Google_Service_Container_CompliancePostureConfig');
