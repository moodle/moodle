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

namespace Google\Service\NetworkSecurity;

class InterceptDeployment extends \Google\Model
{
  /**
   * State not set (this is not a valid state).
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The deployment is ready and in sync with the parent group.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The deployment is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The deployment is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The deployment is out of sync with the parent group. In most cases, this is
   * a result of a transient issue within the system (e.g. a delayed data-path
   * config) and the system is expected to recover automatically. See the parent
   * deployment group's state for more details.
   */
  public const STATE_OUT_OF_SYNC = 'OUT_OF_SYNC';
  /**
   * An attempt to delete the deployment has failed. This is a terminal state
   * and the deployment is not expected to recover. The only permitted operation
   * is to retry deleting the deployment.
   */
  public const STATE_DELETE_FAILED = 'DELETE_FAILED';
  /**
   * Output only. The timestamp when the resource was created. See
   * https://google.aip.dev/148#timestamps.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-provided description of the deployment. Used as additional
   * context for the deployment.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Immutable. The regional forwarding rule that fronts the
   * interceptors, for example: `projects/123456789/regions/us-
   * central1/forwardingRules/my-rule`. See https://google.aip.dev/124.
   *
   * @var string
   */
  public $forwardingRule;
  /**
   * Required. Immutable. The deployment group that this deployment is a part
   * of, for example:
   * `projects/123456789/locations/global/interceptDeploymentGroups/my-dg`. See
   * https://google.aip.dev/124.
   *
   * @var string
   */
  public $interceptDeploymentGroup;
  /**
   * Optional. Labels are key/value pairs that help to organize and filter
   * resources.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Identifier. The resource name of this deployment, for example:
   * `projects/123456789/locations/us-central1-a/interceptDeployments/my-dep`.
   * See https://google.aip.dev/122 for more details.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current state of the resource does not match the user's
   * intended state, and the system is working to reconcile them. This part of
   * the normal operation (e.g. linking a new association to the parent group).
   * See https://google.aip.dev/128.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The current state of the deployment. See
   * https://google.aip.dev/216.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The timestamp when the resource was most recently updated. See
   * https://google.aip.dev/148#timestamps.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the resource was created. See
   * https://google.aip.dev/148#timestamps.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. User-provided description of the deployment. Used as additional
   * context for the deployment.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Immutable. The regional forwarding rule that fronts the
   * interceptors, for example: `projects/123456789/regions/us-
   * central1/forwardingRules/my-rule`. See https://google.aip.dev/124.
   *
   * @param string $forwardingRule
   */
  public function setForwardingRule($forwardingRule)
  {
    $this->forwardingRule = $forwardingRule;
  }
  /**
   * @return string
   */
  public function getForwardingRule()
  {
    return $this->forwardingRule;
  }
  /**
   * Required. Immutable. The deployment group that this deployment is a part
   * of, for example:
   * `projects/123456789/locations/global/interceptDeploymentGroups/my-dg`. See
   * https://google.aip.dev/124.
   *
   * @param string $interceptDeploymentGroup
   */
  public function setInterceptDeploymentGroup($interceptDeploymentGroup)
  {
    $this->interceptDeploymentGroup = $interceptDeploymentGroup;
  }
  /**
   * @return string
   */
  public function getInterceptDeploymentGroup()
  {
    return $this->interceptDeploymentGroup;
  }
  /**
   * Optional. Labels are key/value pairs that help to organize and filter
   * resources.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Immutable. Identifier. The resource name of this deployment, for example:
   * `projects/123456789/locations/us-central1-a/interceptDeployments/my-dep`.
   * See https://google.aip.dev/122 for more details.
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
   * Output only. The current state of the resource does not match the user's
   * intended state, and the system is working to reconcile them. This part of
   * the normal operation (e.g. linking a new association to the parent group).
   * See https://google.aip.dev/128.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. The current state of the deployment. See
   * https://google.aip.dev/216.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING,
   * OUT_OF_SYNC, DELETE_FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The timestamp when the resource was most recently updated. See
   * https://google.aip.dev/148#timestamps.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterceptDeployment::class, 'Google_Service_NetworkSecurity_InterceptDeployment');
