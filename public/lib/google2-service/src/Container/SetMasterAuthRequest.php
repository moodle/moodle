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

namespace Google\Service\Container;

class SetMasterAuthRequest extends \Google\Model
{
  /**
   * Operation is unknown and will error out.
   */
  public const ACTION_UNKNOWN = 'UNKNOWN';
  /**
   * Set the password to a user generated value.
   */
  public const ACTION_SET_PASSWORD = 'SET_PASSWORD';
  /**
   * Generate a new password and set it to that.
   */
  public const ACTION_GENERATE_PASSWORD = 'GENERATE_PASSWORD';
  /**
   * Set the username. If an empty username is provided, basic authentication is
   * disabled for the cluster. If a non-empty username is provided, basic
   * authentication is enabled, with either a provided password or a generated
   * one.
   */
  public const ACTION_SET_USERNAME = 'SET_USERNAME';
  /**
   * Required. The exact form of action to be taken on the master auth.
   *
   * @var string
   */
  public $action;
  /**
   * Deprecated. The name of the cluster to upgrade. This field has been
   * deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $clusterId;
  /**
   * The name (project, location, cluster) of the cluster to set auth. Specified
   * in the format `projects/locations/clusters`.
   *
   * @var string
   */
  public $name;
  /**
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $projectId;
  protected $updateType = MasterAuth::class;
  protected $updateDataType = '';
  /**
   * Deprecated. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field has been deprecated and replaced by the name
   * field.
   *
   * @deprecated
   * @var string
   */
  public $zone;

  /**
   * Required. The exact form of action to be taken on the master auth.
   *
   * Accepted values: UNKNOWN, SET_PASSWORD, GENERATE_PASSWORD, SET_USERNAME
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Deprecated. The name of the cluster to upgrade. This field has been
   * deprecated and replaced by the name field.
   *
   * @deprecated
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * The name (project, location, cluster) of the cluster to set auth. Specified
   * in the format `projects/locations/clusters`.
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
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the name field.
   *
   * @deprecated
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Required. A description of the update.
   *
   * @param MasterAuth $update
   */
  public function setUpdate(MasterAuth $update)
  {
    $this->update = $update;
  }
  /**
   * @return MasterAuth
   */
  public function getUpdate()
  {
    return $this->update;
  }
  /**
   * Deprecated. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field has been deprecated and replaced by the name
   * field.
   *
   * @deprecated
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetMasterAuthRequest::class, 'Google_Service_Container_SetMasterAuthRequest');
