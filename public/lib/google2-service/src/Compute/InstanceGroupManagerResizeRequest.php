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

class InstanceGroupManagerResizeRequest extends \Google\Model
{
  /**
   * The request was created successfully and was accepted for provisioning when
   * the capacity becomes available.
   */
  public const STATE_ACCEPTED = 'ACCEPTED';
  /**
   * The request is cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Resize request is being created and may still fail creation.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The request failed before or during provisioning. If the request fails
   * during provisioning, any VMs that were created during provisioning are
   * rolled back and removed from the MIG.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Default value. This value should never be returned.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The request succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Output only. [Output Only] The creation timestamp for this resize request
   * inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] A unique identifier for this resource type. The
   * server generates this identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] The resource type, which is
   * alwayscompute#instanceGroupManagerResizeRequest for resize requests.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of this resize request. The name must be 1-63 characters long, and
   * comply withRFC1035.
   *
   * @var string
   */
  public $name;
  protected $requestedRunDurationType = Duration::class;
  protected $requestedRunDurationDataType = '';
  /**
   * The number of instances to be created by this resize request. The group's
   * target size will be increased by this number. This field cannot be used
   * together with 'instances'.
   *
   * @var int
   */
  public $resizeBy;
  /**
   * Output only. [Output Only] The URL for this resize request. The server
   * defines this URL.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @var string
   */
  public $selfLinkWithId;
  /**
   * Output only. [Output only] Current state of the request.
   *
   * @var string
   */
  public $state;
  protected $statusType = InstanceGroupManagerResizeRequestStatus::class;
  protected $statusDataType = '';
  /**
   * Output only. [Output Only] The URL of azone where the resize request is
   * located. Populated only for zonal resize requests.
   *
   * @var string
   */
  public $zone;

  /**
   * Output only. [Output Only] The creation timestamp for this resize request
   * inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. [Output Only] A unique identifier for this resource type. The
   * server generates this identifier.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] The resource type, which is
   * alwayscompute#instanceGroupManagerResizeRequest for resize requests.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The name of this resize request. The name must be 1-63 characters long, and
   * comply withRFC1035.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Requested run duration for instances that will be created by this request.
   * At the end of the run duration instance will be deleted.
   *
   * @param Duration $requestedRunDuration
   */
  public function setRequestedRunDuration(Duration $requestedRunDuration)
  {
    $this->requestedRunDuration = $requestedRunDuration;
  }
  /**
   * @return Duration
   */
  public function getRequestedRunDuration()
  {
    return $this->requestedRunDuration;
  }
  /**
   * The number of instances to be created by this resize request. The group's
   * target size will be increased by this number. This field cannot be used
   * together with 'instances'.
   *
   * @param int $resizeBy
   */
  public function setResizeBy($resizeBy)
  {
    $this->resizeBy = $resizeBy;
  }
  /**
   * @return int
   */
  public function getResizeBy()
  {
    return $this->resizeBy;
  }
  /**
   * Output only. [Output Only] The URL for this resize request. The server
   * defines this URL.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
  /**
   * Output only. [Output only] Current state of the request.
   *
   * Accepted values: ACCEPTED, CANCELLED, CREATING, FAILED, STATE_UNSPECIFIED,
   * SUCCEEDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. [Output only] Status of the request.
   *
   * @param InstanceGroupManagerResizeRequestStatus $status
   */
  public function setStatus(InstanceGroupManagerResizeRequestStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return InstanceGroupManagerResizeRequestStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. [Output Only] The URL of azone where the resize request is
   * located. Populated only for zonal resize requests.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerResizeRequest::class, 'Google_Service_Compute_InstanceGroupManagerResizeRequest');
