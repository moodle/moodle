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

namespace Google\Service\BackupforGKE;

class ClusterResourceRestoreScope extends \Google\Collection
{
  protected $collection_key = 'selectedGroupKinds';
  /**
   * Optional. If True, all valid cluster-scoped resources will be restored.
   * Mutually exclusive to any other field in the message.
   *
   * @var bool
   */
  public $allGroupKinds;
  protected $excludedGroupKindsType = GroupKind::class;
  protected $excludedGroupKindsDataType = 'array';
  /**
   * Optional. If True, no cluster-scoped resources will be restored. This has
   * the same restore scope as if the message is not defined. Mutually exclusive
   * to any other field in the message.
   *
   * @var bool
   */
  public $noGroupKinds;
  protected $selectedGroupKindsType = GroupKind::class;
  protected $selectedGroupKindsDataType = 'array';

  /**
   * Optional. If True, all valid cluster-scoped resources will be restored.
   * Mutually exclusive to any other field in the message.
   *
   * @param bool $allGroupKinds
   */
  public function setAllGroupKinds($allGroupKinds)
  {
    $this->allGroupKinds = $allGroupKinds;
  }
  /**
   * @return bool
   */
  public function getAllGroupKinds()
  {
    return $this->allGroupKinds;
  }
  /**
   * Optional. A list of cluster-scoped resource group kinds to NOT restore from
   * the backup. If specified, all valid cluster-scoped resources will be
   * restored except for those specified in the list. Mutually exclusive to any
   * other field in the message.
   *
   * @param GroupKind[] $excludedGroupKinds
   */
  public function setExcludedGroupKinds($excludedGroupKinds)
  {
    $this->excludedGroupKinds = $excludedGroupKinds;
  }
  /**
   * @return GroupKind[]
   */
  public function getExcludedGroupKinds()
  {
    return $this->excludedGroupKinds;
  }
  /**
   * Optional. If True, no cluster-scoped resources will be restored. This has
   * the same restore scope as if the message is not defined. Mutually exclusive
   * to any other field in the message.
   *
   * @param bool $noGroupKinds
   */
  public function setNoGroupKinds($noGroupKinds)
  {
    $this->noGroupKinds = $noGroupKinds;
  }
  /**
   * @return bool
   */
  public function getNoGroupKinds()
  {
    return $this->noGroupKinds;
  }
  /**
   * Optional. A list of cluster-scoped resource group kinds to restore from the
   * backup. If specified, only the selected resources will be restored.
   * Mutually exclusive to any other field in the message.
   *
   * @param GroupKind[] $selectedGroupKinds
   */
  public function setSelectedGroupKinds($selectedGroupKinds)
  {
    $this->selectedGroupKinds = $selectedGroupKinds;
  }
  /**
   * @return GroupKind[]
   */
  public function getSelectedGroupKinds()
  {
    return $this->selectedGroupKinds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterResourceRestoreScope::class, 'Google_Service_BackupforGKE_ClusterResourceRestoreScope');
