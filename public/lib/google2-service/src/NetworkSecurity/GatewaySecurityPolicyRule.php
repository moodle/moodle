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

namespace Google\Service\NetworkSecurity;

class GatewaySecurityPolicyRule extends \Google\Model
{
  /**
   * If there is not a mentioned action for the target.
   */
  public const BASIC_PROFILE_BASIC_PROFILE_UNSPECIFIED = 'BASIC_PROFILE_UNSPECIFIED';
  /**
   * Allow the matched traffic.
   */
  public const BASIC_PROFILE_ALLOW = 'ALLOW';
  /**
   * Deny the matched traffic.
   */
  public const BASIC_PROFILE_DENY = 'DENY';
  /**
   * Optional. CEL expression for matching on L7/application level criteria.
   *
   * @var string
   */
  public $applicationMatcher;
  /**
   * Required. Profile which tells what the primitive action should be.
   *
   * @var string
   */
  public $basicProfile;
  /**
   * Output only. Time when the rule was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Free-text description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Whether the rule is enforced.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Required. Immutable. Name of the resource. ame is the full resource name so
   * projects/{project}/locations/{location}/gatewaySecurityPolicies/{gateway_se
   * curity_policy}/rules/{rule} rule should match the pattern:
   * (^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
   *
   * @var string
   */
  public $name;
  /**
   * Required. Priority of the rule. Lower number corresponds to higher
   * precedence.
   *
   * @var int
   */
  public $priority;
  /**
   * Required. CEL expression for matching on session criteria.
   *
   * @var string
   */
  public $sessionMatcher;
  /**
   * Optional. Flag to enable TLS inspection of traffic matching on , can only
   * be true if the parent GatewaySecurityPolicy references a
   * TLSInspectionConfig.
   *
   * @var bool
   */
  public $tlsInspectionEnabled;
  /**
   * Output only. Time when the rule was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. CEL expression for matching on L7/application level criteria.
   *
   * @param string $applicationMatcher
   */
  public function setApplicationMatcher($applicationMatcher)
  {
    $this->applicationMatcher = $applicationMatcher;
  }
  /**
   * @return string
   */
  public function getApplicationMatcher()
  {
    return $this->applicationMatcher;
  }
  /**
   * Required. Profile which tells what the primitive action should be.
   *
   * Accepted values: BASIC_PROFILE_UNSPECIFIED, ALLOW, DENY
   *
   * @param self::BASIC_PROFILE_* $basicProfile
   */
  public function setBasicProfile($basicProfile)
  {
    $this->basicProfile = $basicProfile;
  }
  /**
   * @return self::BASIC_PROFILE_*
   */
  public function getBasicProfile()
  {
    return $this->basicProfile;
  }
  /**
   * Output only. Time when the rule was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Free-text description of the resource.
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
   * Required. Whether the rule is enforced.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Required. Immutable. Name of the resource. ame is the full resource name so
   * projects/{project}/locations/{location}/gatewaySecurityPolicies/{gateway_se
   * curity_policy}/rules/{rule} rule should match the pattern:
   * (^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
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
   * Required. Priority of the rule. Lower number corresponds to higher
   * precedence.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Required. CEL expression for matching on session criteria.
   *
   * @param string $sessionMatcher
   */
  public function setSessionMatcher($sessionMatcher)
  {
    $this->sessionMatcher = $sessionMatcher;
  }
  /**
   * @return string
   */
  public function getSessionMatcher()
  {
    return $this->sessionMatcher;
  }
  /**
   * Optional. Flag to enable TLS inspection of traffic matching on , can only
   * be true if the parent GatewaySecurityPolicy references a
   * TLSInspectionConfig.
   *
   * @param bool $tlsInspectionEnabled
   */
  public function setTlsInspectionEnabled($tlsInspectionEnabled)
  {
    $this->tlsInspectionEnabled = $tlsInspectionEnabled;
  }
  /**
   * @return bool
   */
  public function getTlsInspectionEnabled()
  {
    return $this->tlsInspectionEnabled;
  }
  /**
   * Output only. Time when the rule was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GatewaySecurityPolicyRule::class, 'Google_Service_NetworkSecurity_GatewaySecurityPolicyRule');
