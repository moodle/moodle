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

namespace Google\Service\GKEHub;

class WorkloadCertificateSpec extends \Google\Model
{
  /**
   * Disable workload certificate feature.
   */
  public const CERTIFICATE_MANAGEMENT_CERTIFICATE_MANAGEMENT_UNSPECIFIED = 'CERTIFICATE_MANAGEMENT_UNSPECIFIED';
  /**
   * Disable workload certificate feature.
   */
  public const CERTIFICATE_MANAGEMENT_DISABLED = 'DISABLED';
  /**
   * Enable workload certificate feature.
   */
  public const CERTIFICATE_MANAGEMENT_ENABLED = 'ENABLED';
  /**
   * CertificateManagement specifies workload certificate management.
   *
   * @var string
   */
  public $certificateManagement;

  /**
   * CertificateManagement specifies workload certificate management.
   *
   * Accepted values: CERTIFICATE_MANAGEMENT_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::CERTIFICATE_MANAGEMENT_* $certificateManagement
   */
  public function setCertificateManagement($certificateManagement)
  {
    $this->certificateManagement = $certificateManagement;
  }
  /**
   * @return self::CERTIFICATE_MANAGEMENT_*
   */
  public function getCertificateManagement()
  {
    return $this->certificateManagement;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkloadCertificateSpec::class, 'Google_Service_GKEHub_WorkloadCertificateSpec');
