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

namespace Google\Service\GKEHub;

class CloudBuildSpec extends \Google\Model
{
  /**
   * Unspecified policy
   */
  public const SECURITY_POLICY_SECURITY_POLICY_UNSPECIFIED = 'SECURITY_POLICY_UNSPECIFIED';
  /**
   * Privileged build pods are disallowed
   */
  public const SECURITY_POLICY_NON_PRIVILEGED = 'NON_PRIVILEGED';
  /**
   * Privileged build pods are allowed
   */
  public const SECURITY_POLICY_PRIVILEGED = 'PRIVILEGED';
  /**
   * Whether it is allowed to run the privileged builds on the cluster or not.
   *
   * @var string
   */
  public $securityPolicy;
  /**
   * Version of the cloud build software on the cluster.
   *
   * @var string
   */
  public $version;

  /**
   * Whether it is allowed to run the privileged builds on the cluster or not.
   *
   * Accepted values: SECURITY_POLICY_UNSPECIFIED, NON_PRIVILEGED, PRIVILEGED
   *
   * @param self::SECURITY_POLICY_* $securityPolicy
   */
  public function setSecurityPolicy($securityPolicy)
  {
    $this->securityPolicy = $securityPolicy;
  }
  /**
   * @return self::SECURITY_POLICY_*
   */
  public function getSecurityPolicy()
  {
    return $this->securityPolicy;
  }
  /**
   * Version of the cloud build software on the cluster.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudBuildSpec::class, 'Google_Service_GKEHub_CloudBuildSpec');
