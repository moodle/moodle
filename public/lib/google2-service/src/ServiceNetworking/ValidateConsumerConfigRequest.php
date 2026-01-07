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

class ValidateConsumerConfigRequest extends \Google\Model
{
  /**
   * Optional. The IAM permission check determines whether the consumer project
   * has 'servicenetworking.services.use' permission or not.
   *
   * @var bool
   */
  public $checkServiceNetworkingUsePermission;
  /**
   * Required. The network that the consumer is using to connect with services.
   * Must be in the form of projects/{project}/global/networks/{network}
   * {project} is a project number, as in '12345' {network} is network name.
   *
   * @var string
   */
  public $consumerNetwork;
  protected $consumerProjectType = ConsumerProject::class;
  protected $consumerProjectDataType = '';
  protected $rangeReservationType = RangeReservation::class;
  protected $rangeReservationDataType = '';
  /**
   * The validations will be performed in the order listed in the
   * ValidationError enum. The first failure will return. If a validation is not
   * requested, then the next one will be performed.
   * SERVICE_NETWORKING_NOT_ENABLED and NETWORK_NOT_PEERED checks are performed
   * for all requests where validation is requested. NETWORK_NOT_FOUND and
   * NETWORK_DISCONNECTED checks are done for requests that have
   * validate_network set to true.
   *
   * @var bool
   */
  public $validateNetwork;

  /**
   * Optional. The IAM permission check determines whether the consumer project
   * has 'servicenetworking.services.use' permission or not.
   *
   * @param bool $checkServiceNetworkingUsePermission
   */
  public function setCheckServiceNetworkingUsePermission($checkServiceNetworkingUsePermission)
  {
    $this->checkServiceNetworkingUsePermission = $checkServiceNetworkingUsePermission;
  }
  /**
   * @return bool
   */
  public function getCheckServiceNetworkingUsePermission()
  {
    return $this->checkServiceNetworkingUsePermission;
  }
  /**
   * Required. The network that the consumer is using to connect with services.
   * Must be in the form of projects/{project}/global/networks/{network}
   * {project} is a project number, as in '12345' {network} is network name.
   *
   * @param string $consumerNetwork
   */
  public function setConsumerNetwork($consumerNetwork)
  {
    $this->consumerNetwork = $consumerNetwork;
  }
  /**
   * @return string
   */
  public function getConsumerNetwork()
  {
    return $this->consumerNetwork;
  }
  /**
   * NETWORK_NOT_IN_CONSUMERS_PROJECT, NETWORK_NOT_IN_CONSUMERS_HOST_PROJECT,
   * and HOST_PROJECT_NOT_FOUND are done when consumer_project is provided.
   *
   * @param ConsumerProject $consumerProject
   */
  public function setConsumerProject(ConsumerProject $consumerProject)
  {
    $this->consumerProject = $consumerProject;
  }
  /**
   * @return ConsumerProject
   */
  public function getConsumerProject()
  {
    return $this->consumerProject;
  }
  /**
   * RANGES_EXHAUSTED, RANGES_NOT_RESERVED, and RANGES_DELETED_LATER are done
   * when range_reservation is provided.
   *
   * @param RangeReservation $rangeReservation
   */
  public function setRangeReservation(RangeReservation $rangeReservation)
  {
    $this->rangeReservation = $rangeReservation;
  }
  /**
   * @return RangeReservation
   */
  public function getRangeReservation()
  {
    return $this->rangeReservation;
  }
  /**
   * The validations will be performed in the order listed in the
   * ValidationError enum. The first failure will return. If a validation is not
   * requested, then the next one will be performed.
   * SERVICE_NETWORKING_NOT_ENABLED and NETWORK_NOT_PEERED checks are performed
   * for all requests where validation is requested. NETWORK_NOT_FOUND and
   * NETWORK_DISCONNECTED checks are done for requests that have
   * validate_network set to true.
   *
   * @param bool $validateNetwork
   */
  public function setValidateNetwork($validateNetwork)
  {
    $this->validateNetwork = $validateNetwork;
  }
  /**
   * @return bool
   */
  public function getValidateNetwork()
  {
    return $this->validateNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidateConsumerConfigRequest::class, 'Google_Service_ServiceNetworking_ValidateConsumerConfigRequest');
