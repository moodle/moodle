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

namespace Google\Service\CloudRedis;

class PscAutoConnection extends \Google\Model
{
  /**
   * Cluster endpoint Type is not set
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_UNSPECIFIED = 'CONNECTION_TYPE_UNSPECIFIED';
  /**
   * Cluster endpoint that will be used as for cluster topology discovery.
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_DISCOVERY = 'CONNECTION_TYPE_DISCOVERY';
  /**
   * Cluster endpoint that will be used as primary endpoint to access primary.
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_PRIMARY = 'CONNECTION_TYPE_PRIMARY';
  /**
   * Cluster endpoint that will be used as reader endpoint to access replicas.
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_READER = 'CONNECTION_TYPE_READER';
  /**
   * PSC connection status is not specified.
   */
  public const PSC_CONNECTION_STATUS_PSC_CONNECTION_STATUS_UNSPECIFIED = 'PSC_CONNECTION_STATUS_UNSPECIFIED';
  /**
   * The connection is active
   */
  public const PSC_CONNECTION_STATUS_PSC_CONNECTION_STATUS_ACTIVE = 'PSC_CONNECTION_STATUS_ACTIVE';
  /**
   * Connection not found
   */
  public const PSC_CONNECTION_STATUS_PSC_CONNECTION_STATUS_NOT_FOUND = 'PSC_CONNECTION_STATUS_NOT_FOUND';
  /**
   * Output only. The IP allocated on the consumer network for the PSC
   * forwarding rule.
   *
   * @var string
   */
  public $address;
  /**
   * Output only. Type of the PSC connection.
   *
   * @var string
   */
  public $connectionType;
  /**
   * Output only. The URI of the consumer side forwarding rule. Example:
   * projects/{projectNumOrId}/regions/us-east1/forwardingRules/{resourceId}.
   *
   * @var string
   */
  public $forwardingRule;
  /**
   * Required. The consumer network where the IP address resides, in the form of
   * projects/{project_id}/global/networks/{network_id}.
   *
   * @var string
   */
  public $network;
  /**
   * Required. The consumer project_id where the forwarding rule is created
   * from.
   *
   * @var string
   */
  public $projectId;
  /**
   * Output only. The PSC connection id of the forwarding rule connected to the
   * service attachment.
   *
   * @var string
   */
  public $pscConnectionId;
  /**
   * Output only. The status of the PSC connection. Please note that this value
   * is updated periodically. Please use Private Service Connect APIs for the
   * latest status.
   *
   * @var string
   */
  public $pscConnectionStatus;
  /**
   * Output only. The service attachment which is the target of the PSC
   * connection, in the form of projects/{project-
   * id}/regions/{region}/serviceAttachments/{service-attachment-id}.
   *
   * @var string
   */
  public $serviceAttachment;

  /**
   * Output only. The IP allocated on the consumer network for the PSC
   * forwarding rule.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Output only. Type of the PSC connection.
   *
   * Accepted values: CONNECTION_TYPE_UNSPECIFIED, CONNECTION_TYPE_DISCOVERY,
   * CONNECTION_TYPE_PRIMARY, CONNECTION_TYPE_READER
   *
   * @param self::CONNECTION_TYPE_* $connectionType
   */
  public function setConnectionType($connectionType)
  {
    $this->connectionType = $connectionType;
  }
  /**
   * @return self::CONNECTION_TYPE_*
   */
  public function getConnectionType()
  {
    return $this->connectionType;
  }
  /**
   * Output only. The URI of the consumer side forwarding rule. Example:
   * projects/{projectNumOrId}/regions/us-east1/forwardingRules/{resourceId}.
   *
   * @param string $forwardingRule
   */
  public function setForwardingRule($forwardingRule)
  {
    $this->forwardingRule = $forwardingRule;
  }
  /**
   * @return string
   */
  public function getForwardingRule()
  {
    return $this->forwardingRule;
  }
  /**
   * Required. The consumer network where the IP address resides, in the form of
   * projects/{project_id}/global/networks/{network_id}.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Required. The consumer project_id where the forwarding rule is created
   * from.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Output only. The PSC connection id of the forwarding rule connected to the
   * service attachment.
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
   * Output only. The status of the PSC connection. Please note that this value
   * is updated periodically. Please use Private Service Connect APIs for the
   * latest status.
   *
   * Accepted values: PSC_CONNECTION_STATUS_UNSPECIFIED,
   * PSC_CONNECTION_STATUS_ACTIVE, PSC_CONNECTION_STATUS_NOT_FOUND
   *
   * @param self::PSC_CONNECTION_STATUS_* $pscConnectionStatus
   */
  public function setPscConnectionStatus($pscConnectionStatus)
  {
    $this->pscConnectionStatus = $pscConnectionStatus;
  }
  /**
   * @return self::PSC_CONNECTION_STATUS_*
   */
  public function getPscConnectionStatus()
  {
    return $this->pscConnectionStatus;
  }
  /**
   * Output only. The service attachment which is the target of the PSC
   * connection, in the form of projects/{project-
   * id}/regions/{region}/serviceAttachments/{service-attachment-id}.
   *
   * @param string $serviceAttachment
   */
  public function setServiceAttachment($serviceAttachment)
  {
    $this->serviceAttachment = $serviceAttachment;
  }
  /**
   * @return string
   */
  public function getServiceAttachment()
  {
    return $this->serviceAttachment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscAutoConnection::class, 'Google_Service_CloudRedis_PscAutoConnection');
