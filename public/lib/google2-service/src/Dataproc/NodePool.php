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

class NodePool extends \Google\Collection
{
  /**
   * No action will be taken by default.
   */
  public const REPAIR_ACTION_REPAIR_ACTION_UNSPECIFIED = 'REPAIR_ACTION_UNSPECIFIED';
  /**
   * delete the specified list of nodes.
   */
  public const REPAIR_ACTION_DELETE = 'DELETE';
  protected $collection_key = 'instanceNames';
  /**
   * Required. A unique id of the node pool. Primary and Secondary workers can
   * be specified using special reserved ids PRIMARY_WORKER_POOL and
   * SECONDARY_WORKER_POOL respectively. Aux node pools can be referenced using
   * corresponding pool id.
   *
   * @var string
   */
  public $id;
  /**
   * Name of instances to be repaired. These instances must belong to specified
   * node pool.
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
   * Required. A unique id of the node pool. Primary and Secondary workers can
   * be specified using special reserved ids PRIMARY_WORKER_POOL and
   * SECONDARY_WORKER_POOL respectively. Aux node pools can be referenced using
   * corresponding pool id.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Name of instances to be repaired. These instances must belong to specified
   * node pool.
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
   * Accepted values: REPAIR_ACTION_UNSPECIFIED, DELETE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodePool::class, 'Google_Service_Dataproc_NodePool');
