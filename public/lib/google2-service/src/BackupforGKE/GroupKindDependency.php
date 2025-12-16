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

class GroupKindDependency extends \Google\Model
{
  protected $requiringType = GroupKind::class;
  protected $requiringDataType = '';
  protected $satisfyingType = GroupKind::class;
  protected $satisfyingDataType = '';

  /**
   * Required. The requiring group kind requires that the other group kind be
   * restored first.
   *
   * @param GroupKind $requiring
   */
  public function setRequiring(GroupKind $requiring)
  {
    $this->requiring = $requiring;
  }
  /**
   * @return GroupKind
   */
  public function getRequiring()
  {
    return $this->requiring;
  }
  /**
   * Required. The satisfying group kind must be restored first in order to
   * satisfy the dependency.
   *
   * @param GroupKind $satisfying
   */
  public function setSatisfying(GroupKind $satisfying)
  {
    $this->satisfying = $satisfying;
  }
  /**
   * @return GroupKind
   */
  public function getSatisfying()
  {
    return $this->satisfying;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupKindDependency::class, 'Google_Service_BackupforGKE_GroupKindDependency');
