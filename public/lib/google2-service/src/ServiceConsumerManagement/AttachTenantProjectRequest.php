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

class AttachTenantProjectRequest extends \Google\Model
{
  /**
   * When attaching an external project, this is in the format of
   * `projects/{project_number}`.
   *
   * @var string
   */
  public $externalResource;
  /**
   * When attaching a reserved project already in tenancy units, this is the tag
   * of a tenant resource under the tenancy unit for the managed service's
   * service producer project. The reserved tenant resource must be in an active
   * state.
   *
   * @var string
   */
  public $reservedResource;
  /**
   * Required. Tag of the tenant resource after attachment. Must be less than
   * 128 characters. Required.
   *
   * @var string
   */
  public $tag;

  /**
   * When attaching an external project, this is in the format of
   * `projects/{project_number}`.
   *
   * @param string $externalResource
   */
  public function setExternalResource($externalResource)
  {
    $this->externalResource = $externalResource;
  }
  /**
   * @return string
   */
  public function getExternalResource()
  {
    return $this->externalResource;
  }
  /**
   * When attaching a reserved project already in tenancy units, this is the tag
   * of a tenant resource under the tenancy unit for the managed service's
   * service producer project. The reserved tenant resource must be in an active
   * state.
   *
   * @param string $reservedResource
   */
  public function setReservedResource($reservedResource)
  {
    $this->reservedResource = $reservedResource;
  }
  /**
   * @return string
   */
  public function getReservedResource()
  {
    return $this->reservedResource;
  }
  /**
   * Required. Tag of the tenant resource after attachment. Must be less than
   * 128 characters. Required.
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
class_alias(AttachTenantProjectRequest::class, 'Google_Service_ServiceConsumerManagement_AttachTenantProjectRequest');
