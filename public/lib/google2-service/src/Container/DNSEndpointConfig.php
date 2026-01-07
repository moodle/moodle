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

namespace Google\Service\Container;

class DNSEndpointConfig extends \Google\Model
{
  /**
   * Controls whether user traffic is allowed over this endpoint. Note that
   * Google-managed services may still use the endpoint even if this is false.
   *
   * @var bool
   */
  public $allowExternalTraffic;
  /**
   * Controls whether the k8s certs auth is allowed via DNS.
   *
   * @var bool
   */
  public $enableK8sCertsViaDns;
  /**
   * Controls whether the k8s token auth is allowed via DNS.
   *
   * @var bool
   */
  public $enableK8sTokensViaDns;
  /**
   * Output only. The cluster's DNS endpoint configuration. A DNS format
   * address. This is accessible from the public internet. Ex: uid.us-
   * central1.gke.goog. Always present, but the behavior may change according to
   * the value of DNSEndpointConfig.allow_external_traffic.
   *
   * @var string
   */
  public $endpoint;

  /**
   * Controls whether user traffic is allowed over this endpoint. Note that
   * Google-managed services may still use the endpoint even if this is false.
   *
   * @param bool $allowExternalTraffic
   */
  public function setAllowExternalTraffic($allowExternalTraffic)
  {
    $this->allowExternalTraffic = $allowExternalTraffic;
  }
  /**
   * @return bool
   */
  public function getAllowExternalTraffic()
  {
    return $this->allowExternalTraffic;
  }
  /**
   * Controls whether the k8s certs auth is allowed via DNS.
   *
   * @param bool $enableK8sCertsViaDns
   */
  public function setEnableK8sCertsViaDns($enableK8sCertsViaDns)
  {
    $this->enableK8sCertsViaDns = $enableK8sCertsViaDns;
  }
  /**
   * @return bool
   */
  public function getEnableK8sCertsViaDns()
  {
    return $this->enableK8sCertsViaDns;
  }
  /**
   * Controls whether the k8s token auth is allowed via DNS.
   *
   * @param bool $enableK8sTokensViaDns
   */
  public function setEnableK8sTokensViaDns($enableK8sTokensViaDns)
  {
    $this->enableK8sTokensViaDns = $enableK8sTokensViaDns;
  }
  /**
   * @return bool
   */
  public function getEnableK8sTokensViaDns()
  {
    return $this->enableK8sTokensViaDns;
  }
  /**
   * Output only. The cluster's DNS endpoint configuration. A DNS format
   * address. This is accessible from the public internet. Ex: uid.us-
   * central1.gke.goog. Always present, but the behavior may change according to
   * the value of DNSEndpointConfig.allow_external_traffic.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DNSEndpointConfig::class, 'Google_Service_Container_DNSEndpointConfig');
