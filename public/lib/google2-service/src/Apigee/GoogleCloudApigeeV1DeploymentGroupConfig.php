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

class GoogleCloudApigeeV1DeploymentGroupConfig extends \Google\Model
{
  /**
   * Unspecified type
   */
  public const DEPLOYMENT_GROUP_TYPE_DEPLOYMENT_GROUP_TYPE_UNSPECIFIED = 'DEPLOYMENT_GROUP_TYPE_UNSPECIFIED';
  /**
   * Standard type
   */
  public const DEPLOYMENT_GROUP_TYPE_STANDARD = 'STANDARD';
  /**
   * Extensible Type
   */
  public const DEPLOYMENT_GROUP_TYPE_EXTENSIBLE = 'EXTENSIBLE';
  /**
   * Type of the deployment group, which will be either Standard or Extensible.
   *
   * @var string
   */
  public $deploymentGroupType;
  /**
   * Name of the deployment group in the following format:
   * `organizations/{org}/environments/{env}/deploymentGroups/{group}`.
   *
   * @var string
   */
  public $name;
  /**
   * Revision number which can be used by the runtime to detect if the
   * deployment group has changed between two versions.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Unique ID. The ID will only change if the deployment group is deleted and
   * recreated.
   *
   * @var string
   */
  public $uid;

  /**
   * Type of the deployment group, which will be either Standard or Extensible.
   *
   * Accepted values: DEPLOYMENT_GROUP_TYPE_UNSPECIFIED, STANDARD, EXTENSIBLE
   *
   * @param self::DEPLOYMENT_GROUP_TYPE_* $deploymentGroupType
   */
  public function setDeploymentGroupType($deploymentGroupType)
  {
    $this->deploymentGroupType = $deploymentGroupType;
  }
  /**
   * @return self::DEPLOYMENT_GROUP_TYPE_*
   */
  public function getDeploymentGroupType()
  {
    return $this->deploymentGroupType;
  }
  /**
   * Name of the deployment group in the following format:
   * `organizations/{org}/environments/{env}/deploymentGroups/{group}`.
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
   * Revision number which can be used by the runtime to detect if the
   * deployment group has changed between two versions.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Unique ID. The ID will only change if the deployment group is deleted and
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
class_alias(GoogleCloudApigeeV1DeploymentGroupConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DeploymentGroupConfig');
