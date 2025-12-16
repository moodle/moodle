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

namespace Google\Service\CloudDeploy;

class MultiTarget extends \Google\Collection
{
  protected $collection_key = 'targetIds';
  /**
   * Required. The target_ids of this multiTarget.
   *
   * @var string[]
   */
  public $targetIds;

  /**
   * Required. The target_ids of this multiTarget.
   *
   * @param string[] $targetIds
   */
  public function setTargetIds($targetIds)
  {
    $this->targetIds = $targetIds;
  }
  /**
   * @return string[]
   */
  public function getTargetIds()
  {
    return $this->targetIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MultiTarget::class, 'Google_Service_CloudDeploy_MultiTarget');
