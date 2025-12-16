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

class RouterNatRule extends \Google\Model
{
  protected $actionType = RouterNatRuleAction::class;
  protected $actionDataType = '';
  /**
   * An optional description of this rule.
   *
   * @var string
   */
  public $description;
  /**
   * CEL expression that specifies the match condition that egress traffic from
   * a VM is evaluated against. If it evaluates to true, the corresponding
   * `action` is enforced.
   *
   * The following examples are valid match expressions for public NAT:
   *
   * `inIpRange(destination.ip, '1.1.0.0/16') || inIpRange(destination.ip,
   * '2.2.0.0/16')`
   *
   * `destination.ip == '1.1.0.1' || destination.ip == '8.8.8.8'`
   *
   * The following example is a valid match expression for private NAT:
   *
   * `nexthop.hub == '//networkconnectivity.googleapis.com/projects/my-
   * project/locations/global/hubs/hub-1'`
   *
   * @var string
   */
  public $match;
  /**
   * An integer uniquely identifying a rule in the list. The rule number must be
   * a positive value between 0 and 65000, and must be unique among rules within
   * a NAT.
   *
   * @var string
   */
  public $ruleNumber;

  /**
   * The action to be enforced for traffic that matches this rule.
   *
   * @param RouterNatRuleAction $action
   */
  public function setAction(RouterNatRuleAction $action)
  {
    $this->action = $action;
  }
  /**
   * @return RouterNatRuleAction
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * An optional description of this rule.
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
   * CEL expression that specifies the match condition that egress traffic from
   * a VM is evaluated against. If it evaluates to true, the corresponding
   * `action` is enforced.
   *
   * The following examples are valid match expressions for public NAT:
   *
   * `inIpRange(destination.ip, '1.1.0.0/16') || inIpRange(destination.ip,
   * '2.2.0.0/16')`
   *
   * `destination.ip == '1.1.0.1' || destination.ip == '8.8.8.8'`
   *
   * The following example is a valid match expression for private NAT:
   *
   * `nexthop.hub == '//networkconnectivity.googleapis.com/projects/my-
   * project/locations/global/hubs/hub-1'`
   *
   * @param string $match
   */
  public function setMatch($match)
  {
    $this->match = $match;
  }
  /**
   * @return string
   */
  public function getMatch()
  {
    return $this->match;
  }
  /**
   * An integer uniquely identifying a rule in the list. The rule number must be
   * a positive value between 0 and 65000, and must be unique among rules within
   * a NAT.
   *
   * @param string $ruleNumber
   */
  public function setRuleNumber($ruleNumber)
  {
    $this->ruleNumber = $ruleNumber;
  }
  /**
   * @return string
   */
  public function getRuleNumber()
  {
    return $this->ruleNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterNatRule::class, 'Google_Service_Compute_RouterNatRule');
