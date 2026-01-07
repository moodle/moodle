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

class SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfig extends \Google\Collection
{
  public const RULE_VISIBILITY_PREMIUM = 'PREMIUM';
  public const RULE_VISIBILITY_STANDARD = 'STANDARD';
  protected $collection_key = 'thresholdConfigs';
  /**
   * If set to true, enables CAAP for L7 DDoS detection. This field is only
   * supported in Global Security Policies of type CLOUD_ARMOR.
   *
   * @var bool
   */
  public $enable;
  /**
   * Rule visibility can be one of the following: STANDARD - opaque rules.
   * (default) PREMIUM - transparent rules. This field is only supported in
   * Global Security Policies of type CLOUD_ARMOR.
   *
   * @var string
   */
  public $ruleVisibility;
  protected $thresholdConfigsType = SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfigThresholdConfig::class;
  protected $thresholdConfigsDataType = 'array';

  /**
   * If set to true, enables CAAP for L7 DDoS detection. This field is only
   * supported in Global Security Policies of type CLOUD_ARMOR.
   *
   * @param bool $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return bool
   */
  public function getEnable()
  {
    return $this->enable;
  }
  /**
   * Rule visibility can be one of the following: STANDARD - opaque rules.
   * (default) PREMIUM - transparent rules. This field is only supported in
   * Global Security Policies of type CLOUD_ARMOR.
   *
   * Accepted values: PREMIUM, STANDARD
   *
   * @param self::RULE_VISIBILITY_* $ruleVisibility
   */
  public function setRuleVisibility($ruleVisibility)
  {
    $this->ruleVisibility = $ruleVisibility;
  }
  /**
   * @return self::RULE_VISIBILITY_*
   */
  public function getRuleVisibility()
  {
    return $this->ruleVisibility;
  }
  /**
   * Configuration options for layer7 adaptive protection for various
   * customizable thresholds.
   *
   * @param SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfigThresholdConfig[] $thresholdConfigs
   */
  public function setThresholdConfigs($thresholdConfigs)
  {
    $this->thresholdConfigs = $thresholdConfigs;
  }
  /**
   * @return SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfigThresholdConfig[]
   */
  public function getThresholdConfigs()
  {
    return $this->thresholdConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfig::class, 'Google_Service_Compute_SecurityPolicyAdaptiveProtectionConfigLayer7DdosDefenseConfig');
