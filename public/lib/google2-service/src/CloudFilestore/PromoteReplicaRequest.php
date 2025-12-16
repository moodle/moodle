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

namespace Google\Service\CloudFilestore;

class PromoteReplicaRequest extends \Google\Model
{
  /**
   * Optional. The resource name of the peer instance to promote, in the format
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`.
   * The peer instance is required if the operation is called on an active
   * instance.
   *
   * @var string
   */
  public $peerInstance;

  /**
   * Optional. The resource name of the peer instance to promote, in the format
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`.
   * The peer instance is required if the operation is called on an active
   * instance.
   *
   * @param string $peerInstance
   */
  public function setPeerInstance($peerInstance)
  {
    $this->peerInstance = $peerInstance;
  }
  /**
   * @return string
   */
  public function getPeerInstance()
  {
    return $this->peerInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PromoteReplicaRequest::class, 'Google_Service_CloudFilestore_PromoteReplicaRequest');
