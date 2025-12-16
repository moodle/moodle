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

class BulkInsertDiskResource extends \Google\Model
{
  /**
   * The URL of the DiskConsistencyGroupPolicy for the group of disks to clone.
   * This may be a full or partial URL, such as:              -         https://
   * www.googleapis.com/compute/v1/projects/project/regions/region/resourcePolic
   * ies/resourcePolicy            -
   * projects/project/regions/region/resourcePolicies/resourcePolicy
   * -         regions/region/resourcePolicies/resourcePolicy
   *
   * @var string
   */
  public $sourceConsistencyGroupPolicy;

  /**
   * The URL of the DiskConsistencyGroupPolicy for the group of disks to clone.
   * This may be a full or partial URL, such as:              -         https://
   * www.googleapis.com/compute/v1/projects/project/regions/region/resourcePolic
   * ies/resourcePolicy            -
   * projects/project/regions/region/resourcePolicies/resourcePolicy
   * -         regions/region/resourcePolicies/resourcePolicy
   *
   * @param string $sourceConsistencyGroupPolicy
   */
  public function setSourceConsistencyGroupPolicy($sourceConsistencyGroupPolicy)
  {
    $this->sourceConsistencyGroupPolicy = $sourceConsistencyGroupPolicy;
  }
  /**
   * @return string
   */
  public function getSourceConsistencyGroupPolicy()
  {
    return $this->sourceConsistencyGroupPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkInsertDiskResource::class, 'Google_Service_Compute_BulkInsertDiskResource');
