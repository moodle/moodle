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

namespace Google\Service\Dns;

class ResponsePolicyRule extends \Google\Model
{
  public const BEHAVIOR_behaviorUnspecified = 'behaviorUnspecified';
  /**
   * Skip a less-specific Response Policy Rule and let the query logic continue.
   * This mechanism, when used with wildcard selectors, lets you exempt specific
   * subdomains from a broader Response Policy Rule and direct the queries to
   * the public internet instead. For example, if the following rules exist: ```
   * *.example.com -> LocalData 1.2.3.4 foo.example.com -> Behavior
   * 'passthrough' ``` A query for foo.example.com skips the wildcard rule. This
   * functionality also facilitates allowlisting. Response Policy Zones (RPZs)
   * can be applied at multiple levels within the hierarchy: for example, an
   * organization, a folder, a project, or a VPC network. If an RPZ rule is
   * applied at a higher level, adding a `passthrough` rule at a lower level
   * will override it. Queries from affected virtual machines (VMs) to that
   * domain bypass the RPZ and proceed with normal resolution.
   */
  public const BEHAVIOR_bypassResponsePolicy = 'bypassResponsePolicy';
  /**
   * Answer this query with a behavior rather than DNS data.
   *
   * @var string
   */
  public $behavior;
  /**
   * The DNS name (wildcard or exact) to apply this rule to. Must be unique
   * within the Response Policy Rule.
   *
   * @var string
   */
  public $dnsName;
  /**
   * @var string
   */
  public $kind;
  protected $localDataType = ResponsePolicyRuleLocalData::class;
  protected $localDataDataType = '';
  /**
   * An identifier for this rule. Must be unique with the ResponsePolicy.
   *
   * @var string
   */
  public $ruleName;

  /**
   * Answer this query with a behavior rather than DNS data.
   *
   * Accepted values: behaviorUnspecified, bypassResponsePolicy
   *
   * @param self::BEHAVIOR_* $behavior
   */
  public function setBehavior($behavior)
  {
    $this->behavior = $behavior;
  }
  /**
   * @return self::BEHAVIOR_*
   */
  public function getBehavior()
  {
    return $this->behavior;
  }
  /**
   * The DNS name (wildcard or exact) to apply this rule to. Must be unique
   * within the Response Policy Rule.
   *
   * @param string $dnsName
   */
  public function setDnsName($dnsName)
  {
    $this->dnsName = $dnsName;
  }
  /**
   * @return string
   */
  public function getDnsName()
  {
    return $this->dnsName;
  }
  /**
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
   * Answer this query directly with DNS data. These ResourceRecordSets override
   * any other DNS behavior for the matched name; in particular they override
   * private zones, the public internet, and GCP internal DNS. No SOA nor NS
   * types are allowed.
   *
   * @param ResponsePolicyRuleLocalData $localData
   */
  public function setLocalData(ResponsePolicyRuleLocalData $localData)
  {
    $this->localData = $localData;
  }
  /**
   * @return ResponsePolicyRuleLocalData
   */
  public function getLocalData()
  {
    return $this->localData;
  }
  /**
   * An identifier for this rule. Must be unique with the ResponsePolicy.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResponsePolicyRule::class, 'Google_Service_Dns_ResponsePolicyRule');
