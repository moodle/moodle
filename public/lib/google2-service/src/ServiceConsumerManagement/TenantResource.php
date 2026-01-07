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

namespace Google\Service\ServiceConsumerManagement;

class TenantResource extends \Google\Model
{
  /**
   * Unspecified status is the default unset value.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Creation of the tenant resource is ongoing.
   */
  public const STATUS_PENDING_CREATE = 'PENDING_CREATE';
  /**
   * Active resource.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * Deletion of the resource is ongoing.
   */
  public const STATUS_PENDING_DELETE = 'PENDING_DELETE';
  /**
   * Tenant resource creation or deletion has failed.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * Tenant resource has been deleted.
   */
  public const STATUS_DELETED = 'DELETED';
  /**
   * Output only. @OutputOnly Identifier of the tenant resource. For cloud
   * projects, it is in the form 'projects/{number}'. For example
   * 'projects/123456'.
   *
   * @var string
   */
  public $resource;
  /**
   * Status of tenant resource.
   *
   * @var string
   */
  public $status;
  /**
   * Unique per single tenancy unit.
   *
   * @var string
   */
  public $tag;

  /**
   * Output only. @OutputOnly Identifier of the tenant resource. For cloud
   * projects, it is in the form 'projects/{number}'. For example
   * 'projects/123456'.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Status of tenant resource.
   *
   * Accepted values: STATUS_UNSPECIFIED, PENDING_CREATE, ACTIVE,
   * PENDING_DELETE, FAILED, DELETED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Unique per single tenancy unit.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TenantResource::class, 'Google_Service_ServiceConsumerManagement_TenantResource');
