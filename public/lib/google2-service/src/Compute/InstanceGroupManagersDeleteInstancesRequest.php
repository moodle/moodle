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

class InstanceGroupManagersDeleteInstancesRequest extends \Google\Collection
{
  protected $collection_key = 'instances';
  /**
   * The URLs of one or more instances to delete. This can be a full URL or a
   * partial URL, such as zones/[ZONE]/instances/[INSTANCE_NAME]. Queued
   * instances do not have URL and can be deleted only by name. One cannot
   * specify both URLs and names in a single request.
   *
   * @var string[]
   */
  public $instances;
  /**
   * Specifies whether the request should proceed despite the inclusion of
   * instances that are not members of the group or that are already in the
   * process of being deleted or abandoned. If this field is set to `false` and
   * such an instance is specified in the request, the operation fails. The
   * operation always fails if the request contains a malformed instance URL or
   * a reference to an instance that exists in a zone or region other than the
   * group's zone or region.
   *
   * @var bool
   */
  public $skipInstancesOnValidationError;

  /**
   * The URLs of one or more instances to delete. This can be a full URL or a
   * partial URL, such as zones/[ZONE]/instances/[INSTANCE_NAME]. Queued
   * instances do not have URL and can be deleted only by name. One cannot
   * specify both URLs and names in a single request.
   *
   * @param string[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return string[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Specifies whether the request should proceed despite the inclusion of
   * instances that are not members of the group or that are already in the
   * process of being deleted or abandoned. If this field is set to `false` and
   * such an instance is specified in the request, the operation fails. The
   * operation always fails if the request contains a malformed instance URL or
   * a reference to an instance that exists in a zone or region other than the
   * group's zone or region.
   *
   * @param bool $skipInstancesOnValidationError
   */
  public function setSkipInstancesOnValidationError($skipInstancesOnValidationError)
  {
    $this->skipInstancesOnValidationError = $skipInstancesOnValidationError;
  }
  /**
   * @return bool
   */
  public function getSkipInstancesOnValidationError()
  {
    return $this->skipInstancesOnValidationError;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagersDeleteInstancesRequest::class, 'Google_Service_Compute_InstanceGroupManagersDeleteInstancesRequest');
