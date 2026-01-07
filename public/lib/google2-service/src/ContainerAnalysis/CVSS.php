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

namespace Google\Service\ContainerAnalysis;

class CVSS extends \Google\Model
{
  public const ATTACK_COMPLEXITY_ATTACK_COMPLEXITY_UNSPECIFIED = 'ATTACK_COMPLEXITY_UNSPECIFIED';
  public const ATTACK_COMPLEXITY_ATTACK_COMPLEXITY_LOW = 'ATTACK_COMPLEXITY_LOW';
  public const ATTACK_COMPLEXITY_ATTACK_COMPLEXITY_HIGH = 'ATTACK_COMPLEXITY_HIGH';
  public const ATTACK_COMPLEXITY_ATTACK_COMPLEXITY_MEDIUM = 'ATTACK_COMPLEXITY_MEDIUM';
  public const ATTACK_VECTOR_ATTACK_VECTOR_UNSPECIFIED = 'ATTACK_VECTOR_UNSPECIFIED';
  public const ATTACK_VECTOR_ATTACK_VECTOR_NETWORK = 'ATTACK_VECTOR_NETWORK';
  public const ATTACK_VECTOR_ATTACK_VECTOR_ADJACENT = 'ATTACK_VECTOR_ADJACENT';
  public const ATTACK_VECTOR_ATTACK_VECTOR_LOCAL = 'ATTACK_VECTOR_LOCAL';
  public const ATTACK_VECTOR_ATTACK_VECTOR_PHYSICAL = 'ATTACK_VECTOR_PHYSICAL';
  public const AUTHENTICATION_AUTHENTICATION_UNSPECIFIED = 'AUTHENTICATION_UNSPECIFIED';
  public const AUTHENTICATION_AUTHENTICATION_MULTIPLE = 'AUTHENTICATION_MULTIPLE';
  public const AUTHENTICATION_AUTHENTICATION_SINGLE = 'AUTHENTICATION_SINGLE';
  public const AUTHENTICATION_AUTHENTICATION_NONE = 'AUTHENTICATION_NONE';
  public const AVAILABILITY_IMPACT_IMPACT_UNSPECIFIED = 'IMPACT_UNSPECIFIED';
  public const AVAILABILITY_IMPACT_IMPACT_HIGH = 'IMPACT_HIGH';
  public const AVAILABILITY_IMPACT_IMPACT_LOW = 'IMPACT_LOW';
  public const AVAILABILITY_IMPACT_IMPACT_NONE = 'IMPACT_NONE';
  public const AVAILABILITY_IMPACT_IMPACT_PARTIAL = 'IMPACT_PARTIAL';
  public const AVAILABILITY_IMPACT_IMPACT_COMPLETE = 'IMPACT_COMPLETE';
  public const CONFIDENTIALITY_IMPACT_IMPACT_UNSPECIFIED = 'IMPACT_UNSPECIFIED';
  public const CONFIDENTIALITY_IMPACT_IMPACT_HIGH = 'IMPACT_HIGH';
  public const CONFIDENTIALITY_IMPACT_IMPACT_LOW = 'IMPACT_LOW';
  public const CONFIDENTIALITY_IMPACT_IMPACT_NONE = 'IMPACT_NONE';
  public const CONFIDENTIALITY_IMPACT_IMPACT_PARTIAL = 'IMPACT_PARTIAL';
  public const CONFIDENTIALITY_IMPACT_IMPACT_COMPLETE = 'IMPACT_COMPLETE';
  public const INTEGRITY_IMPACT_IMPACT_UNSPECIFIED = 'IMPACT_UNSPECIFIED';
  public const INTEGRITY_IMPACT_IMPACT_HIGH = 'IMPACT_HIGH';
  public const INTEGRITY_IMPACT_IMPACT_LOW = 'IMPACT_LOW';
  public const INTEGRITY_IMPACT_IMPACT_NONE = 'IMPACT_NONE';
  public const INTEGRITY_IMPACT_IMPACT_PARTIAL = 'IMPACT_PARTIAL';
  public const INTEGRITY_IMPACT_IMPACT_COMPLETE = 'IMPACT_COMPLETE';
  public const PRIVILEGES_REQUIRED_PRIVILEGES_REQUIRED_UNSPECIFIED = 'PRIVILEGES_REQUIRED_UNSPECIFIED';
  public const PRIVILEGES_REQUIRED_PRIVILEGES_REQUIRED_NONE = 'PRIVILEGES_REQUIRED_NONE';
  public const PRIVILEGES_REQUIRED_PRIVILEGES_REQUIRED_LOW = 'PRIVILEGES_REQUIRED_LOW';
  public const PRIVILEGES_REQUIRED_PRIVILEGES_REQUIRED_HIGH = 'PRIVILEGES_REQUIRED_HIGH';
  public const SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  public const SCOPE_SCOPE_UNCHANGED = 'SCOPE_UNCHANGED';
  public const SCOPE_SCOPE_CHANGED = 'SCOPE_CHANGED';
  public const USER_INTERACTION_USER_INTERACTION_UNSPECIFIED = 'USER_INTERACTION_UNSPECIFIED';
  public const USER_INTERACTION_USER_INTERACTION_NONE = 'USER_INTERACTION_NONE';
  public const USER_INTERACTION_USER_INTERACTION_REQUIRED = 'USER_INTERACTION_REQUIRED';
  /**
   * @var string
   */
  public $attackComplexity;
  /**
   * Base Metrics Represents the intrinsic characteristics of a vulnerability
   * that are constant over time and across user environments.
   *
   * @var string
   */
  public $attackVector;
  /**
   * @var string
   */
  public $authentication;
  /**
   * @var string
   */
  public $availabilityImpact;
  /**
   * The base score is a function of the base metric scores.
   *
   * @var float
   */
  public $baseScore;
  /**
   * @var string
   */
  public $confidentialityImpact;
  /**
   * @var float
   */
  public $exploitabilityScore;
  /**
   * @var float
   */
  public $impactScore;
  /**
   * @var string
   */
  public $integrityImpact;
  /**
   * @var string
   */
  public $privilegesRequired;
  /**
   * @var string
   */
  public $scope;
  /**
   * @var string
   */
  public $userInteraction;

  /**
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
   * that are constant over time and across user environments.
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
   * @param self::AUTHENTICATION_* $authentication
   */
  public function setAuthentication($authentication)
  {
    $this->authentication = $authentication;
  }
  /**
   * @return self::AUTHENTICATION_*
   */
  public function getAuthentication()
  {
    return $this->authentication;
  }
  /**
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
  /**
   * The base score is a function of the base metric scores.
   *
   * @param float $baseScore
   */
  public function setBaseScore($baseScore)
  {
    $this->baseScore = $baseScore;
  }
  /**
   * @return float
   */
  public function getBaseScore()
  {
    return $this->baseScore;
  }
  /**
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
   * @param float $exploitabilityScore
   */
  public function setExploitabilityScore($exploitabilityScore)
  {
    $this->exploitabilityScore = $exploitabilityScore;
  }
  /**
   * @return float
   */
  public function getExploitabilityScore()
  {
    return $this->exploitabilityScore;
  }
  /**
   * @param float $impactScore
   */
  public function setImpactScore($impactScore)
  {
    $this->impactScore = $impactScore;
  }
  /**
   * @return float
   */
  public function getImpactScore()
  {
    return $this->impactScore;
  }
  /**
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
class_alias(CVSS::class, 'Google_Service_ContainerAnalysis_CVSS');
