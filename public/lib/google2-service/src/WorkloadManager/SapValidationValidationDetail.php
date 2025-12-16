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

namespace Google\Service\WorkloadManager;

class SapValidationValidationDetail extends \Google\Model
{
  /**
   * Unspecified type.
   */
  public const SAP_VALIDATION_TYPE_SAP_VALIDATION_TYPE_UNSPECIFIED = 'SAP_VALIDATION_TYPE_UNSPECIFIED';
  /**
   * The SYSTEM validation type collects underlying system data from the VM.
   */
  public const SAP_VALIDATION_TYPE_SYSTEM = 'SYSTEM';
  /**
   * The COROSYNC validation type collects Corosync configuration and runtime
   * data. Corosync enables servers to interact as a HA cluster.
   */
  public const SAP_VALIDATION_TYPE_COROSYNC = 'COROSYNC';
  /**
   * The PACEMAKER validation type collects Pacemaker configuration data.
   * Pacemaker is a high-availability cluster resource manager.
   */
  public const SAP_VALIDATION_TYPE_PACEMAKER = 'PACEMAKER';
  /**
   * The HANA validation type collects HANA configuration data. SAP HANA is an
   * in-memory, column-oriented, relational database management system.
   */
  public const SAP_VALIDATION_TYPE_HANA = 'HANA';
  /**
   * The NETWEAVER validation type collects NetWeaver configuration data. SAP
   * NetWeaver is a software stack for many of SAP SE's applications.
   */
  public const SAP_VALIDATION_TYPE_NETWEAVER = 'NETWEAVER';
  /**
   * The HANA_SECURITY validation type collects HANA configuration data as it
   * relates to SAP security best practices.
   */
  public const SAP_VALIDATION_TYPE_HANA_SECURITY = 'HANA_SECURITY';
  /**
   * The CUSTOM validation type collects any customer-defined data that does not
   * fall into any of the other categories of validations.
   */
  public const SAP_VALIDATION_TYPE_CUSTOM = 'CUSTOM';
  /**
   * Optional. The pairs of metrics data: field name & field value.
   *
   * @var string[]
   */
  public $details;
  /**
   * Optional. Was there a SAP system detected for this validation type.
   *
   * @var bool
   */
  public $isPresent;
  /**
   * Optional. The SAP system that the validation data is from.
   *
   * @var string
   */
  public $sapValidationType;

  /**
   * Optional. The pairs of metrics data: field name & field value.
   *
   * @param string[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Optional. Was there a SAP system detected for this validation type.
   *
   * @param bool $isPresent
   */
  public function setIsPresent($isPresent)
  {
    $this->isPresent = $isPresent;
  }
  /**
   * @return bool
   */
  public function getIsPresent()
  {
    return $this->isPresent;
  }
  /**
   * Optional. The SAP system that the validation data is from.
   *
   * Accepted values: SAP_VALIDATION_TYPE_UNSPECIFIED, SYSTEM, COROSYNC,
   * PACEMAKER, HANA, NETWEAVER, HANA_SECURITY, CUSTOM
   *
   * @param self::SAP_VALIDATION_TYPE_* $sapValidationType
   */
  public function setSapValidationType($sapValidationType)
  {
    $this->sapValidationType = $sapValidationType;
  }
  /**
   * @return self::SAP_VALIDATION_TYPE_*
   */
  public function getSapValidationType()
  {
    return $this->sapValidationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapValidationValidationDetail::class, 'Google_Service_WorkloadManager_SapValidationValidationDetail');
