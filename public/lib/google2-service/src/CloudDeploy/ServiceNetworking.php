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

class ServiceNetworking extends \Google\Model
{
  /**
   * Required. Name of the Kubernetes Deployment whose traffic is managed by the
   * specified Service.
   *
   * @var string
   */
  public $deployment;
  /**
   * Optional. Whether to disable Pod overprovisioning. If Pod overprovisioning
   * is disabled then Cloud Deploy will limit the number of total Pods used for
   * the deployment strategy to the number of Pods the Deployment has on the
   * cluster.
   *
   * @var bool
   */
  public $disablePodOverprovisioning;
  /**
   * Optional. The label to use when selecting Pods for the Deployment resource.
   * This label must already be present in the Deployment.
   *
   * @var string
   */
  public $podSelectorLabel;
  /**
   * Required. Name of the Kubernetes Service.
   *
   * @var string
   */
  public $service;

  /**
   * Required. Name of the Kubernetes Deployment whose traffic is managed by the
   * specified Service.
   *
   * @param string $deployment
   */
  public function setDeployment($deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return string
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * Optional. Whether to disable Pod overprovisioning. If Pod overprovisioning
   * is disabled then Cloud Deploy will limit the number of total Pods used for
   * the deployment strategy to the number of Pods the Deployment has on the
   * cluster.
   *
   * @param bool $disablePodOverprovisioning
   */
  public function setDisablePodOverprovisioning($disablePodOverprovisioning)
  {
    $this->disablePodOverprovisioning = $disablePodOverprovisioning;
  }
  /**
   * @return bool
   */
  public function getDisablePodOverprovisioning()
  {
    return $this->disablePodOverprovisioning;
  }
  /**
   * Optional. The label to use when selecting Pods for the Deployment resource.
   * This label must already be present in the Deployment.
   *
   * @param string $podSelectorLabel
   */
  public function setPodSelectorLabel($podSelectorLabel)
  {
    $this->podSelectorLabel = $podSelectorLabel;
  }
  /**
   * @return string
   */
  public function getPodSelectorLabel()
  {
    return $this->podSelectorLabel;
  }
  /**
   * Required. Name of the Kubernetes Service.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceNetworking::class, 'Google_Service_CloudDeploy_ServiceNetworking');
