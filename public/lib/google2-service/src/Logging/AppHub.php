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

namespace Google\Service\Logging;

class AppHub extends \Google\Model
{
  protected $applicationType = AppHubApplication::class;
  protected $applicationDataType = '';
  protected $serviceType = AppHubService::class;
  protected $serviceDataType = '';
  protected $workloadType = AppHubWorkload::class;
  protected $workloadDataType = '';

  /**
   * Metadata associated with the application.
   *
   * @param AppHubApplication $application
   */
  public function setApplication(AppHubApplication $application)
  {
    $this->application = $application;
  }
  /**
   * @return AppHubApplication
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * Metadata associated with the service.
   *
   * @param AppHubService $service
   */
  public function setService(AppHubService $service)
  {
    $this->service = $service;
  }
  /**
   * @return AppHubService
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Metadata associated with the workload.
   *
   * @param AppHubWorkload $workload
   */
  public function setWorkload(AppHubWorkload $workload)
  {
    $this->workload = $workload;
  }
  /**
   * @return AppHubWorkload
   */
  public function getWorkload()
  {
    return $this->workload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppHub::class, 'Google_Service_Logging_AppHub');
