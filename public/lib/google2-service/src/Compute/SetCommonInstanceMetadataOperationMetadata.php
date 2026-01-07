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

class SetCommonInstanceMetadataOperationMetadata extends \Google\Model
{
  /**
   * [Output Only] The client operation id.
   *
   * @var string
   */
  public $clientOperationId;
  protected $perLocationOperationsType = SetCommonInstanceMetadataOperationMetadataPerLocationOperationInfo::class;
  protected $perLocationOperationsDataType = 'map';

  /**
   * [Output Only] The client operation id.
   *
   * @param string $clientOperationId
   */
  public function setClientOperationId($clientOperationId)
  {
    $this->clientOperationId = $clientOperationId;
  }
  /**
   * @return string
   */
  public function getClientOperationId()
  {
    return $this->clientOperationId;
  }
  /**
   * [Output Only] Status information per location (location name is key).
   * Example key: zones/us-central1-a
   *
   * @param SetCommonInstanceMetadataOperationMetadataPerLocationOperationInfo[] $perLocationOperations
   */
  public function setPerLocationOperations($perLocationOperations)
  {
    $this->perLocationOperations = $perLocationOperations;
  }
  /**
   * @return SetCommonInstanceMetadataOperationMetadataPerLocationOperationInfo[]
   */
  public function getPerLocationOperations()
  {
    return $this->perLocationOperations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetCommonInstanceMetadataOperationMetadata::class, 'Google_Service_Compute_SetCommonInstanceMetadataOperationMetadata');
