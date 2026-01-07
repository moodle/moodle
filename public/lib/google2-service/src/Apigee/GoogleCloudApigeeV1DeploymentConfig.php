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

class GoogleCloudApigeeV1DeploymentConfig extends \Google\Collection
{
  protected $collection_key = 'deploymentGroups';
  /**
   * Additional key-value metadata for the deployment.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * Base path where the application will be hosted. Defaults to "/".
   *
   * @var string
   */
  public $basePath;
  /**
   * The list of deployment groups in which this proxy should be deployed. Not
   * currently populated for shared flows.
   *
   * @var string[]
   */
  public $deploymentGroups;
  /**
   * A mapping from basepaths to proxy endpoint names in this proxy. Not
   * populated for shared flows.
   *
   * @var string[]
   */
  public $endpoints;
  /**
   * Location of the API proxy bundle as a URI.
   *
   * @var string
   */
  public $location;
  /**
   * Name of the API or shared flow revision to be deployed in the following
   * format: `organizations/{org}/apis/{api}/revisions/{rev}` or
   * `organizations/{org}/sharedflows/{sharedflow}/revisions/{rev}`
   *
   * @var string
   */
  public $name;
  /**
   * Unique ID of the API proxy revision.
   *
   * @var string
   */
  public $proxyUid;
  /**
   * The service account identity associated with this deployment. If non-empty,
   * will be in the following format:
   * `projects/-/serviceAccounts/{account_email}`
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Unique ID. The ID will only change if the deployment is deleted and
   * recreated.
   *
   * @var string
   */
  public $uid;

  /**
   * Additional key-value metadata for the deployment.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Base path where the application will be hosted. Defaults to "/".
   *
   * @param string $basePath
   */
  public function setBasePath($basePath)
  {
    $this->basePath = $basePath;
  }
  /**
   * @return string
   */
  public function getBasePath()
  {
    return $this->basePath;
  }
  /**
   * The list of deployment groups in which this proxy should be deployed. Not
   * currently populated for shared flows.
   *
   * @param string[] $deploymentGroups
   */
  public function setDeploymentGroups($deploymentGroups)
  {
    $this->deploymentGroups = $deploymentGroups;
  }
  /**
   * @return string[]
   */
  public function getDeploymentGroups()
  {
    return $this->deploymentGroups;
  }
  /**
   * A mapping from basepaths to proxy endpoint names in this proxy. Not
   * populated for shared flows.
   *
   * @param string[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return string[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Location of the API proxy bundle as a URI.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Name of the API or shared flow revision to be deployed in the following
   * format: `organizations/{org}/apis/{api}/revisions/{rev}` or
   * `organizations/{org}/sharedflows/{sharedflow}/revisions/{rev}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Unique ID of the API proxy revision.
   *
   * @param string $proxyUid
   */
  public function setProxyUid($proxyUid)
  {
    $this->proxyUid = $proxyUid;
  }
  /**
   * @return string
   */
  public function getProxyUid()
  {
    return $this->proxyUid;
  }
  /**
   * The service account identity associated with this deployment. If non-empty,
   * will be in the following format:
   * `projects/-/serviceAccounts/{account_email}`
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Unique ID. The ID will only change if the deployment is deleted and
   * recreated.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DeploymentConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DeploymentConfig');
