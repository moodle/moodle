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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstream extends \Google\Model
{
  protected $egressPolicyType = GoogleCloudBeyondcorpSecuritygatewaysV1EgressPolicy::class;
  protected $egressPolicyDataType = '';
  protected $externalType = GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstreamExternal::class;
  protected $externalDataType = '';
  protected $networkType = GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstreamNetwork::class;
  protected $networkDataType = '';
  protected $proxyProtocolType = GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig::class;
  protected $proxyProtocolDataType = '';

  /**
   * Optional. Routing policy information.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1EgressPolicy $egressPolicy
   */
  public function setEgressPolicy(GoogleCloudBeyondcorpSecuritygatewaysV1EgressPolicy $egressPolicy)
  {
    $this->egressPolicy = $egressPolicy;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1EgressPolicy
   */
  public function getEgressPolicy()
  {
    return $this->egressPolicy;
  }
  /**
   * List of the external endpoints to forward traffic to.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstreamExternal $external
   */
  public function setExternal(GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstreamExternal $external)
  {
    $this->external = $external;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstreamExternal
   */
  public function getExternal()
  {
    return $this->external;
  }
  /**
   * Network to forward traffic to.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstreamNetwork $network
   */
  public function setNetwork(GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstreamNetwork $network)
  {
    $this->network = $network;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstreamNetwork
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. Enables proxy protocol configuration for the upstream.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig $proxyProtocol
   */
  public function setProxyProtocol(GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig $proxyProtocol)
  {
    $this->proxyProtocol = $proxyProtocol;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig
   */
  public function getProxyProtocol()
  {
    return $this->proxyProtocol;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstream::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpSecuritygatewaysV1ApplicationUpstream');
