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

class GoogleCloudSecuritycenterV2CloudArmor extends \Google\Model
{
  protected $adaptiveProtectionType = GoogleCloudSecuritycenterV2AdaptiveProtection::class;
  protected $adaptiveProtectionDataType = '';
  protected $attackType = GoogleCloudSecuritycenterV2Attack::class;
  protected $attackDataType = '';
  /**
   * Duration of attack from the start until the current moment (updated every 5
   * minutes).
   *
   * @var string
   */
  public $duration;
  protected $requestsType = GoogleCloudSecuritycenterV2Requests::class;
  protected $requestsDataType = '';
  protected $securityPolicyType = GoogleCloudSecuritycenterV2SecurityPolicy::class;
  protected $securityPolicyDataType = '';
  /**
   * Distinguish between volumetric & protocol DDoS attack and application layer
   * attacks. For example, "L3_4" for Layer 3 and Layer 4 DDoS attacks, or "L_7"
   * for Layer 7 DDoS attacks.
   *
   * @var string
   */
  public $threatVector;

  /**
   * Information about potential Layer 7 DDoS attacks identified by [Google
   * Cloud Armor Adaptive
   * Protection](https://cloud.google.com/armor/docs/adaptive-protection-
   * overview).
   *
   * @param GoogleCloudSecuritycenterV2AdaptiveProtection $adaptiveProtection
   */
  public function setAdaptiveProtection(GoogleCloudSecuritycenterV2AdaptiveProtection $adaptiveProtection)
  {
    $this->adaptiveProtection = $adaptiveProtection;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AdaptiveProtection
   */
  public function getAdaptiveProtection()
  {
    return $this->adaptiveProtection;
  }
  /**
   * Information about DDoS attack volume and classification.
   *
   * @param GoogleCloudSecuritycenterV2Attack $attack
   */
  public function setAttack(GoogleCloudSecuritycenterV2Attack $attack)
  {
    $this->attack = $attack;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Attack
   */
  public function getAttack()
  {
    return $this->attack;
  }
  /**
   * Duration of attack from the start until the current moment (updated every 5
   * minutes).
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Information about incoming requests evaluated by [Google Cloud Armor
   * security policies](https://cloud.google.com/armor/docs/security-policy-
   * overview).
   *
   * @param GoogleCloudSecuritycenterV2Requests $requests
   */
  public function setRequests(GoogleCloudSecuritycenterV2Requests $requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Requests
   */
  public function getRequests()
  {
    return $this->requests;
  }
  /**
   * Information about the [Google Cloud Armor security
   * policy](https://cloud.google.com/armor/docs/security-policy-overview)
   * relevant to the finding.
   *
   * @param GoogleCloudSecuritycenterV2SecurityPolicy $securityPolicy
   */
  public function setSecurityPolicy(GoogleCloudSecuritycenterV2SecurityPolicy $securityPolicy)
  {
    $this->securityPolicy = $securityPolicy;
  }
  /**
   * @return GoogleCloudSecuritycenterV2SecurityPolicy
   */
  public function getSecurityPolicy()
  {
    return $this->securityPolicy;
  }
  /**
   * Distinguish between volumetric & protocol DDoS attack and application layer
   * attacks. For example, "L3_4" for Layer 3 and Layer 4 DDoS attacks, or "L_7"
   * for Layer 7 DDoS attacks.
   *
   * @param string $threatVector
   */
  public function setThreatVector($threatVector)
  {
    $this->threatVector = $threatVector;
  }
  /**
   * @return string
   */
  public function getThreatVector()
  {
    return $this->threatVector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2CloudArmor::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2CloudArmor');
