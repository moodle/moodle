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

class InstancesSetSecurityPolicyRequest extends \Google\Collection
{
  protected $collection_key = 'networkInterfaces';
  /**
   * The network interfaces that the security policy will be applied to. Network
   * interfaces use the nicN naming format. You can only set a security policy
   * for network interfaces with an access config.
   *
   * @var string[]
   */
  public $networkInterfaces;
  /**
   * A full or partial URL to a security policy to add to this instance. If this
   * field is set to an empty string it will remove the associated security
   * policy.
   *
   * @var string
   */
  public $securityPolicy;

  /**
   * The network interfaces that the security policy will be applied to. Network
   * interfaces use the nicN naming format. You can only set a security policy
   * for network interfaces with an access config.
   *
   * @param string[] $networkInterfaces
   */
  public function setNetworkInterfaces($networkInterfaces)
  {
    $this->networkInterfaces = $networkInterfaces;
  }
  /**
   * @return string[]
   */
  public function getNetworkInterfaces()
  {
    return $this->networkInterfaces;
  }
  /**
   * A full or partial URL to a security policy to add to this instance. If this
   * field is set to an empty string it will remove the associated security
   * policy.
   *
   * @param string $securityPolicy
   */
  public function setSecurityPolicy($securityPolicy)
  {
    $this->securityPolicy = $securityPolicy;
  }
  /**
   * @return string
   */
  public function getSecurityPolicy()
  {
    return $this->securityPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesSetSecurityPolicyRequest::class, 'Google_Service_Compute_InstancesSetSecurityPolicyRequest');
