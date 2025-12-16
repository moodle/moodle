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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1PodStatus extends \Google\Model
{
  /**
   * Version of the application running in the pod.
   *
   * @var string
   */
  public $appVersion;
  /**
   * Status of the deployment. Valid values include: - `deployed`: Successful. -
   * `error` : Failed. - `pending` : Pod has not yet reported on the deployment.
   *
   * @var string
   */
  public $deploymentStatus;
  /**
   * Time the deployment status was reported in milliseconds since epoch.
   *
   * @var string
   */
  public $deploymentStatusTime;
  /**
   * Time the proxy was deployed in milliseconds since epoch.
   *
   * @var string
   */
  public $deploymentTime;
  /**
   * Name of the pod which is reporting the status.
   *
   * @var string
   */
  public $podName;
  /**
   * Overall status of the pod (not this specific deployment). Valid values
   * include: - `active`: Up to date. - `stale` : Recently out of date. Pods
   * that have not reported status in a long time are excluded from the output.
   *
   * @var string
   */
  public $podStatus;
  /**
   * Time the pod status was reported in milliseconds since epoch.
   *
   * @var string
   */
  public $podStatusTime;
  /**
   * Code associated with the deployment status.
   *
   * @var string
   */
  public $statusCode;
  /**
   * Human-readable message associated with the status code.
   *
   * @var string
   */
  public $statusCodeDetails;

  /**
   * Version of the application running in the pod.
   *
   * @param string $appVersion
   */
  public function setAppVersion($appVersion)
  {
    $this->appVersion = $appVersion;
  }
  /**
   * @return string
   */
  public function getAppVersion()
  {
    return $this->appVersion;
  }
  /**
   * Status of the deployment. Valid values include: - `deployed`: Successful. -
   * `error` : Failed. - `pending` : Pod has not yet reported on the deployment.
   *
   * @param string $deploymentStatus
   */
  public function setDeploymentStatus($deploymentStatus)
  {
    $this->deploymentStatus = $deploymentStatus;
  }
  /**
   * @return string
   */
  public function getDeploymentStatus()
  {
    return $this->deploymentStatus;
  }
  /**
   * Time the deployment status was reported in milliseconds since epoch.
   *
   * @param string $deploymentStatusTime
   */
  public function setDeploymentStatusTime($deploymentStatusTime)
  {
    $this->deploymentStatusTime = $deploymentStatusTime;
  }
  /**
   * @return string
   */
  public function getDeploymentStatusTime()
  {
    return $this->deploymentStatusTime;
  }
  /**
   * Time the proxy was deployed in milliseconds since epoch.
   *
   * @param string $deploymentTime
   */
  public function setDeploymentTime($deploymentTime)
  {
    $this->deploymentTime = $deploymentTime;
  }
  /**
   * @return string
   */
  public function getDeploymentTime()
  {
    return $this->deploymentTime;
  }
  /**
   * Name of the pod which is reporting the status.
   *
   * @param string $podName
   */
  public function setPodName($podName)
  {
    $this->podName = $podName;
  }
  /**
   * @return string
   */
  public function getPodName()
  {
    return $this->podName;
  }
  /**
   * Overall status of the pod (not this specific deployment). Valid values
   * include: - `active`: Up to date. - `stale` : Recently out of date. Pods
   * that have not reported status in a long time are excluded from the output.
   *
   * @param string $podStatus
   */
  public function setPodStatus($podStatus)
  {
    $this->podStatus = $podStatus;
  }
  /**
   * @return string
   */
  public function getPodStatus()
  {
    return $this->podStatus;
  }
  /**
   * Time the pod status was reported in milliseconds since epoch.
   *
   * @param string $podStatusTime
   */
  public function setPodStatusTime($podStatusTime)
  {
    $this->podStatusTime = $podStatusTime;
  }
  /**
   * @return string
   */
  public function getPodStatusTime()
  {
    return $this->podStatusTime;
  }
  /**
   * Code associated with the deployment status.
   *
   * @param string $statusCode
   */
  public function setStatusCode($statusCode)
  {
    $this->statusCode = $statusCode;
  }
  /**
   * @return string
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }
  /**
   * Human-readable message associated with the status code.
   *
   * @param string $statusCodeDetails
   */
  public function setStatusCodeDetails($statusCodeDetails)
  {
    $this->statusCodeDetails = $statusCodeDetails;
  }
  /**
   * @return string
   */
  public function getStatusCodeDetails()
  {
    return $this->statusCodeDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1PodStatus::class, 'Google_Service_Apigee_GoogleCloudApigeeV1PodStatus');
