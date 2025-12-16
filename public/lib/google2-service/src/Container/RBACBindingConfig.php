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

namespace Google\Service\Container;

class RBACBindingConfig extends \Google\Model
{
  /**
   * Setting this to true will allow any ClusterRoleBinding and RoleBinding with
   * subjects system:authenticated.
   *
   * @var bool
   */
  public $enableInsecureBindingSystemAuthenticated;
  /**
   * Setting this to true will allow any ClusterRoleBinding and RoleBinding with
   * subjets system:anonymous or system:unauthenticated.
   *
   * @var bool
   */
  public $enableInsecureBindingSystemUnauthenticated;

  /**
   * Setting this to true will allow any ClusterRoleBinding and RoleBinding with
   * subjects system:authenticated.
   *
   * @param bool $enableInsecureBindingSystemAuthenticated
   */
  public function setEnableInsecureBindingSystemAuthenticated($enableInsecureBindingSystemAuthenticated)
  {
    $this->enableInsecureBindingSystemAuthenticated = $enableInsecureBindingSystemAuthenticated;
  }
  /**
   * @return bool
   */
  public function getEnableInsecureBindingSystemAuthenticated()
  {
    return $this->enableInsecureBindingSystemAuthenticated;
  }
  /**
   * Setting this to true will allow any ClusterRoleBinding and RoleBinding with
   * subjets system:anonymous or system:unauthenticated.
   *
   * @param bool $enableInsecureBindingSystemUnauthenticated
   */
  public function setEnableInsecureBindingSystemUnauthenticated($enableInsecureBindingSystemUnauthenticated)
  {
    $this->enableInsecureBindingSystemUnauthenticated = $enableInsecureBindingSystemUnauthenticated;
  }
  /**
   * @return bool
   */
  public function getEnableInsecureBindingSystemUnauthenticated()
  {
    return $this->enableInsecureBindingSystemUnauthenticated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RBACBindingConfig::class, 'Google_Service_Container_RBACBindingConfig');
