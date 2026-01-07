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

namespace Google\Service\ArtifactRegistry;

class VPCSCConfig extends \Google\Model
{
  /**
   * VPCSC_POLICY_UNSPECIFIED - the VPS SC policy is not defined. When VPS SC
   * policy is not defined - the Service will use the default behavior
   * (VPCSC_DENY).
   */
  public const VPCSC_POLICY_VPCSC_POLICY_UNSPECIFIED = 'VPCSC_POLICY_UNSPECIFIED';
  /**
   * VPCSC_DENY - repository will block the requests to the Upstreams for the
   * Remote Repositories if the resource is in the perimeter.
   */
  public const VPCSC_POLICY_DENY = 'DENY';
  /**
   * VPCSC_ALLOW - repository will allow the requests to the Upstreams for the
   * Remote Repositories if the resource is in the perimeter.
   */
  public const VPCSC_POLICY_ALLOW = 'ALLOW';
  /**
   * The name of the project's VPC SC Config. Always of the form:
   * projects/{projectID}/locations/{location}/vpcscConfig In update request:
   * never set In response: always set
   *
   * @var string
   */
  public $name;
  /**
   * The project per location VPC SC policy that defines the VPC SC behavior for
   * the Remote Repository (Allow/Deny).
   *
   * @var string
   */
  public $vpcscPolicy;

  /**
   * The name of the project's VPC SC Config. Always of the form:
   * projects/{projectID}/locations/{location}/vpcscConfig In update request:
   * never set In response: always set
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
   * The project per location VPC SC policy that defines the VPC SC behavior for
   * the Remote Repository (Allow/Deny).
   *
   * Accepted values: VPCSC_POLICY_UNSPECIFIED, DENY, ALLOW
   *
   * @param self::VPCSC_POLICY_* $vpcscPolicy
   */
  public function setVpcscPolicy($vpcscPolicy)
  {
    $this->vpcscPolicy = $vpcscPolicy;
  }
  /**
   * @return self::VPCSC_POLICY_*
   */
  public function getVpcscPolicy()
  {
    return $this->vpcscPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VPCSCConfig::class, 'Google_Service_ArtifactRegistry_VPCSCConfig');
