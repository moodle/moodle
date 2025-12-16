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

class AttributeContext extends \Google\Collection
{
  protected $collection_key = 'extensions';
  protected $apiType = Api::class;
  protected $apiDataType = '';
  protected $destinationType = Peer::class;
  protected $destinationDataType = '';
  /**
   * Supports extensions for advanced use cases, such as logs and metrics.
   *
   * @var array[]
   */
  public $extensions;
  protected $originType = Peer::class;
  protected $originDataType = '';
  protected $requestType = Request::class;
  protected $requestDataType = '';
  protected $resourceType = ServicecontrolResource::class;
  protected $resourceDataType = '';
  protected $responseType = Response::class;
  protected $responseDataType = '';
  protected $sourceType = Peer::class;
  protected $sourceDataType = '';

  /**
   * Represents an API operation that is involved to a network activity.
   *
   * @param Api $api
   */
  public function setApi(Api $api)
  {
    $this->api = $api;
  }
  /**
   * @return Api
   */
  public function getApi()
  {
    return $this->api;
  }
  /**
   * The destination of a network activity, such as accepting a TCP connection.
   * In a multi hop network activity, the destination represents the receiver of
   * the last hop.
   *
   * @param Peer $destination
   */
  public function setDestination(Peer $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return Peer
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Supports extensions for advanced use cases, such as logs and metrics.
   *
   * @param array[] $extensions
   */
  public function setExtensions($extensions)
  {
    $this->extensions = $extensions;
  }
  /**
   * @return array[]
   */
  public function getExtensions()
  {
    return $this->extensions;
  }
  /**
   * The origin of a network activity. In a multi hop network activity, the
   * origin represents the sender of the first hop. For the first hop, the
   * `source` and the `origin` must have the same content.
   *
   * @param Peer $origin
   */
  public function setOrigin(Peer $origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return Peer
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * Represents a network request, such as an HTTP request.
   *
   * @param Request $request
   */
  public function setRequest(Request $request)
  {
    $this->request = $request;
  }
  /**
   * @return Request
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Represents a target resource that is involved with a network activity. If
   * multiple resources are involved with an activity, this must be the primary
   * one.
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
   * Represents a network response, such as an HTTP response.
   *
   * @param Response $response
   */
  public function setResponse(Response $response)
  {
    $this->response = $response;
  }
  /**
   * @return Response
   */
  public function getResponse()
  {
    return $this->response;
  }
  /**
   * The source of a network activity, such as starting a TCP connection. In a
   * multi hop network activity, the source represents the sender of the last
   * hop.
   *
   * @param Peer $source
   */
  public function setSource(Peer $source)
  {
    $this->source = $source;
  }
  /**
   * @return Peer
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttributeContext::class, 'Google_Service_ServiceControl_AttributeContext');
