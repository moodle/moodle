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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2AccessReview extends \Google\Model
{
  /**
   * The API group of the resource. "*" means all.
   *
   * @var string
   */
  public $group;
  /**
   * The name of the resource being requested. Empty means all.
   *
   * @var string
   */
  public $name;
  /**
   * Namespace of the action being requested. Currently, there is no distinction
   * between no namespace and all namespaces. Both are represented by ""
   * (empty).
   *
   * @var string
   */
  public $ns;
  /**
   * The optional resource type requested. "*" means all.
   *
   * @var string
   */
  public $resource;
  /**
   * The optional subresource type.
   *
   * @var string
   */
  public $subresource;
  /**
   * A Kubernetes resource API verb, like get, list, watch, create, update,
   * delete, proxy. "*" means all.
   *
   * @var string
   */
  public $verb;
  /**
   * The API version of the resource. "*" means all.
   *
   * @var string
   */
  public $version;

  /**
   * The API group of the resource. "*" means all.
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * The name of the resource being requested. Empty means all.
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
   * Namespace of the action being requested. Currently, there is no distinction
   * between no namespace and all namespaces. Both are represented by ""
   * (empty).
   *
   * @param string $ns
   */
  public function setNs($ns)
  {
    $this->ns = $ns;
  }
  /**
   * @return string
   */
  public function getNs()
  {
    return $this->ns;
  }
  /**
   * The optional resource type requested. "*" means all.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The optional subresource type.
   *
   * @param string $subresource
   */
  public function setSubresource($subresource)
  {
    $this->subresource = $subresource;
  }
  /**
   * @return string
   */
  public function getSubresource()
  {
    return $this->subresource;
  }
  /**
   * A Kubernetes resource API verb, like get, list, watch, create, update,
   * delete, proxy. "*" means all.
   *
   * @param string $verb
   */
  public function setVerb($verb)
  {
    $this->verb = $verb;
  }
  /**
   * @return string
   */
  public function getVerb()
  {
    return $this->verb;
  }
  /**
   * The API version of the resource. "*" means all.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2AccessReview::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2AccessReview');
