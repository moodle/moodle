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

namespace Google\Service\CloudDeploy;

class GkeCluster extends \Google\Model
{
  /**
   * Optional. Information specifying a GKE Cluster. Format is
   * `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}`.
   *
   * @var string
   */
  public $cluster;
  /**
   * Optional. If set, the cluster will be accessed using the DNS endpoint. Note
   * that both `dns_endpoint` and `internal_ip` cannot be set to true.
   *
   * @var bool
   */
  public $dnsEndpoint;
  /**
   * Optional. If true, `cluster` is accessed using the private IP address of
   * the control plane endpoint. Otherwise, the default IP address of the
   * control plane endpoint is used. The default IP address is the private IP
   * address for clusters with private control-plane endpoints and the public IP
   * address otherwise. Only specify this option when `cluster` is a [private
   * GKE cluster](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/private-cluster-concept). Note that `internal_ip` and
   * `dns_endpoint` cannot both be set to true.
   *
   * @var bool
   */
  public $internalIp;
  /**
   * Optional. If set, used to configure a
   * [proxy](https://kubernetes.io/docs/concepts/configuration/organize-cluster-
   * access-kubeconfig/#proxy) to the Kubernetes server.
   *
   * @var string
   */
  public $proxyUrl;

  /**
   * Optional. Information specifying a GKE Cluster. Format is
   * `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}`.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Optional. If set, the cluster will be accessed using the DNS endpoint. Note
   * that both `dns_endpoint` and `internal_ip` cannot be set to true.
   *
   * @param bool $dnsEndpoint
   */
  public function setDnsEndpoint($dnsEndpoint)
  {
    $this->dnsEndpoint = $dnsEndpoint;
  }
  /**
   * @return bool
   */
  public function getDnsEndpoint()
  {
    return $this->dnsEndpoint;
  }
  /**
   * Optional. If true, `cluster` is accessed using the private IP address of
   * the control plane endpoint. Otherwise, the default IP address of the
   * control plane endpoint is used. The default IP address is the private IP
   * address for clusters with private control-plane endpoints and the public IP
   * address otherwise. Only specify this option when `cluster` is a [private
   * GKE cluster](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/private-cluster-concept). Note that `internal_ip` and
   * `dns_endpoint` cannot both be set to true.
   *
   * @param bool $internalIp
   */
  public function setInternalIp($internalIp)
  {
    $this->internalIp = $internalIp;
  }
  /**
   * @return bool
   */
  public function getInternalIp()
  {
    return $this->internalIp;
  }
  /**
   * Optional. If set, used to configure a
   * [proxy](https://kubernetes.io/docs/concepts/configuration/organize-cluster-
   * access-kubeconfig/#proxy) to the Kubernetes server.
   *
   * @param string $proxyUrl
   */
  public function setProxyUrl($proxyUrl)
  {
    $this->proxyUrl = $proxyUrl;
  }
  /**
   * @return string
   */
  public function getProxyUrl()
  {
    return $this->proxyUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkeCluster::class, 'Google_Service_CloudDeploy_GkeCluster');
