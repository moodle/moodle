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

namespace Google\Service\VMwareEngine;

class MountDatastoreRequest extends \Google\Model
{
  protected $datastoreMountConfigType = DatastoreMountConfig::class;
  protected $datastoreMountConfigDataType = '';
  /**
   * Optional. If set to true, the colocation requirement will be ignored. If
   * set to false, the colocation requirement will be enforced. If not set, the
   * colocation requirement will be enforced. Colocation requirement is the
   * requirement that the cluster must be in the same region/zone of
   * datastore(regional/zonal datastore).
   *
   * @var bool
   */
  public $ignoreColocation;
  /**
   * Optional. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;

  /**
   * Required. The datastore mount configuration.
   *
   * @param DatastoreMountConfig $datastoreMountConfig
   */
  public function setDatastoreMountConfig(DatastoreMountConfig $datastoreMountConfig)
  {
    $this->datastoreMountConfig = $datastoreMountConfig;
  }
  /**
   * @return DatastoreMountConfig
   */
  public function getDatastoreMountConfig()
  {
    return $this->datastoreMountConfig;
  }
  /**
   * Optional. If set to true, the colocation requirement will be ignored. If
   * set to false, the colocation requirement will be enforced. If not set, the
   * colocation requirement will be enforced. Colocation requirement is the
   * requirement that the cluster must be in the same region/zone of
   * datastore(regional/zonal datastore).
   *
   * @param bool $ignoreColocation
   */
  public function setIgnoreColocation($ignoreColocation)
  {
    $this->ignoreColocation = $ignoreColocation;
  }
  /**
   * @return bool
   */
  public function getIgnoreColocation()
  {
    return $this->ignoreColocation;
  }
  /**
   * Optional. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
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
class_alias(MountDatastoreRequest::class, 'Google_Service_VMwareEngine_MountDatastoreRequest');
