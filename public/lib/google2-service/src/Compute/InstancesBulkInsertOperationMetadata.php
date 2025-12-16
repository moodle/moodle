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

class InstancesBulkInsertOperationMetadata extends \Google\Model
{
  protected $perLocationStatusType = BulkInsertOperationStatus::class;
  protected $perLocationStatusDataType = 'map';

  /**
   * Status information per location (location name is key). Example key:
   * zones/us-central1-a
   *
   * @param BulkInsertOperationStatus[] $perLocationStatus
   */
  public function setPerLocationStatus($perLocationStatus)
  {
    $this->perLocationStatus = $perLocationStatus;
  }
  /**
   * @return BulkInsertOperationStatus[]
   */
  public function getPerLocationStatus()
  {
    return $this->perLocationStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesBulkInsertOperationMetadata::class, 'Google_Service_Compute_InstancesBulkInsertOperationMetadata');
