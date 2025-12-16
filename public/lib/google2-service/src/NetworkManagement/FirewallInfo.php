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

namespace Google\Service\NetworkManagement;

class FirewallInfo extends \Google\Collection
{
  /**
   * Unspecified type.
   */
  public const FIREWALL_RULE_TYPE_FIREWALL_RULE_TYPE_UNSPECIFIED = 'FIREWALL_RULE_TYPE_UNSPECIFIED';
  /**
   * Hierarchical firewall policy rule. For details, see [Hierarchical firewall
   * policies overview](https://cloud.google.com/vpc/docs/firewall-policies).
   */
  public const FIREWALL_RULE_TYPE_HIERARCHICAL_FIREWALL_POLICY_RULE = 'HIERARCHICAL_FIREWALL_POLICY_RULE';
  /**
   * VPC firewall rule. For details, see [VPC firewall rules
   * overview](https://cloud.google.com/vpc/docs/firewalls).
   */
  public const FIREWALL_RULE_TYPE_VPC_FIREWALL_RULE = 'VPC_FIREWALL_RULE';
  /**
   * Implied VPC firewall rule. For details, see [Implied
   * rules](https://cloud.google.com/vpc/docs/firewalls#default_firewall_rules).
   */
  public const FIREWALL_RULE_TYPE_IMPLIED_VPC_FIREWALL_RULE = 'IMPLIED_VPC_FIREWALL_RULE';
  /**
   * Implicit firewall rules that are managed by serverless VPC access to allow
   * ingress access. They are not visible in the Google Cloud console. For
   * details, see [VPC connector's implicit
   * rules](https://cloud.google.com/functions/docs/networking/connecting-
   * vpc#restrict-access).
   */
  public const FIREWALL_RULE_TYPE_SERVERLESS_VPC_ACCESS_MANAGED_FIREWALL_RULE = 'SERVERLESS_VPC_ACCESS_MANAGED_FIREWALL_RULE';
  /**
   * User-defined global network firewall policy rule. For details, see [Network
   * firewall policies](https://cloud.google.com/vpc/docs/network-firewall-
   * policies).
   */
  public const FIREWALL_RULE_TYPE_NETWORK_FIREWALL_POLICY_RULE = 'NETWORK_FIREWALL_POLICY_RULE';
  /**
   * User-defined regional network firewall policy rule. For details, see
   * [Regional network firewall
   * policies](https://cloud.google.com/firewall/docs/regional-firewall-
   * policies).
   */
  public const FIREWALL_RULE_TYPE_NETWORK_REGIONAL_FIREWALL_POLICY_RULE = 'NETWORK_REGIONAL_FIREWALL_POLICY_RULE';
  /**
   * System-defined global network firewall policy rule.
   */
  public const FIREWALL_RULE_TYPE_SYSTEM_NETWORK_FIREWALL_POLICY_RULE = 'SYSTEM_NETWORK_FIREWALL_POLICY_RULE';
  /**
   * System-defined regional network firewall policy rule.
   */
  public const FIREWALL_RULE_TYPE_SYSTEM_REGIONAL_NETWORK_FIREWALL_POLICY_RULE = 'SYSTEM_REGIONAL_NETWORK_FIREWALL_POLICY_RULE';
  /**
   * Firewall policy rule containing attributes not yet supported in
   * Connectivity tests. Firewall analysis is skipped if such a rule can
   * potentially be matched. Please see the [list of unsupported
   * configurations](https://cloud.google.com/network-intelligence-
   * center/docs/connectivity-tests/concepts/overview#unsupported-configs).
   */
  public const FIREWALL_RULE_TYPE_UNSUPPORTED_FIREWALL_POLICY_RULE = 'UNSUPPORTED_FIREWALL_POLICY_RULE';
  /**
   * Tracking state for response traffic created when request traffic goes
   * through allow firewall rule. For details, see [firewall rules specification
   * s](https://cloud.google.com/firewall/docs/firewalls#specifications)
   */
  public const FIREWALL_RULE_TYPE_TRACKING_STATE = 'TRACKING_STATE';
  /**
   * Firewall analysis was skipped due to executing Connectivity Test in the
   * BypassFirewallChecks mode
   */
  public const FIREWALL_RULE_TYPE_ANALYSIS_SKIPPED = 'ANALYSIS_SKIPPED';
  /**
   * Target type is not specified. In this case we treat the rule as applying to
   * INSTANCES target type.
   */
  public const TARGET_TYPE_TARGET_TYPE_UNSPECIFIED = 'TARGET_TYPE_UNSPECIFIED';
  /**
   * Firewall rule applies to instances.
   */
  public const TARGET_TYPE_INSTANCES = 'INSTANCES';
  /**
   * Firewall rule applies to internal managed load balancers.
   */
  public const TARGET_TYPE_INTERNAL_MANAGED_LB = 'INTERNAL_MANAGED_LB';
  protected $collection_key = 'targetTags';
  /**
   * Possible values: ALLOW, DENY, APPLY_SECURITY_PROFILE_GROUP
   *
   * @var string
   */
  public $action;
  /**
   * Possible values: INGRESS, EGRESS
   *
   * @var string
   */
  public $direction;
  /**
   * The display name of the firewall rule. This field might be empty for
   * firewall policy rules.
   *
   * @var string
   */
  public $displayName;
  /**
   * The firewall rule's type.
   *
   * @var string
   */
  public $firewallRuleType;
  /**
   * The URI of the VPC network that the firewall rule is associated with. This
   * field is not applicable to hierarchical firewall policy rules.
   *
   * @var string
   */
  public $networkUri;
  /**
   * The name of the firewall policy that this rule is associated with. This
   * field is not applicable to VPC firewall rules and implied VPC firewall
   * rules.
   *
   * @var string
   */
  public $policy;
  /**
   * The priority of the firewall policy that this rule is associated with. This
   * field is not applicable to VPC firewall rules and implied VPC firewall
   * rules.
   *
   * @var int
   */
  public $policyPriority;
  /**
   * The URI of the firewall policy that this rule is associated with. This
   * field is not applicable to VPC firewall rules and implied VPC firewall
   * rules.
   *
   * @var string
   */
  public $policyUri;
  /**
   * The priority of the firewall rule.
   *
   * @var int
   */
  public $priority;
  /**
   * The target service accounts specified by the firewall rule.
   *
   * @var string[]
   */
  public $targetServiceAccounts;
  /**
   * The target tags defined by the VPC firewall rule. This field is not
   * applicable to firewall policy rules.
   *
   * @var string[]
   */
  public $targetTags;
  /**
   * Target type of the firewall rule.
   *
   * @var string
   */
  public $targetType;
  /**
   * The URI of the firewall rule. This field is not applicable to implied VPC
   * firewall rules.
   *
   * @var string
   */
  public $uri;

  /**
   * Possible values: ALLOW, DENY, APPLY_SECURITY_PROFILE_GROUP
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Possible values: INGRESS, EGRESS
   *
   * @param string $direction
   */
  public function setDirection($direction)
  {
    $this->direction = $direction;
  }
  /**
   * @return string
   */
  public function getDirection()
  {
    return $this->direction;
  }
  /**
   * The display name of the firewall rule. This field might be empty for
   * firewall policy rules.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The firewall rule's type.
   *
   * Accepted values: FIREWALL_RULE_TYPE_UNSPECIFIED,
   * HIERARCHICAL_FIREWALL_POLICY_RULE, VPC_FIREWALL_RULE,
   * IMPLIED_VPC_FIREWALL_RULE, SERVERLESS_VPC_ACCESS_MANAGED_FIREWALL_RULE,
   * NETWORK_FIREWALL_POLICY_RULE, NETWORK_REGIONAL_FIREWALL_POLICY_RULE,
   * SYSTEM_NETWORK_FIREWALL_POLICY_RULE,
   * SYSTEM_REGIONAL_NETWORK_FIREWALL_POLICY_RULE,
   * UNSUPPORTED_FIREWALL_POLICY_RULE, TRACKING_STATE, ANALYSIS_SKIPPED
   *
   * @param self::FIREWALL_RULE_TYPE_* $firewallRuleType
   */
  public function setFirewallRuleType($firewallRuleType)
  {
    $this->firewallRuleType = $firewallRuleType;
  }
  /**
   * @return self::FIREWALL_RULE_TYPE_*
   */
  public function getFirewallRuleType()
  {
    return $this->firewallRuleType;
  }
  /**
   * The URI of the VPC network that the firewall rule is associated with. This
   * field is not applicable to hierarchical firewall policy rules.
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * The name of the firewall policy that this rule is associated with. This
   * field is not applicable to VPC firewall rules and implied VPC firewall
   * rules.
   *
   * @param string $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return string
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * The priority of the firewall policy that this rule is associated with. This
   * field is not applicable to VPC firewall rules and implied VPC firewall
   * rules.
   *
   * @param int $policyPriority
   */
  public function setPolicyPriority($policyPriority)
  {
    $this->policyPriority = $policyPriority;
  }
  /**
   * @return int
   */
  public function getPolicyPriority()
  {
    return $this->policyPriority;
  }
  /**
   * The URI of the firewall policy that this rule is associated with. This
   * field is not applicable to VPC firewall rules and implied VPC firewall
   * rules.
   *
   * @param string $policyUri
   */
  public function setPolicyUri($policyUri)
  {
    $this->policyUri = $policyUri;
  }
  /**
   * @return string
   */
  public function getPolicyUri()
  {
    return $this->policyUri;
  }
  /**
   * The priority of the firewall rule.
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
   * The target service accounts specified by the firewall rule.
   *
   * @param string[] $targetServiceAccounts
   */
  public function setTargetServiceAccounts($targetServiceAccounts)
  {
    $this->targetServiceAccounts = $targetServiceAccounts;
  }
  /**
   * @return string[]
   */
  public function getTargetServiceAccounts()
  {
    return $this->targetServiceAccounts;
  }
  /**
   * The target tags defined by the VPC firewall rule. This field is not
   * applicable to firewall policy rules.
   *
   * @param string[] $targetTags
   */
  public function setTargetTags($targetTags)
  {
    $this->targetTags = $targetTags;
  }
  /**
   * @return string[]
   */
  public function getTargetTags()
  {
    return $this->targetTags;
  }
  /**
   * Target type of the firewall rule.
   *
   * Accepted values: TARGET_TYPE_UNSPECIFIED, INSTANCES, INTERNAL_MANAGED_LB
   *
   * @param self::TARGET_TYPE_* $targetType
   */
  public function setTargetType($targetType)
  {
    $this->targetType = $targetType;
  }
  /**
   * @return self::TARGET_TYPE_*
   */
  public function getTargetType()
  {
    return $this->targetType;
  }
  /**
   * The URI of the firewall rule. This field is not applicable to implied VPC
   * firewall rules.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallInfo::class, 'Google_Service_NetworkManagement_FirewallInfo');
