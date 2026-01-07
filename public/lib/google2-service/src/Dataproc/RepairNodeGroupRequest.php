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

namespace Google\Service\Dataproc;

class RepairNodeGroupRequest extends \Google\Collection
{
  /**
   * No action will be taken by default.
   */
  public const REPAIR_ACTION_REPAIR_ACTION_UNSPECIFIED = 'REPAIR_ACTION_UNSPECIFIED';
  /**
   * replace the specified list of nodes.
   */
  public const REPAIR_ACTION_REPLACE = 'REPLACE';
  protected $collection_key = 'instanceNames';
  /**
   * Required. Name of instances to be repaired. These instances must belong to
   * specified node pool.
   *
   * @var string[]
   */
  public $instanceNames;
  /**
   * Required. Repair action to take on specified resources of the node pool.
   *
   * @var string
   */
  public $repairAction;
  /**
   * Optional. A unique ID used to identify the request. If the server receives
   * two RepairNodeGroupRequest with the same ID, the second request is ignored
   * and the first google.longrunning.Operation created and stored in the
   * backend is returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @var string
   */
  public $requestId;

  /**
   * Required. Name of instances to be repaired. These instances must belong to
   * specified node pool.
   *
   * @param string[] $instanceNames
   */
  public function setInstanceNames($instanceNames)
  {
    $this->instanceNames = $instanceNames;
  }
  /**
   * @return string[]
   */
  public function getInstanceNames()
  {
    return $this->instanceNames;
  }
  /**
   * Required. Repair action to take on specified resources of the node pool.
   *
   * Accepted values: REPAIR_ACTION_UNSPECIFIED, REPLACE
   *
   * @param self::REPAIR_ACTION_* $repairAction
   */
  public function setRepairAction($repairAction)
  {
    $this->repairAction = $repairAction;
  }
  /**
   * @return self::REPAIR_ACTION_*
   */
  public function getRepairAction()
  {
    return $this->repairAction;
  }
  /**
   * Optional. A unique ID used to identify the request. If the server receives
   * two RepairNodeGroupRequest with the same ID, the second request is ignored
   * and the first google.longrunning.Operation created and stored in the
   * backend is returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RepairNodeGroupRequest::class, 'Google_Service_Dataproc_RepairNodeGroupRequest');
