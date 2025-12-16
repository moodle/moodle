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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class DomainJoinMachineRequest extends \Google\Model
{
  /**
   * Optional. force if True, forces domain join even if the computer account
   * already exists.
   *
   * @var bool
   */
  public $force;
  /**
   * Optional. OU name where the VM needs to be domain joined
   *
   * @var string
   */
  public $ouName;
  /**
   * Required. Full instance id token of compute engine VM to verify instance
   * identity. More about this:
   * https://cloud.google.com/compute/docs/instances/verifying-instance-
   * identity#request_signature
   *
   * @var string
   */
  public $vmIdToken;

  /**
   * Optional. force if True, forces domain join even if the computer account
   * already exists.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * Optional. OU name where the VM needs to be domain joined
   *
   * @param string $ouName
   */
  public function setOuName($ouName)
  {
    $this->ouName = $ouName;
  }
  /**
   * @return string
   */
  public function getOuName()
  {
    return $this->ouName;
  }
  /**
   * Required. Full instance id token of compute engine VM to verify instance
   * identity. More about this:
   * https://cloud.google.com/compute/docs/instances/verifying-instance-
   * identity#request_signature
   *
   * @param string $vmIdToken
   */
  public function setVmIdToken($vmIdToken)
  {
    $this->vmIdToken = $vmIdToken;
  }
  /**
   * @return string
   */
  public function getVmIdToken()
  {
    return $this->vmIdToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DomainJoinMachineRequest::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_DomainJoinMachineRequest');
