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

namespace Google\Service\Clouderrorreporting;

class ServiceContext extends \Google\Model
{
  /**
   * Type of the MonitoredResource. List of possible values:
   * https://cloud.google.com/monitoring/api/resources Value is set
   * automatically for incoming errors and must not be set when reporting
   * errors.
   *
   * @var string
   */
  public $resourceType;
  /**
   * An identifier of the service, such as the name of the executable, job, or
   * Google App Engine service name. This field is expected to have a low number
   * of values that are relatively stable over time, as opposed to `version`,
   * which can be changed whenever new code is deployed. Contains the service
   * name for error reports extracted from Google App Engine logs or `default`
   * if the App Engine default service is used.
   *
   * @var string
   */
  public $service;
  /**
   * Represents the source code version that the developer provided, which could
   * represent a version label or a Git SHA-1 hash, for example. For App Engine
   * standard environment, the version is set to the version of the app.
   *
   * @var string
   */
  public $version;

  /**
   * Type of the MonitoredResource. List of possible values:
   * https://cloud.google.com/monitoring/api/resources Value is set
   * automatically for incoming errors and must not be set when reporting
   * errors.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * An identifier of the service, such as the name of the executable, job, or
   * Google App Engine service name. This field is expected to have a low number
   * of values that are relatively stable over time, as opposed to `version`,
   * which can be changed whenever new code is deployed. Contains the service
   * name for error reports extracted from Google App Engine logs or `default`
   * if the App Engine default service is used.
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
  /**
   * Represents the source code version that the developer provided, which could
   * represent a version label or a Git SHA-1 hash, for example. For App Engine
   * standard environment, the version is set to the version of the app.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceContext::class, 'Google_Service_Clouderrorreporting_ServiceContext');
