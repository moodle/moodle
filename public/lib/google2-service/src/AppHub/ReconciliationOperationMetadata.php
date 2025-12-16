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

namespace Google\Service\AppHub;

class ReconciliationOperationMetadata extends \Google\Model
{
  /**
   * Unknown repair action.
   */
  public const EXCLUSIVE_ACTION_UNKNOWN_REPAIR_ACTION = 'UNKNOWN_REPAIR_ACTION';
  /**
   * The resource has to be deleted. When using this bit, the CLH should fail
   * the operation. DEPRECATED. Instead use DELETE_RESOURCE OperationSignal in
   * SideChannel.
   *
   * @deprecated
   */
  public const EXCLUSIVE_ACTION_DELETE = 'DELETE';
  /**
   * This resource could not be repaired but the repair should be tried again at
   * a later time. This can happen if there is a dependency that needs to be
   * resolved first- e.g. if a parent resource must be repaired before a child
   * resource.
   */
  public const EXCLUSIVE_ACTION_RETRY = 'RETRY';
  /**
   * DEPRECATED. Use exclusive_action instead.
   *
   * @deprecated
   * @var bool
   */
  public $deleteResource;
  /**
   * Excluisive action returned by the CLH.
   *
   * @var string
   */
  public $exclusiveAction;

  /**
   * DEPRECATED. Use exclusive_action instead.
   *
   * @deprecated
   * @param bool $deleteResource
   */
  public function setDeleteResource($deleteResource)
  {
    $this->deleteResource = $deleteResource;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getDeleteResource()
  {
    return $this->deleteResource;
  }
  /**
   * Excluisive action returned by the CLH.
   *
   * Accepted values: UNKNOWN_REPAIR_ACTION, DELETE, RETRY
   *
   * @param self::EXCLUSIVE_ACTION_* $exclusiveAction
   */
  public function setExclusiveAction($exclusiveAction)
  {
    $this->exclusiveAction = $exclusiveAction;
  }
  /**
   * @return self::EXCLUSIVE_ACTION_*
   */
  public function getExclusiveAction()
  {
    return $this->exclusiveAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReconciliationOperationMetadata::class, 'Google_Service_AppHub_ReconciliationOperationMetadata');
