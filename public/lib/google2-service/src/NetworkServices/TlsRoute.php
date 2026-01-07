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

namespace Google\Service\NetworkServices;

class TlsRoute extends \Google\Collection
{
  protected $collection_key = 'rules';
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Gateways defines a list of gateways this TlsRoute is attached to,
   * as one of the routing rules to route the requests served by the gateway.
   * Each gateway reference should match the pattern:
   * `projects/locations/gateways/`
   *
   * @var string[]
   */
  public $gateways;
  /**
   * Optional. Set of label tags associated with the TlsRoute resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Meshes defines a list of meshes this TlsRoute is attached to, as
   * one of the routing rules to route the requests served by the mesh. Each
   * mesh reference should match the pattern: `projects/locations/meshes/` The
   * attached Mesh should be of a type SIDECAR
   *
   * @var string[]
   */
  public $meshes;
  /**
   * Identifier. Name of the TlsRoute resource. It matches pattern
   * `projects/locations/tlsRoutes/tls_route_name>`.
   *
   * @var string
   */
  public $name;
  protected $rulesType = TlsRouteRouteRule::class;
  protected $rulesDataType = 'array';
  /**
   * Output only. Server-defined URL of this resource
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Gateways defines a list of gateways this TlsRoute is attached to,
   * as one of the routing rules to route the requests served by the gateway.
   * Each gateway reference should match the pattern:
   * `projects/locations/gateways/`
   *
   * @param string[] $gateways
   */
  public function setGateways($gateways)
  {
    $this->gateways = $gateways;
  }
  /**
   * @return string[]
   */
  public function getGateways()
  {
    return $this->gateways;
  }
  /**
   * Optional. Set of label tags associated with the TlsRoute resource.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. Meshes defines a list of meshes this TlsRoute is attached to, as
   * one of the routing rules to route the requests served by the mesh. Each
   * mesh reference should match the pattern: `projects/locations/meshes/` The
   * attached Mesh should be of a type SIDECAR
   *
   * @param string[] $meshes
   */
  public function setMeshes($meshes)
  {
    $this->meshes = $meshes;
  }
  /**
   * @return string[]
   */
  public function getMeshes()
  {
    return $this->meshes;
  }
  /**
   * Identifier. Name of the TlsRoute resource. It matches pattern
   * `projects/locations/tlsRoutes/tls_route_name>`.
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
   * Required. Rules that define how traffic is routed and handled. At least one
   * RouteRule must be supplied. If there are multiple rules then the action
   * taken will be the first rule to match.
   *
   * @param TlsRouteRouteRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return TlsRouteRouteRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Output only. Server-defined URL of this resource
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TlsRoute::class, 'Google_Service_NetworkServices_TlsRoute');
