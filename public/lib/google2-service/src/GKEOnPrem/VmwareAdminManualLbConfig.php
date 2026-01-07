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

namespace Google\Service\GKEOnPrem;

class VmwareAdminManualLbConfig extends \Google\Model
{
  /**
   * NodePort for add-ons server in the admin cluster.
   *
   * @var int
   */
  public $addonsNodePort;
  /**
   * NodePort for control plane service. The Kubernetes API server in the admin
   * cluster is implemented as a Service of type NodePort (ex. 30968).
   *
   * @var int
   */
  public $controlPlaneNodePort;
  /**
   * NodePort for ingress service's http. The ingress service in the admin
   * cluster is implemented as a Service of type NodePort (ex. 32527).
   *
   * @var int
   */
  public $ingressHttpNodePort;
  /**
   * NodePort for ingress service's https. The ingress service in the admin
   * cluster is implemented as a Service of type NodePort (ex. 30139).
   *
   * @var int
   */
  public $ingressHttpsNodePort;
  /**
   * NodePort for konnectivity server service running as a sidecar in each kube-
   * apiserver pod (ex. 30564).
   *
   * @var int
   */
  public $konnectivityServerNodePort;

  /**
   * NodePort for add-ons server in the admin cluster.
   *
   * @param int $addonsNodePort
   */
  public function setAddonsNodePort($addonsNodePort)
  {
    $this->addonsNodePort = $addonsNodePort;
  }
  /**
   * @return int
   */
  public function getAddonsNodePort()
  {
    return $this->addonsNodePort;
  }
  /**
   * NodePort for control plane service. The Kubernetes API server in the admin
   * cluster is implemented as a Service of type NodePort (ex. 30968).
   *
   * @param int $controlPlaneNodePort
   */
  public function setControlPlaneNodePort($controlPlaneNodePort)
  {
    $this->controlPlaneNodePort = $controlPlaneNodePort;
  }
  /**
   * @return int
   */
  public function getControlPlaneNodePort()
  {
    return $this->controlPlaneNodePort;
  }
  /**
   * NodePort for ingress service's http. The ingress service in the admin
   * cluster is implemented as a Service of type NodePort (ex. 32527).
   *
   * @param int $ingressHttpNodePort
   */
  public function setIngressHttpNodePort($ingressHttpNodePort)
  {
    $this->ingressHttpNodePort = $ingressHttpNodePort;
  }
  /**
   * @return int
   */
  public function getIngressHttpNodePort()
  {
    return $this->ingressHttpNodePort;
  }
  /**
   * NodePort for ingress service's https. The ingress service in the admin
   * cluster is implemented as a Service of type NodePort (ex. 30139).
   *
   * @param int $ingressHttpsNodePort
   */
  public function setIngressHttpsNodePort($ingressHttpsNodePort)
  {
    $this->ingressHttpsNodePort = $ingressHttpsNodePort;
  }
  /**
   * @return int
   */
  public function getIngressHttpsNodePort()
  {
    return $this->ingressHttpsNodePort;
  }
  /**
   * NodePort for konnectivity server service running as a sidecar in each kube-
   * apiserver pod (ex. 30564).
   *
   * @param int $konnectivityServerNodePort
   */
  public function setKonnectivityServerNodePort($konnectivityServerNodePort)
  {
    $this->konnectivityServerNodePort = $konnectivityServerNodePort;
  }
  /**
   * @return int
   */
  public function getKonnectivityServerNodePort()
  {
    return $this->konnectivityServerNodePort;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAdminManualLbConfig::class, 'Google_Service_GKEOnPrem_VmwareAdminManualLbConfig');
