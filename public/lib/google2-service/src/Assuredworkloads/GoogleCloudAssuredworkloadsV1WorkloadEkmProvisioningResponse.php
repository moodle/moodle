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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1WorkloadEkmProvisioningResponse extends \Google\Model
{
  /**
   * No error domain
   */
  public const EKM_PROVISIONING_ERROR_DOMAIN_EKM_PROVISIONING_ERROR_DOMAIN_UNSPECIFIED = 'EKM_PROVISIONING_ERROR_DOMAIN_UNSPECIFIED';
  /**
   * Error but domain is unspecified.
   */
  public const EKM_PROVISIONING_ERROR_DOMAIN_UNSPECIFIED_ERROR = 'UNSPECIFIED_ERROR';
  /**
   * Internal logic breaks within provisioning code.
   */
  public const EKM_PROVISIONING_ERROR_DOMAIN_GOOGLE_SERVER_ERROR = 'GOOGLE_SERVER_ERROR';
  /**
   * Error occurred with the customer not granting permission/creating resource.
   */
  public const EKM_PROVISIONING_ERROR_DOMAIN_EXTERNAL_USER_ERROR = 'EXTERNAL_USER_ERROR';
  /**
   * Error occurred within the partner's provisioning cluster.
   */
  public const EKM_PROVISIONING_ERROR_DOMAIN_EXTERNAL_PARTNER_ERROR = 'EXTERNAL_PARTNER_ERROR';
  /**
   * Resource wasn't provisioned in the required 7 day time period
   */
  public const EKM_PROVISIONING_ERROR_DOMAIN_TIMEOUT_ERROR = 'TIMEOUT_ERROR';
  /**
   * Error is unspecified.
   */
  public const EKM_PROVISIONING_ERROR_MAPPING_EKM_PROVISIONING_ERROR_MAPPING_UNSPECIFIED = 'EKM_PROVISIONING_ERROR_MAPPING_UNSPECIFIED';
  /**
   * Service account is used is invalid.
   */
  public const EKM_PROVISIONING_ERROR_MAPPING_INVALID_SERVICE_ACCOUNT = 'INVALID_SERVICE_ACCOUNT';
  /**
   * Iam permission monitoring.MetricsScopeAdmin wasn't applied.
   */
  public const EKM_PROVISIONING_ERROR_MAPPING_MISSING_METRICS_SCOPE_ADMIN_PERMISSION = 'MISSING_METRICS_SCOPE_ADMIN_PERMISSION';
  /**
   * Iam permission cloudkms.ekmConnectionsAdmin wasn't applied.
   */
  public const EKM_PROVISIONING_ERROR_MAPPING_MISSING_EKM_CONNECTION_ADMIN_PERMISSION = 'MISSING_EKM_CONNECTION_ADMIN_PERMISSION';
  /**
   * Default State for Ekm Provisioning
   */
  public const EKM_PROVISIONING_STATE_EKM_PROVISIONING_STATE_UNSPECIFIED = 'EKM_PROVISIONING_STATE_UNSPECIFIED';
  /**
   * Pending State for Ekm Provisioning
   */
  public const EKM_PROVISIONING_STATE_EKM_PROVISIONING_STATE_PENDING = 'EKM_PROVISIONING_STATE_PENDING';
  /**
   * Failed State for Ekm Provisioning
   */
  public const EKM_PROVISIONING_STATE_EKM_PROVISIONING_STATE_FAILED = 'EKM_PROVISIONING_STATE_FAILED';
  /**
   * Completed State for Ekm Provisioning
   */
  public const EKM_PROVISIONING_STATE_EKM_PROVISIONING_STATE_COMPLETED = 'EKM_PROVISIONING_STATE_COMPLETED';
  /**
   * Indicates Ekm provisioning error if any.
   *
   * @var string
   */
  public $ekmProvisioningErrorDomain;
  /**
   * Detailed error message if Ekm provisioning fails
   *
   * @var string
   */
  public $ekmProvisioningErrorMapping;
  /**
   * Output only. Indicates Ekm enrollment Provisioning of a given workload.
   *
   * @var string
   */
  public $ekmProvisioningState;

  /**
   * Indicates Ekm provisioning error if any.
   *
   * Accepted values: EKM_PROVISIONING_ERROR_DOMAIN_UNSPECIFIED,
   * UNSPECIFIED_ERROR, GOOGLE_SERVER_ERROR, EXTERNAL_USER_ERROR,
   * EXTERNAL_PARTNER_ERROR, TIMEOUT_ERROR
   *
   * @param self::EKM_PROVISIONING_ERROR_DOMAIN_* $ekmProvisioningErrorDomain
   */
  public function setEkmProvisioningErrorDomain($ekmProvisioningErrorDomain)
  {
    $this->ekmProvisioningErrorDomain = $ekmProvisioningErrorDomain;
  }
  /**
   * @return self::EKM_PROVISIONING_ERROR_DOMAIN_*
   */
  public function getEkmProvisioningErrorDomain()
  {
    return $this->ekmProvisioningErrorDomain;
  }
  /**
   * Detailed error message if Ekm provisioning fails
   *
   * Accepted values: EKM_PROVISIONING_ERROR_MAPPING_UNSPECIFIED,
   * INVALID_SERVICE_ACCOUNT, MISSING_METRICS_SCOPE_ADMIN_PERMISSION,
   * MISSING_EKM_CONNECTION_ADMIN_PERMISSION
   *
   * @param self::EKM_PROVISIONING_ERROR_MAPPING_* $ekmProvisioningErrorMapping
   */
  public function setEkmProvisioningErrorMapping($ekmProvisioningErrorMapping)
  {
    $this->ekmProvisioningErrorMapping = $ekmProvisioningErrorMapping;
  }
  /**
   * @return self::EKM_PROVISIONING_ERROR_MAPPING_*
   */
  public function getEkmProvisioningErrorMapping()
  {
    return $this->ekmProvisioningErrorMapping;
  }
  /**
   * Output only. Indicates Ekm enrollment Provisioning of a given workload.
   *
   * Accepted values: EKM_PROVISIONING_STATE_UNSPECIFIED,
   * EKM_PROVISIONING_STATE_PENDING, EKM_PROVISIONING_STATE_FAILED,
   * EKM_PROVISIONING_STATE_COMPLETED
   *
   * @param self::EKM_PROVISIONING_STATE_* $ekmProvisioningState
   */
  public function setEkmProvisioningState($ekmProvisioningState)
  {
    $this->ekmProvisioningState = $ekmProvisioningState;
  }
  /**
   * @return self::EKM_PROVISIONING_STATE_*
   */
  public function getEkmProvisioningState()
  {
    return $this->ekmProvisioningState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1WorkloadEkmProvisioningResponse::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1WorkloadEkmProvisioningResponse');
