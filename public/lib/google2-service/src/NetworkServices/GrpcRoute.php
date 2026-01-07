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

class GrpcRoute extends \Google\Collection
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
   * Optional. Gateways defines a list of gateways this GrpcRoute is attached
   * to, as one of the routing rules to route the requests served by the
   * gateway. Each gateway reference should match the pattern:
   * `projects/locations/gateways/`
   *
   * @var string[]
   */
  public $gateways;
  /**
   * Required. Service hostnames with an optional port for which this route
   * describes traffic. Format: [:] Hostname is the fully qualified domain name
   * of a network host. This matches the RFC 1123 definition of a hostname with
   * 2 notable exceptions: - IPs are not allowed. - A hostname may be prefixed
   * with a wildcard label (`*.`). The wildcard label must appear by itself as
   * the first label. Hostname can be "precise" which is a domain name without
   * the terminating dot of a network host (e.g. `foo.example.com`) or
   * "wildcard", which is a domain name prefixed with a single wildcard label
   * (e.g. `*.example.com`). Note that as per RFC1035 and RFC1123, a label must
   * consist of lower case alphanumeric characters or '-', and must start and
   * end with an alphanumeric character. No other punctuation is allowed. The
   * routes associated with a Mesh or Gateway must have unique hostnames. If you
   * attempt to attach multiple routes with conflicting hostnames, the
   * configuration will be rejected. For example, while it is acceptable for
   * routes for the hostnames `*.foo.bar.com` and `*.bar.com` to be associated
   * with the same route, it is not possible to associate two routes both with
   * `*.bar.com` or both with `bar.com`. If a port is specified, then gRPC
   * clients must use the channel URI with the port to match this rule (i.e.
   * "xds:service:123"), otherwise they must supply the URI without a port (i.e.
   * "xds:service").
   *
   * @var string[]
   */
  public $hostnames;
  /**
   * Optional. Set of label tags associated with the GrpcRoute resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Meshes defines a list of meshes this GrpcRoute is attached to, as
   * one of the routing rules to route the requests served by the mesh. Each
   * mesh reference should match the pattern: `projects/locations/meshes/`
   *
   * @var string[]
   */
  public $meshes;
  /**
   * Identifier. Name of the GrpcRoute resource. It matches pattern
   * `projects/locations/grpcRoutes/`
   *
   * @var string
   */
  public $name;
  protected $rulesType = GrpcRouteRouteRule::class;
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
   * Optional. Gateways defines a list of gateways this GrpcRoute is attached
   * to, as one of the routing rules to route the requests served by the
   * gateway. Each gateway reference should match the pattern:
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
   * Required. Service hostnames with an optional port for which this route
   * describes traffic. Format: [:] Hostname is the fully qualified domain name
   * of a network host. This matches the RFC 1123 definition of a hostname with
   * 2 notable exceptions: - IPs are not allowed. - A hostname may be prefixed
   * with a wildcard label (`*.`). The wildcard label must appear by itself as
   * the first label. Hostname can be "precise" which is a domain name without
   * the terminating dot of a network host (e.g. `foo.example.com`) or
   * "wildcard", which is a domain name prefixed with a single wildcard label
   * (e.g. `*.example.com`). Note that as per RFC1035 and RFC1123, a label must
   * consist of lower case alphanumeric characters or '-', and must start and
   * end with an alphanumeric character. No other punctuation is allowed. The
   * routes associated with a Mesh or Gateway must have unique hostnames. If you
   * attempt to attach multiple routes with conflicting hostnames, the
   * configuration will be rejected. For example, while it is acceptable for
   * routes for the hostnames `*.foo.bar.com` and `*.bar.com` to be associated
   * with the same route, it is not possible to associate two routes both with
   * `*.bar.com` or both with `bar.com`. If a port is specified, then gRPC
   * clients must use the channel URI with the port to match this rule (i.e.
   * "xds:service:123"), otherwise they must supply the URI without a port (i.e.
   * "xds:service").
   *
   * @param string[] $hostnames
   */
  public function setHostnames($hostnames)
  {
    $this->hostnames = $hostnames;
  }
  /**
   * @return string[]
   */
  public function getHostnames()
  {
    return $this->hostnames;
  }
  /**
   * Optional. Set of label tags associated with the GrpcRoute resource.
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
   * Optional. Meshes defines a list of meshes this GrpcRoute is attached to, as
   * one of the routing rules to route the requests served by the mesh. Each
   * mesh reference should match the pattern: `projects/locations/meshes/`
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
   * Identifier. Name of the GrpcRoute resource. It matches pattern
   * `projects/locations/grpcRoutes/`
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
   * Required. A list of detailed rules defining how to route traffic. Within a
   * single GrpcRoute, the GrpcRoute.RouteAction associated with the first
   * matching GrpcRoute.RouteRule will be executed. At least one rule must be
   * supplied.
   *
   * @param GrpcRouteRouteRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return GrpcRouteRouteRule[]
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
class_alias(GrpcRoute::class, 'Google_Service_NetworkServices_GrpcRoute');
