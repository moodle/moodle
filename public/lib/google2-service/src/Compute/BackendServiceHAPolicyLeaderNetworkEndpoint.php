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

class BackendServiceHAPolicyLeaderNetworkEndpoint extends \Google\Model
{
  /**
   * The name of the VM instance of the leader network endpoint. The instance
   * must already be attached to the NEG specified in the
   * haPolicy.leader.backendGroup.
   *
   * The name must be 1-63 characters long, and comply with RFC1035.
   * Authorization requires the following IAM permission on the specified
   * resource instance: compute.instances.use
   *
   * @var string
   */
  public $instance;

  /**
   * The name of the VM instance of the leader network endpoint. The instance
   * must already be attached to the NEG specified in the
   * haPolicy.leader.backendGroup.
   *
   * The name must be 1-63 characters long, and comply with RFC1035.
   * Authorization requires the following IAM permission on the specified
   * resource instance: compute.instances.use
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceHAPolicyLeaderNetworkEndpoint::class, 'Google_Service_Compute_BackendServiceHAPolicyLeaderNetworkEndpoint');
