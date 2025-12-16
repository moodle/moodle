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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1EndpointAttachment extends \Google\Model
{
  /**
   * The connection state has not been set.
   */
  public const CONNECTION_STATE_CONNECTION_STATE_UNSPECIFIED = 'CONNECTION_STATE_UNSPECIFIED';
  /**
   * The connection state is unavailable at this time, possibly because the
   * endpoint attachment is currently being provisioned.
   */
  public const CONNECTION_STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * The connection is pending acceptance by the PSC producer.
   */
  public const CONNECTION_STATE_PENDING = 'PENDING';
  /**
   * The connection has been accepted by the PSC producer.
   */
  public const CONNECTION_STATE_ACCEPTED = 'ACCEPTED';
  /**
   * The connection has been rejected by the PSC producer.
   */
  public const CONNECTION_STATE_REJECTED = 'REJECTED';
  /**
   * The connection has been closed by the PSC producer and will not serve
   * traffic going forward.
   */
  public const CONNECTION_STATE_CLOSED = 'CLOSED';
  /**
   * The connection has been frozen by the PSC producer and will not serve
   * traffic.
   */
  public const CONNECTION_STATE_FROZEN = 'FROZEN';
  /**
   * The connection has been accepted by the PSC producer, but it is not ready
   * to serve the traffic due to producer side issues.
   */
  public const CONNECTION_STATE_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * Resource is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is provisioned and ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Output only. State of the endpoint attachment connection to the service
   * attachment.
   *
   * @var string
   */
  public $connectionState;
  /**
   * Output only. Host that can be used in either the HTTP target endpoint
   * directly or as the host in target server.
   *
   * @var string
   */
  public $host;
  /**
   * Required. Location of the endpoint attachment.
   *
   * @var string
   */
  public $location;
  /**
   * Name of the endpoint attachment. Use the following structure in your
   * request: `organizations/{org}/endpointAttachments/{endpoint_attachment}`
   *
   * @var string
   */
  public $name;
  /**
   * Format: projects/regions/serviceAttachments
   *
   * @var string
   */
  public $serviceAttachment;
  /**
   * Output only. State of the endpoint attachment. Values other than `ACTIVE`
   * mean the resource is not ready to use.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. State of the endpoint attachment connection to the service
   * attachment.
   *
   * Accepted values: CONNECTION_STATE_UNSPECIFIED, UNAVAILABLE, PENDING,
   * ACCEPTED, REJECTED, CLOSED, FROZEN, NEEDS_ATTENTION
   *
   * @param self::CONNECTION_STATE_* $connectionState
   */
  public function setConnectionState($connectionState)
  {
    $this->connectionState = $connectionState;
  }
  /**
   * @return self::CONNECTION_STATE_*
   */
  public function getConnectionState()
  {
    return $this->connectionState;
  }
  /**
   * Output only. Host that can be used in either the HTTP target endpoint
   * directly or as the host in target server.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Required. Location of the endpoint attachment.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Name of the endpoint attachment. Use the following structure in your
   * request: `organizations/{org}/endpointAttachments/{endpoint_attachment}`
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
   * Format: projects/regions/serviceAttachments
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
  /**
   * Output only. State of the endpoint attachment. Values other than `ACTIVE`
   * mean the resource is not ready to use.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, UPDATING
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
class_alias(GoogleCloudApigeeV1EndpointAttachment::class, 'Google_Service_Apigee_GoogleCloudApigeeV1EndpointAttachment');
