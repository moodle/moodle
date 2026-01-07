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

class SecurityPolicyRule extends \Google\Model
{
  /**
   * The Action to perform when the rule is matched. The following are the valid
   * actions:        - allow: allow access to target.    - deny(STATUS): deny
   * access to target, returns the    HTTP response code specified. Valid values
   * for `STATUS`    are 403, 404, and 502.    - rate_based_ban: limit client
   * traffic to the configured    threshold and ban the client if the traffic
   * exceeds the threshold.    Configure parameters for this action in
   * RateLimitOptions. Requires    rate_limit_options to be set.    - redirect:
   * redirect to a different target. This can    either be an internal reCAPTCHA
   * redirect, or an external URL-based    redirect via a 302 response.
   * Parameters for this action can be configured    via redirectOptions. This
   * action is only supported in Global Security    Policies of type
   * CLOUD_ARMOR.    - throttle: limit    client traffic to the configured
   * threshold. Configure parameters for this    action in rateLimitOptions.
   * Requires rate_limit_options to be set for    this.    - fairshare (preview
   * only): when traffic reaches the    threshold limit, requests from the
   * clients matching this rule begin to be    rate-limited using the Fair Share
   * algorithm. This action is only allowed    in security policies of type
   * `CLOUD_ARMOR_INTERNAL_SERVICE`.
   *
   * @var string
   */
  public $action;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  protected $headerActionType = SecurityPolicyRuleHttpHeaderAction::class;
  protected $headerActionDataType = '';
  /**
   * Output only. [Output only] Type of the resource.
   * Alwayscompute#securityPolicyRule for security policy rules
   *
   * @var string
   */
  public $kind;
  protected $matchType = SecurityPolicyRuleMatcher::class;
  protected $matchDataType = '';
  protected $networkMatchType = SecurityPolicyRuleNetworkMatcher::class;
  protected $networkMatchDataType = '';
  protected $preconfiguredWafConfigType = SecurityPolicyRulePreconfiguredWafConfig::class;
  protected $preconfiguredWafConfigDataType = '';
  /**
   * If set to true, the specified action is not enforced.
   *
   * @var bool
   */
  public $preview;
  /**
   * An integer indicating the priority of a rule in the list. The priority must
   * be a positive value between 0 and 2147483647. Rules are evaluated from
   * highest to lowest priority where 0 is the highest priority and 2147483647
   * is the lowest priority.
   *
   * @var int
   */
  public $priority;
  protected $rateLimitOptionsType = SecurityPolicyRuleRateLimitOptions::class;
  protected $rateLimitOptionsDataType = '';
  protected $redirectOptionsType = SecurityPolicyRuleRedirectOptions::class;
  protected $redirectOptionsDataType = '';

  /**
   * The Action to perform when the rule is matched. The following are the valid
   * actions:        - allow: allow access to target.    - deny(STATUS): deny
   * access to target, returns the    HTTP response code specified. Valid values
   * for `STATUS`    are 403, 404, and 502.    - rate_based_ban: limit client
   * traffic to the configured    threshold and ban the client if the traffic
   * exceeds the threshold.    Configure parameters for this action in
   * RateLimitOptions. Requires    rate_limit_options to be set.    - redirect:
   * redirect to a different target. This can    either be an internal reCAPTCHA
   * redirect, or an external URL-based    redirect via a 302 response.
   * Parameters for this action can be configured    via redirectOptions. This
   * action is only supported in Global Security    Policies of type
   * CLOUD_ARMOR.    - throttle: limit    client traffic to the configured
   * threshold. Configure parameters for this    action in rateLimitOptions.
   * Requires rate_limit_options to be set for    this.    - fairshare (preview
   * only): when traffic reaches the    threshold limit, requests from the
   * clients matching this rule begin to be    rate-limited using the Fair Share
   * algorithm. This action is only allowed    in security policies of type
   * `CLOUD_ARMOR_INTERNAL_SERVICE`.
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
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * Optional, additional actions that are performed on headers. This field is
   * only supported in Global Security Policies of type CLOUD_ARMOR.
   *
   * @param SecurityPolicyRuleHttpHeaderAction $headerAction
   */
  public function setHeaderAction(SecurityPolicyRuleHttpHeaderAction $headerAction)
  {
    $this->headerAction = $headerAction;
  }
  /**
   * @return SecurityPolicyRuleHttpHeaderAction
   */
  public function getHeaderAction()
  {
    return $this->headerAction;
  }
  /**
   * Output only. [Output only] Type of the resource.
   * Alwayscompute#securityPolicyRule for security policy rules
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
   * @param SecurityPolicyRuleMatcher $match
   */
  public function setMatch(SecurityPolicyRuleMatcher $match)
  {
    $this->match = $match;
  }
  /**
   * @return SecurityPolicyRuleMatcher
   */
  public function getMatch()
  {
    return $this->match;
  }
  /**
   * A match condition that incoming packets are evaluated against for
   * CLOUD_ARMOR_NETWORK security policies. If it matches, the corresponding
   * 'action' is enforced.
   *
   * The match criteria for a rule consists of built-in match fields (like
   * 'srcIpRanges') and potentially multiple user-defined match fields
   * ('userDefinedFields').
   *
   * Field values may be extracted directly from the packet or derived from it
   * (e.g. 'srcRegionCodes'). Some fields may not be present in every packet
   * (e.g. 'srcPorts'). A user-defined field is only present if the base header
   * is found in the packet and the entire field is in bounds.
   *
   * Each match field may specify which values can match it, listing one or more
   * ranges, prefixes, or exact values that are considered a match for the
   * field. A field value must be present in order to match a specified match
   * field. If no match values are specified for a match field, then any field
   * value is considered to match it, and it's not required to be present. For
   * strings specifying '*' is also equivalent to match all.
   *
   * For a packet to match a rule, all specified match fields must match the
   * corresponding field values derived from the packet.
   *
   * Example:
   *
   * networkMatch:   srcIpRanges:   - "192.0.2.0/24"   - "198.51.100.0/24"
   * userDefinedFields:   - name: "ipv4_fragment_offset"     values:     -
   * "1-0x1fff"
   *
   * The above match condition matches packets with a source IP in 192.0.2.0/24
   * or 198.51.100.0/24 and a user-defined field named "ipv4_fragment_offset"
   * with a value between 1 and 0x1fff inclusive.
   *
   * @param SecurityPolicyRuleNetworkMatcher $networkMatch
   */
  public function setNetworkMatch(SecurityPolicyRuleNetworkMatcher $networkMatch)
  {
    $this->networkMatch = $networkMatch;
  }
  /**
   * @return SecurityPolicyRuleNetworkMatcher
   */
  public function getNetworkMatch()
  {
    return $this->networkMatch;
  }
  /**
   * Preconfigured WAF configuration to be applied for the rule. If the rule
   * does not evaluate preconfigured WAF rules, i.e., if
   * evaluatePreconfiguredWaf() is not used, this field will have no effect.
   *
   * @param SecurityPolicyRulePreconfiguredWafConfig $preconfiguredWafConfig
   */
  public function setPreconfiguredWafConfig(SecurityPolicyRulePreconfiguredWafConfig $preconfiguredWafConfig)
  {
    $this->preconfiguredWafConfig = $preconfiguredWafConfig;
  }
  /**
   * @return SecurityPolicyRulePreconfiguredWafConfig
   */
  public function getPreconfiguredWafConfig()
  {
    return $this->preconfiguredWafConfig;
  }
  /**
   * If set to true, the specified action is not enforced.
   *
   * @param bool $preview
   */
  public function setPreview($preview)
  {
    $this->preview = $preview;
  }
  /**
   * @return bool
   */
  public function getPreview()
  {
    return $this->preview;
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
   * Must be specified if the action is "rate_based_ban" or "throttle" or
   * "fairshare". Cannot be specified for any other actions.
   *
   * @param SecurityPolicyRuleRateLimitOptions $rateLimitOptions
   */
  public function setRateLimitOptions(SecurityPolicyRuleRateLimitOptions $rateLimitOptions)
  {
    $this->rateLimitOptions = $rateLimitOptions;
  }
  /**
   * @return SecurityPolicyRuleRateLimitOptions
   */
  public function getRateLimitOptions()
  {
    return $this->rateLimitOptions;
  }
  /**
   * Parameters defining the redirect action. Cannot be specified for any other
   * actions. This field is only supported in Global Security Policies of type
   * CLOUD_ARMOR.
   *
   * @param SecurityPolicyRuleRedirectOptions $redirectOptions
   */
  public function setRedirectOptions(SecurityPolicyRuleRedirectOptions $redirectOptions)
  {
    $this->redirectOptions = $redirectOptions;
  }
  /**
   * @return SecurityPolicyRuleRedirectOptions
   */
  public function getRedirectOptions()
  {
    return $this->redirectOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRule::class, 'Google_Service_Compute_SecurityPolicyRule');
