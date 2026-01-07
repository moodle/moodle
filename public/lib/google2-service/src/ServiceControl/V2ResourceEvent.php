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

namespace Google\Service\ServiceControl;

class V2ResourceEvent extends \Google\Model
{
  /**
   * Default value. Do not use.
   */
  public const PATH_API_PATH_UNSPECIFIED = 'API_PATH_UNSPECIFIED';
  /**
   * The request path.
   */
  public const PATH_REQUEST = 'REQUEST';
  /**
   * The response path.
   */
  public const PATH_RESPONSE = 'RESPONSE';
  /**
   * The resource event type is unclear. We do not expect any events to fall
   * into this category.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The resource is created/inserted.
   */
  public const TYPE_CREATE = 'CREATE';
  /**
   * The resource is updated.
   */
  public const TYPE_UPDATE = 'UPDATE';
  /**
   * The resource is deleted.
   */
  public const TYPE_DELETE = 'DELETE';
  /**
   * The resource is un-deleted.
   */
  public const TYPE_UNDELETE = 'UNDELETE';
  /**
   * The ESF unique context id of the api request, from which this resource
   * event originated. This field is only needed for CAIS integration via api
   * annotation. See go/cais-lro-delete for more details.
   *
   * @var string
   */
  public $contextId;
  /**
   * The destinations field determines which backend services should handle the
   * event. This should be specified as a comma-delimited string.
   *
   * @var string
   */
  public $destinations;
  protected $parentType = ServicecontrolResource::class;
  protected $parentDataType = '';
  /**
   * The api path the resource event was created in. This should match the
   * source of the `payload` field. For direct integrations with Chemist, this
   * should generally be the RESPONSE. go/resource-event-pipeline-type
   *
   * @var string
   */
  public $path;
  /**
   * The payload contains metadata associated with the resource event. A
   * ResourceEventPayloadStatus is provided instead if the original payload
   * cannot be returned due to a limitation (e.g. size limit).
   *
   * @var array[]
   */
  public $payload;
  protected $resourceType = ServicecontrolResource::class;
  protected $resourceDataType = '';
  /**
   * The resource event type determines how the backend service should process
   * the event.
   *
   * @var string
   */
  public $type;

  /**
   * The ESF unique context id of the api request, from which this resource
   * event originated. This field is only needed for CAIS integration via api
   * annotation. See go/cais-lro-delete for more details.
   *
   * @param string $contextId
   */
  public function setContextId($contextId)
  {
    $this->contextId = $contextId;
  }
  /**
   * @return string
   */
  public function getContextId()
  {
    return $this->contextId;
  }
  /**
   * The destinations field determines which backend services should handle the
   * event. This should be specified as a comma-delimited string.
   *
   * @param string $destinations
   */
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  /**
   * @return string
   */
  public function getDestinations()
  {
    return $this->destinations;
  }
  /**
   * The parent resource for the resource.
   *
   * @param ServicecontrolResource $parent
   */
  public function setParent(ServicecontrolResource $parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return ServicecontrolResource
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * The api path the resource event was created in. This should match the
   * source of the `payload` field. For direct integrations with Chemist, this
   * should generally be the RESPONSE. go/resource-event-pipeline-type
   *
   * Accepted values: API_PATH_UNSPECIFIED, REQUEST, RESPONSE
   *
   * @param self::PATH_* $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return self::PATH_*
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * The payload contains metadata associated with the resource event. A
   * ResourceEventPayloadStatus is provided instead if the original payload
   * cannot be returned due to a limitation (e.g. size limit).
   *
   * @param array[] $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array[]
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * The resource associated with the event.
   *
   * @param ServicecontrolResource $resource
   */
  public function setResource(ServicecontrolResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return ServicecontrolResource
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The resource event type determines how the backend service should process
   * the event.
   *
   * Accepted values: TYPE_UNSPECIFIED, CREATE, UPDATE, DELETE, UNDELETE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V2ResourceEvent::class, 'Google_Service_ServiceControl_V2ResourceEvent');
