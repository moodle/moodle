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

namespace Google\Service\OSConfig;

class OSPolicyAssignment extends \Google\Collection
{
  /**
   * Invalid value
   */
  public const ROLLOUT_STATE_ROLLOUT_STATE_UNSPECIFIED = 'ROLLOUT_STATE_UNSPECIFIED';
  /**
   * The rollout is in progress.
   */
  public const ROLLOUT_STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The rollout is being cancelled.
   */
  public const ROLLOUT_STATE_CANCELLING = 'CANCELLING';
  /**
   * The rollout is cancelled.
   */
  public const ROLLOUT_STATE_CANCELLED = 'CANCELLED';
  /**
   * The rollout has completed successfully.
   */
  public const ROLLOUT_STATE_SUCCEEDED = 'SUCCEEDED';
  protected $collection_key = 'osPolicies';
  /**
   * Output only. Indicates that this revision has been successfully rolled out
   * in this zone and new VMs will be assigned OS policies from this revision.
   * For a given OS policy assignment, there is only one revision with a value
   * of `true` for this field.
   *
   * @var bool
   */
  public $baseline;
  /**
   * Output only. Indicates that this revision deletes the OS policy assignment.
   *
   * @var bool
   */
  public $deleted;
  /**
   * OS policy assignment description. Length of the description is limited to
   * 1024 characters.
   *
   * @var string
   */
  public $description;
  /**
   * The etag for this OS policy assignment. If this is provided on update, it
   * must match the server's etag.
   *
   * @var string
   */
  public $etag;
  protected $instanceFilterType = OSPolicyAssignmentInstanceFilter::class;
  protected $instanceFilterDataType = '';
  /**
   * Resource name. Format: `projects/{project_number}/locations/{location}/osPo
   * licyAssignments/{os_policy_assignment_id}` This field is ignored when you
   * create an OS policy assignment.
   *
   * @var string
   */
  public $name;
  protected $osPoliciesType = OSPolicy::class;
  protected $osPoliciesDataType = 'array';
  /**
   * Output only. Indicates that reconciliation is in progress for the revision.
   * This value is `true` when the `rollout_state` is one of: * IN_PROGRESS *
   * CANCELLING
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @var string
   */
  public $revisionCreateTime;
  /**
   * Output only. The assignment revision ID A new revision is committed
   * whenever a rollout is triggered for a OS policy assignment
   *
   * @var string
   */
  public $revisionId;
  protected $rolloutType = OSPolicyAssignmentRollout::class;
  protected $rolloutDataType = '';
  /**
   * Output only. OS policy assignment rollout state
   *
   * @var string
   */
  public $rolloutState;
  /**
   * Output only. Server generated unique id for the OS policy assignment
   * resource.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. Indicates that this revision has been successfully rolled out
   * in this zone and new VMs will be assigned OS policies from this revision.
   * For a given OS policy assignment, there is only one revision with a value
   * of `true` for this field.
   *
   * @param bool $baseline
   */
  public function setBaseline($baseline)
  {
    $this->baseline = $baseline;
  }
  /**
   * @return bool
   */
  public function getBaseline()
  {
    return $this->baseline;
  }
  /**
   * Output only. Indicates that this revision deletes the OS policy assignment.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * OS policy assignment description. Length of the description is limited to
   * 1024 characters.
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
   * The etag for this OS policy assignment. If this is provided on update, it
   * must match the server's etag.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Required. Filter to select VMs.
   *
   * @param OSPolicyAssignmentInstanceFilter $instanceFilter
   */
  public function setInstanceFilter(OSPolicyAssignmentInstanceFilter $instanceFilter)
  {
    $this->instanceFilter = $instanceFilter;
  }
  /**
   * @return OSPolicyAssignmentInstanceFilter
   */
  public function getInstanceFilter()
  {
    return $this->instanceFilter;
  }
  /**
   * Resource name. Format: `projects/{project_number}/locations/{location}/osPo
   * licyAssignments/{os_policy_assignment_id}` This field is ignored when you
   * create an OS policy assignment.
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
   * Required. List of OS policies to be applied to the VMs.
   *
   * @param OSPolicy[] $osPolicies
   */
  public function setOsPolicies($osPolicies)
  {
    $this->osPolicies = $osPolicies;
  }
  /**
   * @return OSPolicy[]
   */
  public function getOsPolicies()
  {
    return $this->osPolicies;
  }
  /**
   * Output only. Indicates that reconciliation is in progress for the revision.
   * This value is `true` when the `rollout_state` is one of: * IN_PROGRESS *
   * CANCELLING
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
   * Output only. The timestamp that the revision was created.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. The assignment revision ID A new revision is committed
   * whenever a rollout is triggered for a OS policy assignment
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
   * Required. Rollout to deploy the OS policy assignment. A rollout is
   * triggered in the following situations: 1) OSPolicyAssignment is created. 2)
   * OSPolicyAssignment is updated and the update contains changes to one of the
   * following fields: - instance_filter - os_policies 3) OSPolicyAssignment is
   * deleted.
   *
   * @param OSPolicyAssignmentRollout $rollout
   */
  public function setRollout(OSPolicyAssignmentRollout $rollout)
  {
    $this->rollout = $rollout;
  }
  /**
   * @return OSPolicyAssignmentRollout
   */
  public function getRollout()
  {
    return $this->rollout;
  }
  /**
   * Output only. OS policy assignment rollout state
   *
   * Accepted values: ROLLOUT_STATE_UNSPECIFIED, IN_PROGRESS, CANCELLING,
   * CANCELLED, SUCCEEDED
   *
   * @param self::ROLLOUT_STATE_* $rolloutState
   */
  public function setRolloutState($rolloutState)
  {
    $this->rolloutState = $rolloutState;
  }
  /**
   * @return self::ROLLOUT_STATE_*
   */
  public function getRolloutState()
  {
    return $this->rolloutState;
  }
  /**
   * Output only. Server generated unique id for the OS policy assignment
   * resource.
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
class_alias(OSPolicyAssignment::class, 'Google_Service_OSConfig_OSPolicyAssignment');
