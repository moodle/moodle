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

namespace Google\Service\Dataproc;

class ResizeNodeGroupRequest extends \Google\Model
{
  /**
   * Optional. Timeout for graceful YARN decommissioning. Graceful
   * decommissioning
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/scaling-clusters#graceful_decommissioning) allows the removal of
   * nodes from the Compute Engine node group without interrupting jobs in
   * progress. This timeout specifies how long to wait for jobs in progress to
   * finish before forcefully removing nodes (and potentially interrupting
   * jobs). Default timeout is 0 (for forceful decommission), and the maximum
   * allowed timeout is 1 day. (see JSON representation of Duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).Only
   * supported on Dataproc image versions 1.2 and higher.
   *
   * @var string
   */
  public $gracefulDecommissionTimeout;
  /**
   * Optional. operation id of the parent operation sending the resize request
   *
   * @var string
   */
  public $parentOperationId;
  /**
   * Optional. A unique ID used to identify the request. If the server receives
   * two ResizeNodeGroupRequest (https://cloud.google.com/dataproc/docs/referenc
   * e/rpc/google.cloud.dataproc.v1#google.cloud.dataproc.v1.ResizeNodeGroupRequ
   * ests) with the same ID, the second request is ignored and the first
   * google.longrunning.Operation created and stored in the backend is
   * returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @var string
   */
  public $requestId;
  /**
   * Required. The number of running instances for the node group to maintain.
   * The group adds or removes instances to maintain the number of instances
   * specified by this parameter.
   *
   * @var int
   */
  public $size;

  /**
   * Optional. Timeout for graceful YARN decommissioning. Graceful
   * decommissioning
   * (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/scaling-clusters#graceful_decommissioning) allows the removal of
   * nodes from the Compute Engine node group without interrupting jobs in
   * progress. This timeout specifies how long to wait for jobs in progress to
   * finish before forcefully removing nodes (and potentially interrupting
   * jobs). Default timeout is 0 (for forceful decommission), and the maximum
   * allowed timeout is 1 day. (see JSON representation of Duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).Only
   * supported on Dataproc image versions 1.2 and higher.
   *
   * @param string $gracefulDecommissionTimeout
   */
  public function setGracefulDecommissionTimeout($gracefulDecommissionTimeout)
  {
    $this->gracefulDecommissionTimeout = $gracefulDecommissionTimeout;
  }
  /**
   * @return string
   */
  public function getGracefulDecommissionTimeout()
  {
    return $this->gracefulDecommissionTimeout;
  }
  /**
   * Optional. operation id of the parent operation sending the resize request
   *
   * @param string $parentOperationId
   */
  public function setParentOperationId($parentOperationId)
  {
    $this->parentOperationId = $parentOperationId;
  }
  /**
   * @return string
   */
  public function getParentOperationId()
  {
    return $this->parentOperationId;
  }
  /**
   * Optional. A unique ID used to identify the request. If the server receives
   * two ResizeNodeGroupRequest (https://cloud.google.com/dataproc/docs/referenc
   * e/rpc/google.cloud.dataproc.v1#google.cloud.dataproc.v1.ResizeNodeGroupRequ
   * ests) with the same ID, the second request is ignored and the first
   * google.longrunning.Operation created and stored in the backend is
   * returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
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
  /**
   * Required. The number of running instances for the node group to maintain.
   * The group adds or removes instances to maintain the number of instances
   * specified by this parameter.
   *
   * @param int $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return int
   */
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResizeNodeGroupRequest::class, 'Google_Service_Dataproc_ResizeNodeGroupRequest');
