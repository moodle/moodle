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

class EndpointPolicy extends \Google\Model
{
  /**
   * Default value. Must not be used.
   */
  public const TYPE_ENDPOINT_POLICY_TYPE_UNSPECIFIED = 'ENDPOINT_POLICY_TYPE_UNSPECIFIED';
  /**
   * Represents a proxy deployed as a sidecar.
   */
  public const TYPE_SIDECAR_PROXY = 'SIDECAR_PROXY';
  /**
   * Represents a proxyless gRPC backend.
   */
  public const TYPE_GRPC_SERVER = 'GRPC_SERVER';
  /**
   * Optional. This field specifies the URL of AuthorizationPolicy resource that
   * applies authorization policies to the inbound traffic at the matched
   * endpoints. Refer to Authorization. If this field is not specified,
   * authorization is disabled(no authz checks) for this endpoint.
   *
   * @var string
   */
  public $authorizationPolicy;
  /**
   * Optional. A URL referring to a ClientTlsPolicy resource. ClientTlsPolicy
   * can be set to specify the authentication for traffic from the proxy to the
   * actual endpoints. More specifically, it is applied to the outgoing traffic
   * from the proxy to the endpoint. This is typically used for sidecar model
   * where the proxy identifies itself as endpoint to the control plane, with
   * the connection between sidecar and endpoint requiring authentication. If
   * this field is not set, authentication is disabled(open). Applicable only
   * when EndpointPolicyType is SIDECAR_PROXY.
   *
   * @var string
   */
  public $clientTlsPolicy;
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
  protected $endpointMatcherType = EndpointMatcher::class;
  protected $endpointMatcherDataType = '';
  /**
   * Optional. Set of label tags associated with the EndpointPolicy resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the EndpointPolicy resource. It matches pattern
   * `projects/{project}/locations/endpointPolicies/{endpoint_policy}`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. A URL referring to ServerTlsPolicy resource. ServerTlsPolicy is
   * used to determine the authentication policy to be applied to terminate the
   * inbound traffic at the identified backends. If this field is not set,
   * authentication is disabled(open) for this endpoint.
   *
   * @var string
   */
  public $serverTlsPolicy;
  protected $trafficPortSelectorType = TrafficPortSelector::class;
  protected $trafficPortSelectorDataType = '';
  /**
   * Required. The type of endpoint policy. This is primarily used to validate
   * the configuration.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. This field specifies the URL of AuthorizationPolicy resource that
   * applies authorization policies to the inbound traffic at the matched
   * endpoints. Refer to Authorization. If this field is not specified,
   * authorization is disabled(no authz checks) for this endpoint.
   *
   * @param string $authorizationPolicy
   */
  public function setAuthorizationPolicy($authorizationPolicy)
  {
    $this->authorizationPolicy = $authorizationPolicy;
  }
  /**
   * @return string
   */
  public function getAuthorizationPolicy()
  {
    return $this->authorizationPolicy;
  }
  /**
   * Optional. A URL referring to a ClientTlsPolicy resource. ClientTlsPolicy
   * can be set to specify the authentication for traffic from the proxy to the
   * actual endpoints. More specifically, it is applied to the outgoing traffic
   * from the proxy to the endpoint. This is typically used for sidecar model
   * where the proxy identifies itself as endpoint to the control plane, with
   * the connection between sidecar and endpoint requiring authentication. If
   * this field is not set, authentication is disabled(open). Applicable only
   * when EndpointPolicyType is SIDECAR_PROXY.
   *
   * @param string $clientTlsPolicy
   */
  public function setClientTlsPolicy($clientTlsPolicy)
  {
    $this->clientTlsPolicy = $clientTlsPolicy;
  }
  /**
   * @return string
   */
  public function getClientTlsPolicy()
  {
    return $this->clientTlsPolicy;
  }
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
   * Required. A matcher that selects endpoints to which the policies should be
   * applied.
   *
   * @param EndpointMatcher $endpointMatcher
   */
  public function setEndpointMatcher(EndpointMatcher $endpointMatcher)
  {
    $this->endpointMatcher = $endpointMatcher;
  }
  /**
   * @return EndpointMatcher
   */
  public function getEndpointMatcher()
  {
    return $this->endpointMatcher;
  }
  /**
   * Optional. Set of label tags associated with the EndpointPolicy resource.
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
   * Identifier. Name of the EndpointPolicy resource. It matches pattern
   * `projects/{project}/locations/endpointPolicies/{endpoint_policy}`.
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
   * Optional. A URL referring to ServerTlsPolicy resource. ServerTlsPolicy is
   * used to determine the authentication policy to be applied to terminate the
   * inbound traffic at the identified backends. If this field is not set,
   * authentication is disabled(open) for this endpoint.
   *
   * @param string $serverTlsPolicy
   */
  public function setServerTlsPolicy($serverTlsPolicy)
  {
    $this->serverTlsPolicy = $serverTlsPolicy;
  }
  /**
   * @return string
   */
  public function getServerTlsPolicy()
  {
    return $this->serverTlsPolicy;
  }
  /**
   * Optional. Port selector for the (matched) endpoints. If no port selector is
   * provided, the matched config is applied to all ports.
   *
   * @param TrafficPortSelector $trafficPortSelector
   */
  public function setTrafficPortSelector(TrafficPortSelector $trafficPortSelector)
  {
    $this->trafficPortSelector = $trafficPortSelector;
  }
  /**
   * @return TrafficPortSelector
   */
  public function getTrafficPortSelector()
  {
    return $this->trafficPortSelector;
  }
  /**
   * Required. The type of endpoint policy. This is primarily used to validate
   * the configuration.
   *
   * Accepted values: ENDPOINT_POLICY_TYPE_UNSPECIFIED, SIDECAR_PROXY,
   * GRPC_SERVER
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
class_alias(EndpointPolicy::class, 'Google_Service_NetworkServices_EndpointPolicy');
