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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2Cvssv3 extends \Google\Model
{
  /**
   * Invalid value.
   */
  public const ATTACK_COMPLEXITY_ATTACK_COMPLEXITY_UNSPECIFIED = 'ATTACK_COMPLEXITY_UNSPECIFIED';
  /**
   * Specialized access conditions or extenuating circumstances do not exist. An
   * attacker can expect repeatable success when attacking the vulnerable
   * component.
   */
  public const ATTACK_COMPLEXITY_ATTACK_COMPLEXITY_LOW = 'ATTACK_COMPLEXITY_LOW';
  /**
   * A successful attack depends on conditions beyond the attacker's control.
   * That is, a successful attack cannot be accomplished at will, but requires
   * the attacker to invest in some measurable amount of effort in preparation
   * or execution against the vulnerable component before a successful attack
   * can be expected.
   */
  public const ATTACK_COMPLEXITY_ATTACK_COMPLEXITY_HIGH = 'ATTACK_COMPLEXITY_HIGH';
  /**
   * Invalid value.
   */
  public const ATTACK_VECTOR_ATTACK_VECTOR_UNSPECIFIED = 'ATTACK_VECTOR_UNSPECIFIED';
  /**
   * The vulnerable component is bound to the network stack and the set of
   * possible attackers extends beyond the other options listed below, up to and
   * including the entire Internet.
   */
  public const ATTACK_VECTOR_ATTACK_VECTOR_NETWORK = 'ATTACK_VECTOR_NETWORK';
  /**
   * The vulnerable component is bound to the network stack, but the attack is
   * limited at the protocol level to a logically adjacent topology.
   */
  public const ATTACK_VECTOR_ATTACK_VECTOR_ADJACENT = 'ATTACK_VECTOR_ADJACENT';
  /**
   * The vulnerable component is not bound to the network stack and the
   * attacker's path is via read/write/execute capabilities.
   */
  public const ATTACK_VECTOR_ATTACK_VECTOR_LOCAL = 'ATTACK_VECTOR_LOCAL';
  /**
   * The attack requires the attacker to physically touch or manipulate the
   * vulnerable component.
   */
  public const ATTACK_VECTOR_ATTACK_VECTOR_PHYSICAL = 'ATTACK_VECTOR_PHYSICAL';
  /**
   * Invalid value.
   */
  public const AVAILABILITY_IMPACT_IMPACT_UNSPECIFIED = 'IMPACT_UNSPECIFIED';
  /**
   * High impact.
   */
  public const AVAILABILITY_IMPACT_IMPACT_HIGH = 'IMPACT_HIGH';
  /**
   * Low impact.
   */
  public const AVAILABILITY_IMPACT_IMPACT_LOW = 'IMPACT_LOW';
  /**
   * No impact.
   */
  public const AVAILABILITY_IMPACT_IMPACT_NONE = 'IMPACT_NONE';
  /**
   * Invalid value.
   */
  public const CONFIDENTIALITY_IMPACT_IMPACT_UNSPECIFIED = 'IMPACT_UNSPECIFIED';
  /**
   * High impact.
   */
  public const CONFIDENTIALITY_IMPACT_IMPACT_HIGH = 'IMPACT_HIGH';
  /**
   * Low impact.
   */
  public const CONFIDENTIALITY_IMPACT_IMPACT_LOW = 'IMPACT_LOW';
  /**
   * No impact.
   */
  public const CONFIDENTIALITY_IMPACT_IMPACT_NONE = 'IMPACT_NONE';
  /**
   * Invalid value.
   */
  public const INTEGRITY_IMPACT_IMPACT_UNSPECIFIED = 'IMPACT_UNSPECIFIED';
  /**
   * High impact.
   */
  public const INTEGRITY_IMPACT_IMPACT_HIGH = 'IMPACT_HIGH';
  /**
   * Low impact.
   */
  public const INTEGRITY_IMPACT_IMPACT_LOW = 'IMPACT_LOW';
  /**
   * No impact.
   */
  public const INTEGRITY_IMPACT_IMPACT_NONE = 'IMPACT_NONE';
  /**
   * Invalid value.
   */
  public const PRIVILEGES_REQUIRED_PRIVILEGES_REQUIRED_UNSPECIFIED = 'PRIVILEGES_REQUIRED_UNSPECIFIED';
  /**
   * The attacker is unauthorized prior to attack, and therefore does not
   * require any access to settings or files of the vulnerable system to carry
   * out an attack.
   */
  public const PRIVILEGES_REQUIRED_PRIVILEGES_REQUIRED_NONE = 'PRIVILEGES_REQUIRED_NONE';
  /**
   * The attacker requires privileges that provide basic user capabilities that
   * could normally affect only settings and files owned by a user.
   * Alternatively, an attacker with Low privileges has the ability to access
   * only non-sensitive resources.
   */
  public const PRIVILEGES_REQUIRED_PRIVILEGES_REQUIRED_LOW = 'PRIVILEGES_REQUIRED_LOW';
  /**
   * The attacker requires privileges that provide significant (e.g.,
   * administrative) control over the vulnerable component allowing access to
   * component-wide settings and files.
   */
  public const PRIVILEGES_REQUIRED_PRIVILEGES_REQUIRED_HIGH = 'PRIVILEGES_REQUIRED_HIGH';
  /**
   * Invalid value.
   */
  public const SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  /**
   * An exploited vulnerability can only affect resources managed by the same
   * security authority.
   */
  public const SCOPE_SCOPE_UNCHANGED = 'SCOPE_UNCHANGED';
  /**
   * An exploited vulnerability can affect resources beyond the security scope
   * managed by the security authority of the vulnerable component.
   */
  public const SCOPE_SCOPE_CHANGED = 'SCOPE_CHANGED';
  /**
   * Invalid value.
   */
  public const USER_INTERACTION_USER_INTERACTION_UNSPECIFIED = 'USER_INTERACTION_UNSPECIFIED';
  /**
   * The vulnerable system can be exploited without interaction from any user.
   */
  public const USER_INTERACTION_USER_INTERACTION_NONE = 'USER_INTERACTION_NONE';
  /**
   * Successful exploitation of this vulnerability requires a user to take some
   * action before the vulnerability can be exploited.
   */
  public const USER_INTERACTION_USER_INTERACTION_REQUIRED = 'USER_INTERACTION_REQUIRED';
  /**
   * This metric describes the conditions beyond the attacker's control that
   * must exist in order to exploit the vulnerability.
   *
   * @var string
   */
  public $attackComplexity;
  /**
   * Base Metrics Represents the intrinsic characteristics of a vulnerability
   * that are constant over time and across user environments. This metric
   * reflects the context by which vulnerability exploitation is possible.
   *
   * @var string
   */
  public $attackVector;
  /**
   * This metric measures the impact to the availability of the impacted
   * component resulting from a successfully exploited vulnerability.
   *
   * @var string
   */
  public $availabilityImpact;
  /**
   * The base score is a function of the base metric scores.
   *
   * @var 
   */
  public $baseScore;
  /**
   * This metric measures the impact to the confidentiality of the information
   * resources managed by a software component due to a successfully exploited
   * vulnerability.
   *
   * @var string
   */
  public $confidentialityImpact;
  /**
   * This metric measures the impact to integrity of a successfully exploited
   * vulnerability.
   *
   * @var string
   */
  public $integrityImpact;
  /**
   * This metric describes the level of privileges an attacker must possess
   * before successfully exploiting the vulnerability.
   *
   * @var string
   */
  public $privilegesRequired;
  /**
   * The Scope metric captures whether a vulnerability in one vulnerable
   * component impacts resources in components beyond its security scope.
   *
   * @var string
   */
  public $scope;
  /**
   * This metric captures the requirement for a human user, other than the
   * attacker, to participate in the successful compromise of the vulnerable
   * component.
   *
   * @var string
   */
  public $userInteraction;

  /**
   * This metric describes the conditions beyond the attacker's control that
   * must exist in order to exploit the vulnerability.
   *
   * Accepted values: ATTACK_COMPLEXITY_UNSPECIFIED, ATTACK_COMPLEXITY_LOW,
   * ATTACK_COMPLEXITY_HIGH
   *
   * @param self::ATTACK_COMPLEXITY_* $attackComplexity
   */
  public function setAttackComplexity($attackComplexity)
  {
    $this->attackComplexity = $attackComplexity;
  }
  /**
   * @return self::ATTACK_COMPLEXITY_*
   */
  public function getAttackComplexity()
  {
    return $this->attackComplexity;
  }
  /**
   * Base Metrics Represents the intrinsic characteristics of a vulnerability
   * that are constant over time and across user environments. This metric
   * reflects the context by which vulnerability exploitation is possible.
   *
   * Accepted values: ATTACK_VECTOR_UNSPECIFIED, ATTACK_VECTOR_NETWORK,
   * ATTACK_VECTOR_ADJACENT, ATTACK_VECTOR_LOCAL, ATTACK_VECTOR_PHYSICAL
   *
   * @param self::ATTACK_VECTOR_* $attackVector
   */
  public function setAttackVector($attackVector)
  {
    $this->attackVector = $attackVector;
  }
  /**
   * @return self::ATTACK_VECTOR_*
   */
  public function getAttackVector()
  {
    return $this->attackVector;
  }
  /**
   * This metric measures the impact to the availability of the impacted
   * component resulting from a successfully exploited vulnerability.
   *
   * Accepted values: IMPACT_UNSPECIFIED, IMPACT_HIGH, IMPACT_LOW, IMPACT_NONE
   *
   * @param self::AVAILABILITY_IMPACT_* $availabilityImpact
   */
  public function setAvailabilityImpact($availabilityImpact)
  {
    $this->availabilityImpact = $availabilityImpact;
  }
  /**
   * @return self::AVAILABILITY_IMPACT_*
   */
  public function getAvailabilityImpact()
  {
    return $this->availabilityImpact;
  }
  public function setBaseScore($baseScore)
  {
    $this->baseScore = $baseScore;
  }
  public function getBaseScore()
  {
    return $this->baseScore;
  }
  /**
   * This metric measures the impact to the confidentiality of the information
   * resources managed by a software component due to a successfully exploited
   * vulnerability.
   *
   * Accepted values: IMPACT_UNSPECIFIED, IMPACT_HIGH, IMPACT_LOW, IMPACT_NONE
   *
   * @param self::CONFIDENTIALITY_IMPACT_* $confidentialityImpact
   */
  public function setConfidentialityImpact($confidentialityImpact)
  {
    $this->confidentialityImpact = $confidentialityImpact;
  }
  /**
   * @return self::CONFIDENTIALITY_IMPACT_*
   */
  public function getConfidentialityImpact()
  {
    return $this->confidentialityImpact;
  }
  /**
   * This metric measures the impact to integrity of a successfully exploited
   * vulnerability.
   *
   * Accepted values: IMPACT_UNSPECIFIED, IMPACT_HIGH, IMPACT_LOW, IMPACT_NONE
   *
   * @param self::INTEGRITY_IMPACT_* $integrityImpact
   */
  public function setIntegrityImpact($integrityImpact)
  {
    $this->integrityImpact = $integrityImpact;
  }
  /**
   * @return self::INTEGRITY_IMPACT_*
   */
  public function getIntegrityImpact()
  {
    return $this->integrityImpact;
  }
  /**
   * This metric describes the level of privileges an attacker must possess
   * before successfully exploiting the vulnerability.
   *
   * Accepted values: PRIVILEGES_REQUIRED_UNSPECIFIED, PRIVILEGES_REQUIRED_NONE,
   * PRIVILEGES_REQUIRED_LOW, PRIVILEGES_REQUIRED_HIGH
   *
   * @param self::PRIVILEGES_REQUIRED_* $privilegesRequired
   */
  public function setPrivilegesRequired($privilegesRequired)
  {
    $this->privilegesRequired = $privilegesRequired;
  }
  /**
   * @return self::PRIVILEGES_REQUIRED_*
   */
  public function getPrivilegesRequired()
  {
    return $this->privilegesRequired;
  }
  /**
   * The Scope metric captures whether a vulnerability in one vulnerable
   * component impacts resources in components beyond its security scope.
   *
   * Accepted values: SCOPE_UNSPECIFIED, SCOPE_UNCHANGED, SCOPE_CHANGED
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * This metric captures the requirement for a human user, other than the
   * attacker, to participate in the successful compromise of the vulnerable
   * component.
   *
   * Accepted values: USER_INTERACTION_UNSPECIFIED, USER_INTERACTION_NONE,
   * USER_INTERACTION_REQUIRED
   *
   * @param self::USER_INTERACTION_* $userInteraction
   */
  public function setUserInteraction($userInteraction)
  {
    $this->userInteraction = $userInteraction;
  }
  /**
   * @return self::USER_INTERACTION_*
   */
  public function getUserInteraction()
  {
    return $this->userInteraction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2Cvssv3::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2Cvssv3');
