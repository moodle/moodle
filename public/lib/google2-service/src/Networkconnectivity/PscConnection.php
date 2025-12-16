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

namespace Google\Service\Networkconnectivity;

class PscConnection extends \Google\Model
{
  /**
   * An invalid error type as the default case.
   */
  public const ERROR_TYPE_CONNECTION_ERROR_TYPE_UNSPECIFIED = 'CONNECTION_ERROR_TYPE_UNSPECIFIED';
  /**
   * The error is due to Service Automation system internal.
   */
  public const ERROR_TYPE_ERROR_INTERNAL = 'ERROR_INTERNAL';
  /**
   * The error is due to the setup on consumer side.
   */
  public const ERROR_TYPE_ERROR_CONSUMER_SIDE = 'ERROR_CONSUMER_SIDE';
  /**
   * The error is due to the setup on producer side.
   */
  public const ERROR_TYPE_ERROR_PRODUCER_SIDE = 'ERROR_PRODUCER_SIDE';
  /**
   * Default value. We will use IPv4 or IPv6 depending on the IP version of
   * first available subnetwork.
   */
  public const IP_VERSION_IP_VERSION_UNSPECIFIED = 'IP_VERSION_UNSPECIFIED';
  /**
   * Will use IPv4 only.
   */
  public const IP_VERSION_IPV4 = 'IPV4';
  /**
   * Will use IPv6 only.
   */
  public const IP_VERSION_IPV6 = 'IPV6';
  /**
   * An invalid state as the default case.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The connection has been created successfully. However, for the up-to-date
   * connection status, please use the created forwarding rule's
   * "PscConnectionStatus" as the source of truth.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The connection is not functional since some resources on the connection
   * fail to be created.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The connection is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The connection is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The connection is being repaired to complete creation.
   */
  public const STATE_CREATE_REPAIRING = 'CREATE_REPAIRING';
  /**
   * The connection is being repaired to complete deletion.
   */
  public const STATE_DELETE_REPAIRING = 'DELETE_REPAIRING';
  /**
   * The resource reference of the consumer address.
   *
   * @var string
   */
  public $consumerAddress;
  /**
   * The resource reference of the PSC Forwarding Rule within the consumer VPC.
   *
   * @var string
   */
  public $consumerForwardingRule;
  /**
   * The project where the PSC connection is created.
   *
   * @var string
   */
  public $consumerTargetProject;
  protected $errorDataType = '';
  protected $errorInfoType = GoogleRpcErrorInfo::class;
  protected $errorInfoDataType = '';
  /**
   * The error type indicates whether the error is consumer facing, producer
   * facing or system internal.
   *
   * @deprecated
   * @var string
   */
  public $errorType;
  /**
   * The last Compute Engine operation to setup PSC connection.
   *
   * @var string
   */
  public $gceOperation;
  /**
   * The requested IP version for the PSC connection.
   *
   * @var string
   */
  public $ipVersion;
  /**
   * Immutable. Deprecated. Use producer_instance_metadata instead. An immutable
   * identifier for the producer instance.
   *
   * @deprecated
   * @var string
   */
  public $producerInstanceId;
  /**
   * Immutable. An immutable map for the producer instance metadata.
   *
   * @var string[]
   */
  public $producerInstanceMetadata;
  /**
   * The PSC connection id of the PSC forwarding rule.
   *
   * @var string
   */
  public $pscConnectionId;
  /**
   * Output only. The URI of the subnetwork selected to allocate IP address for
   * this connection.
   *
   * @var string
   */
  public $selectedSubnetwork;
  /**
   * Output only. [Output only] The service class associated with this PSC
   * Connection. The value is derived from the SCPolicy and matches the service
   * class name provided by the customer.
   *
   * @var string
   */
  public $serviceClass;
  /**
   * State of the PSC Connection
   *
   * @var string
   */
  public $state;

  /**
   * The resource reference of the consumer address.
   *
   * @param string $consumerAddress
   */
  public function setConsumerAddress($consumerAddress)
  {
    $this->consumerAddress = $consumerAddress;
  }
  /**
   * @return string
   */
  public function getConsumerAddress()
  {
    return $this->consumerAddress;
  }
  /**
   * The resource reference of the PSC Forwarding Rule within the consumer VPC.
   *
   * @param string $consumerForwardingRule
   */
  public function setConsumerForwardingRule($consumerForwardingRule)
  {
    $this->consumerForwardingRule = $consumerForwardingRule;
  }
  /**
   * @return string
   */
  public function getConsumerForwardingRule()
  {
    return $this->consumerForwardingRule;
  }
  /**
   * The project where the PSC connection is created.
   *
   * @param string $consumerTargetProject
   */
  public function setConsumerTargetProject($consumerTargetProject)
  {
    $this->consumerTargetProject = $consumerTargetProject;
  }
  /**
   * @return string
   */
  public function getConsumerTargetProject()
  {
    return $this->consumerTargetProject;
  }
  /**
   * The most recent error during operating this connection. Deprecated, please
   * use error_info instead.
   *
   * @deprecated
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @deprecated
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The error info for the latest error during operating this
   * connection.
   *
   * @param GoogleRpcErrorInfo $errorInfo
   */
  public function setErrorInfo(GoogleRpcErrorInfo $errorInfo)
  {
    $this->errorInfo = $errorInfo;
  }
  /**
   * @return GoogleRpcErrorInfo
   */
  public function getErrorInfo()
  {
    return $this->errorInfo;
  }
  /**
   * The error type indicates whether the error is consumer facing, producer
   * facing or system internal.
   *
   * Accepted values: CONNECTION_ERROR_TYPE_UNSPECIFIED, ERROR_INTERNAL,
   * ERROR_CONSUMER_SIDE, ERROR_PRODUCER_SIDE
   *
   * @deprecated
   * @param self::ERROR_TYPE_* $errorType
   */
  public function setErrorType($errorType)
  {
    $this->errorType = $errorType;
  }
  /**
   * @deprecated
   * @return self::ERROR_TYPE_*
   */
  public function getErrorType()
  {
    return $this->errorType;
  }
  /**
   * The last Compute Engine operation to setup PSC connection.
   *
   * @param string $gceOperation
   */
  public function setGceOperation($gceOperation)
  {
    $this->gceOperation = $gceOperation;
  }
  /**
   * @return string
   */
  public function getGceOperation()
  {
    return $this->gceOperation;
  }
  /**
   * The requested IP version for the PSC connection.
   *
   * Accepted values: IP_VERSION_UNSPECIFIED, IPV4, IPV6
   *
   * @param self::IP_VERSION_* $ipVersion
   */
  public function setIpVersion($ipVersion)
  {
    $this->ipVersion = $ipVersion;
  }
  /**
   * @return self::IP_VERSION_*
   */
  public function getIpVersion()
  {
    return $this->ipVersion;
  }
  /**
   * Immutable. Deprecated. Use producer_instance_metadata instead. An immutable
   * identifier for the producer instance.
   *
   * @deprecated
   * @param string $producerInstanceId
   */
  public function setProducerInstanceId($producerInstanceId)
  {
    $this->producerInstanceId = $producerInstanceId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProducerInstanceId()
  {
    return $this->producerInstanceId;
  }
  /**
   * Immutable. An immutable map for the producer instance metadata.
   *
   * @param string[] $producerInstanceMetadata
   */
  public function setProducerInstanceMetadata($producerInstanceMetadata)
  {
    $this->producerInstanceMetadata = $producerInstanceMetadata;
  }
  /**
   * @return string[]
   */
  public function getProducerInstanceMetadata()
  {
    return $this->producerInstanceMetadata;
  }
  /**
   * The PSC connection id of the PSC forwarding rule.
   *
   * @param string $pscConnectionId
   */
  public function setPscConnectionId($pscConnectionId)
  {
    $this->pscConnectionId = $pscConnectionId;
  }
  /**
   * @return string
   */
  public function getPscConnectionId()
  {
    return $this->pscConnectionId;
  }
  /**
   * Output only. The URI of the subnetwork selected to allocate IP address for
   * this connection.
   *
   * @param string $selectedSubnetwork
   */
  public function setSelectedSubnetwork($selectedSubnetwork)
  {
    $this->selectedSubnetwork = $selectedSubnetwork;
  }
  /**
   * @return string
   */
  public function getSelectedSubnetwork()
  {
    return $this->selectedSubnetwork;
  }
  /**
   * Output only. [Output only] The service class associated with this PSC
   * Connection. The value is derived from the SCPolicy and matches the service
   * class name provided by the customer.
   *
   * @param string $serviceClass
   */
  public function setServiceClass($serviceClass)
  {
    $this->serviceClass = $serviceClass;
  }
  /**
   * @return string
   */
  public function getServiceClass()
  {
    return $this->serviceClass;
  }
  /**
   * State of the PSC Connection
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, FAILED, CREATING, DELETING,
   * CREATE_REPAIRING, DELETE_REPAIRING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscConnection::class, 'Google_Service_Networkconnectivity_PscConnection');
