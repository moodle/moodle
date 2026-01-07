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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1WorkloadPartnerPermissions extends \Google\Model
{
  /**
   * Optional. Allow partner to view support case details for an AXT log
   *
   * @var bool
   */
  public $accessTransparencyLogsSupportCaseViewer;
  /**
   * Optional. Allow partner to view violation alerts.
   *
   * @var bool
   */
  public $assuredWorkloadsMonitoring;
  /**
   * Optional. Allow the partner to view inspectability logs and monitoring
   * violations.
   *
   * @var bool
   */
  public $dataLogsViewer;
  /**
   * Optional. Allow partner to view access approval logs.
   *
   * @var bool
   */
  public $serviceAccessApprover;

  /**
   * Optional. Allow partner to view support case details for an AXT log
   *
   * @param bool $accessTransparencyLogsSupportCaseViewer
   */
  public function setAccessTransparencyLogsSupportCaseViewer($accessTransparencyLogsSupportCaseViewer)
  {
    $this->accessTransparencyLogsSupportCaseViewer = $accessTransparencyLogsSupportCaseViewer;
  }
  /**
   * @return bool
   */
  public function getAccessTransparencyLogsSupportCaseViewer()
  {
    return $this->accessTransparencyLogsSupportCaseViewer;
  }
  /**
   * Optional. Allow partner to view violation alerts.
   *
   * @param bool $assuredWorkloadsMonitoring
   */
  public function setAssuredWorkloadsMonitoring($assuredWorkloadsMonitoring)
  {
    $this->assuredWorkloadsMonitoring = $assuredWorkloadsMonitoring;
  }
  /**
   * @return bool
   */
  public function getAssuredWorkloadsMonitoring()
  {
    return $this->assuredWorkloadsMonitoring;
  }
  /**
   * Optional. Allow the partner to view inspectability logs and monitoring
   * violations.
   *
   * @param bool $dataLogsViewer
   */
  public function setDataLogsViewer($dataLogsViewer)
  {
    $this->dataLogsViewer = $dataLogsViewer;
  }
  /**
   * @return bool
   */
  public function getDataLogsViewer()
  {
    return $this->dataLogsViewer;
  }
  /**
   * Optional. Allow partner to view access approval logs.
   *
   * @param bool $serviceAccessApprover
   */
  public function setServiceAccessApprover($serviceAccessApprover)
  {
    $this->serviceAccessApprover = $serviceAccessApprover;
  }
  /**
   * @return bool
   */
  public function getServiceAccessApprover()
  {
    return $this->serviceAccessApprover;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1WorkloadPartnerPermissions::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1WorkloadPartnerPermissions');
