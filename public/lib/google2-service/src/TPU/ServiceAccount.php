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

namespace Google\Service\TPU;

class ServiceAccount extends \Google\Collection
{
  protected $collection_key = 'scope';
  /**
   * Email address of the service account. If empty, default Compute service
   * account will be used.
   *
   * @var string
   */
  public $email;
  /**
   * The list of scopes to be made available for this service account. If empty,
   * access to all Cloud APIs will be allowed.
   *
   * @var string[]
   */
  public $scope;

  /**
   * Email address of the service account. If empty, default Compute service
   * account will be used.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * The list of scopes to be made available for this service account. If empty,
   * access to all Cloud APIs will be allowed.
   *
   * @param string[] $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string[]
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAccount::class, 'Google_Service_TPU_ServiceAccount');
