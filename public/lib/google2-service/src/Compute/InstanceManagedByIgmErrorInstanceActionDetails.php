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

namespace Google\Service\Compute;

class InstanceManagedByIgmErrorInstanceActionDetails extends \Google\Model
{
  /**
   * The managed instance group is abandoning this instance. The instance will
   * be removed from the instance group and from any target pools that are
   * associated with this group.
   */
  public const ACTION_ABANDONING = 'ABANDONING';
  /**
   * The managed instance group is creating this instance. If the group fails to
   * create this instance, it will try again until it is successful.
   */
  public const ACTION_CREATING = 'CREATING';
  /**
   * The managed instance group is attempting to create this instance only once.
   * If the group fails to create this instance, it does not try again and the
   * group's targetSize value is decreased.
   */
  public const ACTION_CREATING_WITHOUT_RETRIES = 'CREATING_WITHOUT_RETRIES';
  /**
   * The managed instance group is permanently deleting this instance.
   */
  public const ACTION_DELETING = 'DELETING';
  /**
   * The managed instance group has not scheduled any actions for this instance.
   */
  public const ACTION_NONE = 'NONE';
  /**
   * The managed instance group is recreating this instance.
   */
  public const ACTION_RECREATING = 'RECREATING';
  /**
   * The managed instance group is applying configuration changes to the
   * instance without stopping it. For example, the group can update the target
   * pool list for an instance without stopping that instance.
   */
  public const ACTION_REFRESHING = 'REFRESHING';
  /**
   * The managed instance group is restarting this instance.
   */
  public const ACTION_RESTARTING = 'RESTARTING';
  /**
   * The managed instance group is resuming this instance.
   */
  public const ACTION_RESUMING = 'RESUMING';
  /**
   * The managed instance group is starting this instance.
   */
  public const ACTION_STARTING = 'STARTING';
  /**
   * The managed instance group is stopping this instance.
   */
  public const ACTION_STOPPING = 'STOPPING';
  /**
   * The managed instance group is suspending this instance.
   */
  public const ACTION_SUSPENDING = 'SUSPENDING';
  /**
   * The managed instance group is verifying this already created instance.
   * Verification happens every time the instance is (re)created or restarted
   * and consists of:  1. Waiting until health check specified as part of this
   * managed instance     group's autohealing policy reports HEALTHY.     Note:
   * Applies only if autohealing policy has a health check specified  2. Waiting
   * for addition verification steps performed as post-instance     creation
   * (subject to future extensions).
   */
  public const ACTION_VERIFYING = 'VERIFYING';
  /**
   * Output only. [Output Only] Action that managed instance group was executing
   * on the instance when the error occurred. Possible values:
   *
   * @var string
   */
  public $action;
  /**
   * Output only. [Output Only] The URL of the instance. The URL can be set even
   * if the instance has not yet been created.
   *
   * @var string
   */
  public $instance;
  protected $versionType = ManagedInstanceVersion::class;
  protected $versionDataType = '';

  /**
   * Output only. [Output Only] Action that managed instance group was executing
   * on the instance when the error occurred. Possible values:
   *
   * Accepted values: ABANDONING, CREATING, CREATING_WITHOUT_RETRIES, DELETING,
   * NONE, RECREATING, REFRESHING, RESTARTING, RESUMING, STARTING, STOPPING,
   * SUSPENDING, VERIFYING
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
   * Output only. [Output Only] The URL of the instance. The URL can be set even
   * if the instance has not yet been created.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Output only. [Output Only] Version this instance was created from, or was
   * being created from, but the creation failed. Corresponds to one of the
   * versions that were set on the Instance Group Manager resource at the time
   * this instance was being created.
   *
   * @param ManagedInstanceVersion $version
   */
  public function setVersion(ManagedInstanceVersion $version)
  {
    $this->version = $version;
  }
  /**
   * @return ManagedInstanceVersion
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceManagedByIgmErrorInstanceActionDetails::class, 'Google_Service_Compute_InstanceManagedByIgmErrorInstanceActionDetails');
