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

class GoogleCloudApigeeV1DeploymentChangeReportRoutingDeployment extends \Google\Model
{
  /**
   * Name of the deployed API proxy revision containing the base path.
   *
   * @var string
   */
  public $apiProxy;
  /**
   * Base path receiving traffic.
   *
   * @var string
   */
  public $basepath;
  /**
   * Name of the environment in which the proxy is deployed.
   *
   * @var string
   */
  public $environment;
  /**
   * Name of the deployed API proxy revision containing the base path.
   *
   * @var string
   */
  public $revision;

  /**
   * Name of the deployed API proxy revision containing the base path.
   *
   * @param string $apiProxy
   */
  public function setApiProxy($apiProxy)
  {
    $this->apiProxy = $apiProxy;
  }
  /**
   * @return string
   */
  public function getApiProxy()
  {
    return $this->apiProxy;
  }
  /**
   * Base path receiving traffic.
   *
   * @param string $basepath
   */
  public function setBasepath($basepath)
  {
    $this->basepath = $basepath;
  }
  /**
   * @return string
   */
  public function getBasepath()
  {
    return $this->basepath;
  }
  /**
   * Name of the environment in which the proxy is deployed.
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Name of the deployed API proxy revision containing the base path.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DeploymentChangeReportRoutingDeployment::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DeploymentChangeReportRoutingDeployment');
