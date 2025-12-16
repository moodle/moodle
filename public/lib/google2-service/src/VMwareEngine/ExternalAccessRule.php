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

namespace Google\Service\VMwareEngine;

class ExternalAccessRule extends \Google\Collection
{
  /**
   * Defaults to allow.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Allows connections that match the other specified components.
   */
  public const ACTION_ALLOW = 'ALLOW';
  /**
   * Blocks connections that match the other specified components.
   */
  public const ACTION_DENY = 'DENY';
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The rule is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The rule is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The rule is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The rule is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  protected $collection_key = 'sourcePorts';
  /**
   * The action that the external access rule performs.
   *
   * @var string
   */
  public $action;
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * User-provided description for this external access rule.
   *
   * @var string
   */
  public $description;
  protected $destinationIpRangesType = IpRange::class;
  protected $destinationIpRangesDataType = 'array';
  /**
   * A list of destination ports to which the external access rule applies. This
   * field is only applicable for the UDP or TCP protocol. Each entry must be
   * either an integer or a range. For example: `["22"]`, `["80","443"]`, or
   * `["12345-12349"]`. To match all destination ports, specify `["0-65535"]`.
   *
   * @var string[]
   */
  public $destinationPorts;
  /**
   * The IP protocol to which the external access rule applies. This value can
   * be one of the following three protocol strings (not case-sensitive): `tcp`,
   * `udp`, or `icmp`.
   *
   * @var string
   */
  public $ipProtocol;
  /**
   * Output only. The resource name of this external access rule. Resource names
   * are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-
   * policy/externalAccessRules/my-rule`
   *
   * @var string
   */
  public $name;
  /**
   * External access rule priority, which determines the external access rule to
   * use when multiple rules apply. If multiple rules have the same priority,
   * their ordering is non-deterministic. If specific ordering is required,
   * assign unique priorities to enforce such ordering. The external access rule
   * priority is an integer from 100 to 4096, both inclusive. Lower integers
   * indicate higher precedence. For example, a rule with priority `100` has
   * higher precedence than a rule with priority `101`.
   *
   * @var int
   */
  public $priority;
  protected $sourceIpRangesType = IpRange::class;
  protected $sourceIpRangesDataType = 'array';
  /**
   * A list of source ports to which the external access rule applies. This
   * field is only applicable for the UDP or TCP protocol. Each entry must be
   * either an integer or a range. For example: `["22"]`, `["80","443"]`, or
   * `["12345-12349"]`. To match all source ports, specify `["0-65535"]`.
   *
   * @var string[]
   */
  public $sourcePorts;
  /**
   * Output only. The state of the resource.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The action that the external access rule performs.
   *
   * Accepted values: ACTION_UNSPECIFIED, ALLOW, DENY
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
   * Output only. Creation time of this resource.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * User-provided description for this external access rule.
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
   * If destination ranges are specified, the external access rule applies only
   * to the traffic that has a destination IP address in these ranges. The
   * specified IP addresses must have reserved external IP addresses in the
   * scope of the parent network policy. To match all external IP addresses in
   * the scope of the parent network policy, specify `0.0.0.0/0`. To match a
   * specific external IP address, specify it using the
   * `IpRange.external_address` property.
   *
   * @param IpRange[] $destinationIpRanges
   */
  public function setDestinationIpRanges($destinationIpRanges)
  {
    $this->destinationIpRanges = $destinationIpRanges;
  }
  /**
   * @return IpRange[]
   */
  public function getDestinationIpRanges()
  {
    return $this->destinationIpRanges;
  }
  /**
   * A list of destination ports to which the external access rule applies. This
   * field is only applicable for the UDP or TCP protocol. Each entry must be
   * either an integer or a range. For example: `["22"]`, `["80","443"]`, or
   * `["12345-12349"]`. To match all destination ports, specify `["0-65535"]`.
   *
   * @param string[] $destinationPorts
   */
  public function setDestinationPorts($destinationPorts)
  {
    $this->destinationPorts = $destinationPorts;
  }
  /**
   * @return string[]
   */
  public function getDestinationPorts()
  {
    return $this->destinationPorts;
  }
  /**
   * The IP protocol to which the external access rule applies. This value can
   * be one of the following three protocol strings (not case-sensitive): `tcp`,
   * `udp`, or `icmp`.
   *
   * @param string $ipProtocol
   */
  public function setIpProtocol($ipProtocol)
  {
    $this->ipProtocol = $ipProtocol;
  }
  /**
   * @return string
   */
  public function getIpProtocol()
  {
    return $this->ipProtocol;
  }
  /**
   * Output only. The resource name of this external access rule. Resource names
   * are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-
   * policy/externalAccessRules/my-rule`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * External access rule priority, which determines the external access rule to
   * use when multiple rules apply. If multiple rules have the same priority,
   * their ordering is non-deterministic. If specific ordering is required,
   * assign unique priorities to enforce such ordering. The external access rule
   * priority is an integer from 100 to 4096, both inclusive. Lower integers
   * indicate higher precedence. For example, a rule with priority `100` has
   * higher precedence than a rule with priority `101`.
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
   * If source ranges are specified, the external access rule applies only to
   * traffic that has a source IP address in these ranges. These ranges can
   * either be expressed in the CIDR format or as an IP address. As only inbound
   * rules are supported, `ExternalAddress` resources cannot be the source IP
   * addresses of an external access rule. To match all source addresses,
   * specify `0.0.0.0/0`.
   *
   * @param IpRange[] $sourceIpRanges
   */
  public function setSourceIpRanges($sourceIpRanges)
  {
    $this->sourceIpRanges = $sourceIpRanges;
  }
  /**
   * @return IpRange[]
   */
  public function getSourceIpRanges()
  {
    return $this->sourceIpRanges;
  }
  /**
   * A list of source ports to which the external access rule applies. This
   * field is only applicable for the UDP or TCP protocol. Each entry must be
   * either an integer or a range. For example: `["22"]`, `["80","443"]`, or
   * `["12345-12349"]`. To match all source ports, specify `["0-65535"]`.
   *
   * @param string[] $sourcePorts
   */
  public function setSourcePorts($sourcePorts)
  {
    $this->sourcePorts = $sourcePorts;
  }
  /**
   * @return string[]
   */
  public function getSourcePorts()
  {
    return $this->sourcePorts;
  }
  /**
   * Output only. The state of the resource.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, UPDATING, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Last update time of this resource.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalAccessRule::class, 'Google_Service_VMwareEngine_ExternalAccessRule');
