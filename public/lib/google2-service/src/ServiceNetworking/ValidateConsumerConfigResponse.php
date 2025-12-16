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

namespace Google\Service\ServiceNetworking;

class ValidateConsumerConfigResponse extends \Google\Collection
{
  public const VALIDATION_ERROR_VALIDATION_ERROR_UNSPECIFIED = 'VALIDATION_ERROR_UNSPECIFIED';
  /**
   * In case none of the validations are requested.
   */
  public const VALIDATION_ERROR_VALIDATION_NOT_REQUESTED = 'VALIDATION_NOT_REQUESTED';
  public const VALIDATION_ERROR_SERVICE_NETWORKING_NOT_ENABLED = 'SERVICE_NETWORKING_NOT_ENABLED';
  /**
   * The network provided by the consumer does not exist.
   */
  public const VALIDATION_ERROR_NETWORK_NOT_FOUND = 'NETWORK_NOT_FOUND';
  /**
   * The network has not been peered with the producer org.
   */
  public const VALIDATION_ERROR_NETWORK_NOT_PEERED = 'NETWORK_NOT_PEERED';
  /**
   * The peering was created and later deleted.
   */
  public const VALIDATION_ERROR_NETWORK_PEERING_DELETED = 'NETWORK_PEERING_DELETED';
  /**
   * The network is a regular VPC but the network is not in the consumer's
   * project.
   */
  public const VALIDATION_ERROR_NETWORK_NOT_IN_CONSUMERS_PROJECT = 'NETWORK_NOT_IN_CONSUMERS_PROJECT';
  /**
   * The consumer project is a service project, and network is a shared VPC, but
   * the network is not in the host project of this consumer project.
   */
  public const VALIDATION_ERROR_NETWORK_NOT_IN_CONSUMERS_HOST_PROJECT = 'NETWORK_NOT_IN_CONSUMERS_HOST_PROJECT';
  /**
   * The host project associated with the consumer project was not found.
   */
  public const VALIDATION_ERROR_HOST_PROJECT_NOT_FOUND = 'HOST_PROJECT_NOT_FOUND';
  /**
   * The consumer project is not a service project for the specified host
   * project.
   */
  public const VALIDATION_ERROR_CONSUMER_PROJECT_NOT_SERVICE_PROJECT = 'CONSUMER_PROJECT_NOT_SERVICE_PROJECT';
  /**
   * The reserved IP ranges do not have enough space to create a subnet of
   * desired size.
   */
  public const VALIDATION_ERROR_RANGES_EXHAUSTED = 'RANGES_EXHAUSTED';
  /**
   * The IP ranges were not reserved.
   */
  public const VALIDATION_ERROR_RANGES_NOT_RESERVED = 'RANGES_NOT_RESERVED';
  /**
   * The IP ranges were reserved but deleted later.
   */
  public const VALIDATION_ERROR_RANGES_DELETED_LATER = 'RANGES_DELETED_LATER';
  /**
   * The consumer project does not have the compute api enabled.
   */
  public const VALIDATION_ERROR_COMPUTE_API_NOT_ENABLED = 'COMPUTE_API_NOT_ENABLED';
  /**
   * The consumer project does not have the permission from the host project.
   */
  public const VALIDATION_ERROR_USE_PERMISSION_NOT_FOUND = 'USE_PERMISSION_NOT_FOUND';
  /**
   * The SN service agent {service-@service-networking.iam.gserviceaccount.com}
   * does not have the SN service agent role on the consumer project.
   */
  public const VALIDATION_ERROR_SN_SERVICE_AGENT_PERMISSION_DENIED_ON_CONSUMER_PROJECT = 'SN_SERVICE_AGENT_PERMISSION_DENIED_ON_CONSUMER_PROJECT';
  protected $collection_key = 'existingSubnetworkCandidates';
  protected $existingSubnetworkCandidatesType = Subnetwork::class;
  protected $existingSubnetworkCandidatesDataType = 'array';
  /**
   * Indicates whether all the requested validations passed.
   *
   * @var bool
   */
  public $isValid;
  /**
   * The first validation which failed.
   *
   * @var string
   */
  public $validationError;

  /**
   * List of subnetwork candidates from the request which exist with the
   * `ip_cidr_range`, `secondary_ip_cider_ranges`, and `outside_allocation`
   * fields set.
   *
   * @param Subnetwork[] $existingSubnetworkCandidates
   */
  public function setExistingSubnetworkCandidates($existingSubnetworkCandidates)
  {
    $this->existingSubnetworkCandidates = $existingSubnetworkCandidates;
  }
  /**
   * @return Subnetwork[]
   */
  public function getExistingSubnetworkCandidates()
  {
    return $this->existingSubnetworkCandidates;
  }
  /**
   * Indicates whether all the requested validations passed.
   *
   * @param bool $isValid
   */
  public function setIsValid($isValid)
  {
    $this->isValid = $isValid;
  }
  /**
   * @return bool
   */
  public function getIsValid()
  {
    return $this->isValid;
  }
  /**
   * The first validation which failed.
   *
   * Accepted values: VALIDATION_ERROR_UNSPECIFIED, VALIDATION_NOT_REQUESTED,
   * SERVICE_NETWORKING_NOT_ENABLED, NETWORK_NOT_FOUND, NETWORK_NOT_PEERED,
   * NETWORK_PEERING_DELETED, NETWORK_NOT_IN_CONSUMERS_PROJECT,
   * NETWORK_NOT_IN_CONSUMERS_HOST_PROJECT, HOST_PROJECT_NOT_FOUND,
   * CONSUMER_PROJECT_NOT_SERVICE_PROJECT, RANGES_EXHAUSTED,
   * RANGES_NOT_RESERVED, RANGES_DELETED_LATER, COMPUTE_API_NOT_ENABLED,
   * USE_PERMISSION_NOT_FOUND,
   * SN_SERVICE_AGENT_PERMISSION_DENIED_ON_CONSUMER_PROJECT
   *
   * @param self::VALIDATION_ERROR_* $validationError
   */
  public function setValidationError($validationError)
  {
    $this->validationError = $validationError;
  }
  /**
   * @return self::VALIDATION_ERROR_*
   */
  public function getValidationError()
  {
    return $this->validationError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidateConsumerConfigResponse::class, 'Google_Service_ServiceNetworking_ValidateConsumerConfigResponse');
