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

namespace Google\Service\Appengine;

class FirewallRule extends \Google\Model
{
  public const ACTION_UNSPECIFIED_ACTION = 'UNSPECIFIED_ACTION';
  /**
   * Matching requests are allowed.
   */
  public const ACTION_ALLOW = 'ALLOW';
  /**
   * Matching requests are denied.
   */
  public const ACTION_DENY = 'DENY';
  /**
   * The action to take on matched requests.
   *
   * @var string
   */
  public $action;
  /**
   * An optional string description of this rule. This field has a maximum
   * length of 400 characters.
   *
   * @var string
   */
  public $description;
  /**
   * @var int
   */
  public $priority;
  /**
   * IP address or range, defined using CIDR notation, of requests that this
   * rule applies to. You can use the wildcard character "*" to match all IPs
   * equivalent to "0/0" and "::/0" together. Examples: 192.168.1.1 or
   * 192.168.0.0/16 or 2001:db8::/32 or 2001:0db8:0000:0042:0000:8a2e:0370:7334.
   * Truncation will be silently performed on addresses which are not properly
   * truncated. For example, 1.2.3.4/24 is accepted as the same address as
   * 1.2.3.0/24. Similarly, for IPv6, 2001:db8::1/32 is accepted as the same
   * address as 2001:db8::/32.
   *
   * @var string
   */
  public $sourceRange;

  /**
   * The action to take on matched requests.
   *
   * Accepted values: UNSPECIFIED_ACTION, ALLOW, DENY
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * An optional string description of this rule. This field has a maximum
   * length of 400 characters.
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
   * IP address or range, defined using CIDR notation, of requests that this
   * rule applies to. You can use the wildcard character "*" to match all IPs
   * equivalent to "0/0" and "::/0" together. Examples: 192.168.1.1 or
   * 192.168.0.0/16 or 2001:db8::/32 or 2001:0db8:0000:0042:0000:8a2e:0370:7334.
   * Truncation will be silently performed on addresses which are not properly
   * truncated. For example, 1.2.3.4/24 is accepted as the same address as
   * 1.2.3.0/24. Similarly, for IPv6, 2001:db8::1/32 is accepted as the same
   * address as 2001:db8::/32.
   *
   * @param string $sourceRange
   */
  public function setSourceRange($sourceRange)
  {
    $this->sourceRange = $sourceRange;
  }
  /**
   * @return string
   */
  public function getSourceRange()
  {
    return $this->sourceRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallRule::class, 'Google_Service_Appengine_FirewallRule');
