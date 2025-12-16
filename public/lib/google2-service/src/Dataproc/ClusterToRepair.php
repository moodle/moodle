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

class ClusterToRepair extends \Google\Model
{
  /**
   * No action will be taken by default.
   */
  public const CLUSTER_REPAIR_ACTION_CLUSTER_REPAIR_ACTION_UNSPECIFIED = 'CLUSTER_REPAIR_ACTION_UNSPECIFIED';
  /**
   * Repair cluster in ERROR_DUE_TO_UPDATE states.
   */
  public const CLUSTER_REPAIR_ACTION_REPAIR_ERROR_DUE_TO_UPDATE_CLUSTER = 'REPAIR_ERROR_DUE_TO_UPDATE_CLUSTER';
  /**
   * Required. Repair action to take on the cluster resource.
   *
   * @var string
   */
  public $clusterRepairAction;

  /**
   * Required. Repair action to take on the cluster resource.
   *
   * Accepted values: CLUSTER_REPAIR_ACTION_UNSPECIFIED,
   * REPAIR_ERROR_DUE_TO_UPDATE_CLUSTER
   *
   * @param self::CLUSTER_REPAIR_ACTION_* $clusterRepairAction
   */
  public function setClusterRepairAction($clusterRepairAction)
  {
    $this->clusterRepairAction = $clusterRepairAction;
  }
  /**
   * @return self::CLUSTER_REPAIR_ACTION_*
   */
  public function getClusterRepairAction()
  {
    return $this->clusterRepairAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterToRepair::class, 'Google_Service_Dataproc_ClusterToRepair');
