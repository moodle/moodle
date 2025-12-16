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

class Endpoint extends \Google\Collection
{
  protected $collection_key = 'aliases';
  /**
   * Aliases for this endpoint, these will be served by the same UrlMap as the
   * parent endpoint, and will be provisioned in the GCP stack for the Regional
   * Endpoints.
   *
   * @var string[]
   */
  public $aliases;
  /**
   * Allowing [CORS](https://en.wikipedia.org/wiki/Cross-
   * origin_resource_sharing), aka cross-domain traffic, would allow the
   * backends served from this endpoint to receive and respond to HTTP OPTIONS
   * requests. The response will be used by the browser to determine whether the
   * subsequent cross-origin request is allowed to proceed.
   *
   * @var bool
   */
  public $allowCors;
  /**
   * The canonical name of this endpoint.
   *
   * @var string
   */
  public $name;
  /**
   * The specification of an Internet routable address of API frontend that will
   * handle requests to this [API
   * Endpoint](https://cloud.google.com/apis/design/glossary). It should be
   * either a valid IPv4 address or a fully-qualified domain name. For example,
   * "8.8.8.8" or "myservice.appspot.com".
   *
   * @var string
   */
  public $target;

  /**
   * Aliases for this endpoint, these will be served by the same UrlMap as the
   * parent endpoint, and will be provisioned in the GCP stack for the Regional
   * Endpoints.
   *
   * @param string[] $aliases
   */
  public function setAliases($aliases)
  {
    $this->aliases = $aliases;
  }
  /**
   * @return string[]
   */
  public function getAliases()
  {
    return $this->aliases;
  }
  /**
   * Allowing [CORS](https://en.wikipedia.org/wiki/Cross-
   * origin_resource_sharing), aka cross-domain traffic, would allow the
   * backends served from this endpoint to receive and respond to HTTP OPTIONS
   * requests. The response will be used by the browser to determine whether the
   * subsequent cross-origin request is allowed to proceed.
   *
   * @param bool $allowCors
   */
  public function setAllowCors($allowCors)
  {
    $this->allowCors = $allowCors;
  }
  /**
   * @return bool
   */
  public function getAllowCors()
  {
    return $this->allowCors;
  }
  /**
   * The canonical name of this endpoint.
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
   * The specification of an Internet routable address of API frontend that will
   * handle requests to this [API
   * Endpoint](https://cloud.google.com/apis/design/glossary). It should be
   * either a valid IPv4 address or a fully-qualified domain name. For example,
   * "8.8.8.8" or "myservice.appspot.com".
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Endpoint::class, 'Google_Service_ServiceNetworking_Endpoint');
