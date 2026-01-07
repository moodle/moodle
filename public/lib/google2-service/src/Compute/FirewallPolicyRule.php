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

class FirewallPolicyRule extends \Google\Collection
{
  public const DIRECTION_EGRESS = 'EGRESS';
  public const DIRECTION_INGRESS = 'INGRESS';
  protected $collection_key = 'targetServiceAccounts';
  /**
   * The Action to perform when the client connection triggers the rule. Valid
   * actions for firewall rules are: "allow", "deny",
   * "apply_security_profile_group" and "goto_next". Valid actions for packet
   * mirroring rules are: "mirror", "do_not_mirror" and "goto_next".
   *
   * @var string
   */
  public $action;
  /**
   * An optional description for this resource.
   *
   * @var string
   */
  public $description;
  /**
   * The direction in which this rule applies.
   *
   * @var string
   */
  public $direction;
  /**
   * Denotes whether the firewall policy rule is disabled. When set to true, the
   * firewall policy rule is not enforced and traffic behaves as if it did not
   * exist. If this is unspecified, the firewall policy rule will be enabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Denotes whether to enable logging for a particular rule. If logging is
   * enabled, logs will be exported to the configured export destination in
   * Stackdriver. Logs may be exported to BigQuery or Pub/Sub. Note: you cannot
   * enable logging on "goto_next" rules.
   *
   * @var bool
   */
  public $enableLogging;
  /**
   * Output only. [Output only] Type of the resource.
   * Returnscompute#firewallPolicyRule for firewall rules
   * andcompute#packetMirroringRule for packet mirroring rules.
   *
   * @var string
   */
  public $kind;
  protected $matchType = FirewallPolicyRuleMatcher::class;
  protected $matchDataType = '';
  /**
   * An integer indicating the priority of a rule in the list. The priority must
   * be a positive value between 0 and 2147483647. Rules are evaluated from
   * highest to lowest priority where 0 is the highest priority and 2147483647
   * is the lowest priority.
   *
   * @var int
   */
  public $priority;
  /**
   * An optional name for the rule. This field is not a unique identifier and
   * can be updated.
   *
   * @var string
   */
  public $ruleName;
  /**
   * Output only. [Output Only] Calculation of the complexity of a single
   * firewall policy rule.
   *
   * @var int
   */
  public $ruleTupleCount;
  /**
   * A fully-qualified URL of a SecurityProfile resource instance. Example: http
   * s://networksecurity.googleapis.com/v1/projects/{project}/locations/{locatio
   * n}/securityProfileGroups/my-security-profile-group Must be specified if
   * action is one of 'apply_security_profile_group' or 'mirror'. Cannot be
   * specified for other actions.
   *
   * @var string
   */
  public $securityProfileGroup;
  /**
   * A list of network resource URLs to which this rule applies.  This field
   * allows you to control which network's VMs get this rule.  If this field is
   * left blank, all VMs within the organization will receive the rule.
   *
   * @var string[]
   */
  public $targetResources;
  protected $targetSecureTagsType = FirewallPolicyRuleSecureTag::class;
  protected $targetSecureTagsDataType = 'array';
  /**
   * A list of service accounts indicating the sets of instances that are
   * applied with this rule.
   *
   * @var string[]
   */
  public $targetServiceAccounts;
  /**
   * Boolean flag indicating if the traffic should be TLS decrypted. Can be set
   * only if action = 'apply_security_profile_group' and cannot be set for other
   * actions.
   *
   * @var bool
   */
  public $tlsInspect;

  /**
   * The Action to perform when the client connection triggers the rule. Valid
   * actions for firewall rules are: "allow", "deny",
   * "apply_security_profile_group" and "goto_next". Valid actions for packet
   * mirroring rules are: "mirror", "do_not_mirror" and "goto_next".
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
   * An optional description for this resource.
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
   * The direction in which this rule applies.
   *
   * Accepted values: EGRESS, INGRESS
   *
   * @param self::DIRECTION_* $direction
   */
  public function setDirection($direction)
  {
    $this->direction = $direction;
  }
  /**
   * @return self::DIRECTION_*
   */
  public function getDirection()
  {
    return $this->direction;
  }
  /**
   * Denotes whether the firewall policy rule is disabled. When set to true, the
   * firewall policy rule is not enforced and traffic behaves as if it did not
   * exist. If this is unspecified, the firewall policy rule will be enabled.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Denotes whether to enable logging for a particular rule. If logging is
   * enabled, logs will be exported to the configured export destination in
   * Stackdriver. Logs may be exported to BigQuery or Pub/Sub. Note: you cannot
   * enable logging on "goto_next" rules.
   *
   * @param bool $enableLogging
   */
  public function setEnableLogging($enableLogging)
  {
    $this->enableLogging = $enableLogging;
  }
  /**
   * @return bool
   */
  public function getEnableLogging()
  {
    return $this->enableLogging;
  }
  /**
   * Output only. [Output only] Type of the resource.
   * Returnscompute#firewallPolicyRule for firewall rules
   * andcompute#packetMirroringRule for packet mirroring rules.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A match condition that incoming traffic is evaluated against. If it
   * evaluates to true, the corresponding 'action' is enforced.
   *
   * @param FirewallPolicyRuleMatcher $match
   */
  public function setMatch(FirewallPolicyRuleMatcher $match)
  {
    $this->match = $match;
  }
  /**
   * @return FirewallPolicyRuleMatcher
   */
  public function getMatch()
  {
    return $this->match;
  }
  /**
   * An integer indicating the priority of a rule in the list. The priority must
   * be a positive value between 0 and 2147483647. Rules are evaluated from
   * highest to lowest priority where 0 is the highest priority and 2147483647
   * is the lowest priority.
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
   * An optional name for the rule. This field is not a unique identifier and
   * can be updated.
   *
   * @param string $ruleName
   */
  public function setRuleName($ruleName)
  {
    $this->ruleName = $ruleName;
  }
  /**
   * @return string
   */
  public function getRuleName()
  {
    return $this->ruleName;
  }
  /**
   * Output only. [Output Only] Calculation of the complexity of a single
   * firewall policy rule.
   *
   * @param int $ruleTupleCount
   */
  public function setRuleTupleCount($ruleTupleCount)
  {
    $this->ruleTupleCount = $ruleTupleCount;
  }
  /**
   * @return int
   */
  public function getRuleTupleCount()
  {
    return $this->ruleTupleCount;
  }
  /**
   * A fully-qualified URL of a SecurityProfile resource instance. Example: http
   * s://networksecurity.googleapis.com/v1/projects/{project}/locations/{locatio
   * n}/securityProfileGroups/my-security-profile-group Must be specified if
   * action is one of 'apply_security_profile_group' or 'mirror'. Cannot be
   * specified for other actions.
   *
   * @param string $securityProfileGroup
   */
  public function setSecurityProfileGroup($securityProfileGroup)
  {
    $this->securityProfileGroup = $securityProfileGroup;
  }
  /**
   * @return string
   */
  public function getSecurityProfileGroup()
  {
    return $this->securityProfileGroup;
  }
  /**
   * A list of network resource URLs to which this rule applies.  This field
   * allows you to control which network's VMs get this rule.  If this field is
   * left blank, all VMs within the organization will receive the rule.
   *
   * @param string[] $targetResources
   */
  public function setTargetResources($targetResources)
  {
    $this->targetResources = $targetResources;
  }
  /**
   * @return string[]
   */
  public function getTargetResources()
  {
    return $this->targetResources;
  }
  /**
   * A list of secure tags that controls which instances the firewall rule
   * applies to. If targetSecureTag are specified, then the firewall rule
   * applies only to instances in the VPC network that have one of those
   * EFFECTIVE secure tags, if all the target_secure_tag are in INEFFECTIVE
   * state, then this rule will be ignored.targetSecureTag may not be set at the
   * same time astargetServiceAccounts. If neither targetServiceAccounts
   * nortargetSecureTag are specified, the firewall rule applies to all
   * instances on the specified network. Maximum number of target label tags
   * allowed is 256.
   *
   * @param FirewallPolicyRuleSecureTag[] $targetSecureTags
   */
  public function setTargetSecureTags($targetSecureTags)
  {
    $this->targetSecureTags = $targetSecureTags;
  }
  /**
   * @return FirewallPolicyRuleSecureTag[]
   */
  public function getTargetSecureTags()
  {
    return $this->targetSecureTags;
  }
  /**
   * A list of service accounts indicating the sets of instances that are
   * applied with this rule.
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
   * Boolean flag indicating if the traffic should be TLS decrypted. Can be set
   * only if action = 'apply_security_profile_group' and cannot be set for other
   * actions.
   *
   * @param bool $tlsInspect
   */
  public function setTlsInspect($tlsInspect)
  {
    $this->tlsInspect = $tlsInspect;
  }
  /**
   * @return bool
   */
  public function getTlsInspect()
  {
    return $this->tlsInspect;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallPolicyRule::class, 'Google_Service_Compute_FirewallPolicyRule');
