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

class Firewall extends \Google\Collection
{
  /**
   * Indicates that firewall should apply to outgoing traffic.
   */
  public const DIRECTION_EGRESS = 'EGRESS';
  /**
   * Indicates that firewall should apply to incoming traffic.
   */
  public const DIRECTION_INGRESS = 'INGRESS';
  protected $collection_key = 'targetTags';
  protected $allowedType = FirewallAllowed::class;
  protected $allowedDataType = 'array';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $deniedType = FirewallDenied::class;
  protected $deniedDataType = 'array';
  /**
   * An optional description of this resource. Provide this field when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * If destination ranges are specified, the firewall rule applies only to
   * traffic that has destination IP address in these ranges. These ranges must
   * be expressed inCIDR format. Both IPv4 and IPv6 are supported.
   *
   * @var string[]
   */
  public $destinationRanges;
  /**
   * Direction of traffic to which this firewall applies, either `INGRESS` or
   * `EGRESS`. The default is `INGRESS`. For `EGRESS` traffic, you cannot
   * specify the sourceTags fields.
   *
   * @var string
   */
  public $direction;
  /**
   * Denotes whether the firewall rule is disabled. When set to true, the
   * firewall rule is not enforced and the network behaves as if it did not
   * exist. If this is unspecified, the firewall rule will be enabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#firewall
   * for firewall rules.
   *
   * @var string
   */
  public $kind;
  protected $logConfigType = FirewallLogConfig::class;
  protected $logConfigDataType = '';
  /**
   * Name of the resource; provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?`. The first character must be a
   * lowercase letter, and all following characters (except for the last
   * character) must be a dash, lowercase letter, or digit. The last character
   * must be a lowercase letter or digit.
   *
   * @var string
   */
  public $name;
  /**
   * URL of the network resource for this firewall rule. If not specified when
   * creating a firewall rule, the default network is used:
   *
   * global/networks/default
   *
   * If you choose to specify this field, you can specify the network as a full
   * or partial URL. For example, the following are all valid URLs:         -
   * https://www.googleapis.com/compute/v1/projects/myproject/global/networks/my
   * -network     - projects/myproject/global/networks/my-network     -
   * global/networks/default
   *
   * @var string
   */
  public $network;
  protected $paramsType = FirewallParams::class;
  protected $paramsDataType = '';
  /**
   * Priority for this rule. This is an integer between `0` and `65535`, both
   * inclusive. The default value is `1000`. Relative priorities determine which
   * rule takes effect if multiple rules apply. Lower values indicate higher
   * priority. For example, a rule with priority `0` has higher precedence than
   * a rule with priority `1`. DENY rules take precedence over ALLOW rules if
   * they have equal priority. Note that VPC networks have implied rules with a
   * priority of `65535`. To avoid conflicts with the implied rules, use a
   * priority number less than `65535`.
   *
   * @var int
   */
  public $priority;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * If source ranges are specified, the firewall rule applies only to traffic
   * that has a source IP address in these ranges. These ranges must be
   * expressed inCIDR format. One or both of sourceRanges and sourceTags may be
   * set. If both fields are set, the rule applies to traffic that has a source
   * IP address within sourceRanges OR a source IP from a resource with a
   * matching tag listed in thesourceTags field. The connection does not need to
   * match both fields for the rule to apply. Both IPv4 and IPv6 are supported.
   *
   * @var string[]
   */
  public $sourceRanges;
  /**
   * If source service accounts are specified, the firewall rules apply only to
   * traffic originating from an instance with a service account in this list.
   * Source service accounts cannot be used to control traffic to an instance's
   * external IP address because service accounts are associated with an
   * instance, not an IP address.sourceRanges can be set at the same time
   * assourceServiceAccounts. If both are set, the firewall applies to traffic
   * that has a source IP address within the sourceRanges OR a source IP that
   * belongs to an instance with service account listed insourceServiceAccount.
   * The connection does not need to match both fields for the firewall to
   * apply.sourceServiceAccounts cannot be used at the same time assourceTags or
   * targetTags.
   *
   * @var string[]
   */
  public $sourceServiceAccounts;
  /**
   * If source tags are specified, the firewall rule applies only to traffic
   * with source IPs that match the primary network interfaces of VM instances
   * that have the tag and are in the same VPC network. Source tags cannot be
   * used to control traffic to an instance's external IP address, it only
   * applies to traffic between instances in the same virtual network. Because
   * tags are associated with instances, not IP addresses. One or both of
   * sourceRanges and sourceTags may be set. If both fields are set, the
   * firewall applies to traffic that has a source IP address within
   * sourceRanges OR a source IP from a resource with a matching tag listed in
   * the sourceTags field. The connection does not need to match both fields for
   * the firewall to apply.
   *
   * @var string[]
   */
  public $sourceTags;
  /**
   * A list of service accounts indicating sets of instances located in the
   * network that may make network connections as specified
   * inallowed[].targetServiceAccounts cannot be used at the same time
   * astargetTags or sourceTags. If neither targetServiceAccounts nor targetTags
   * are specified, the firewall rule applies to all instances on the specified
   * network.
   *
   * @var string[]
   */
  public $targetServiceAccounts;
  /**
   * A list of tags that controls which instances the firewall rule applies to.
   * If targetTags are specified, then the firewall rule applies only to
   * instances in the VPC network that have one of those tags. If no targetTags
   * are specified, the firewall rule applies to all instances on the specified
   * network.
   *
   * @var string[]
   */
  public $targetTags;

  /**
   * The list of ALLOW rules specified by this firewall. Each rule specifies a
   * protocol and port-range tuple that describes a permitted connection.
   *
   * @param FirewallAllowed[] $allowed
   */
  public function setAllowed($allowed)
  {
    $this->allowed = $allowed;
  }
  /**
   * @return FirewallAllowed[]
   */
  public function getAllowed()
  {
    return $this->allowed;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * The list of DENY rules specified by this firewall. Each rule specifies a
   * protocol and port-range tuple that describes a denied connection.
   *
   * @param FirewallDenied[] $denied
   */
  public function setDenied($denied)
  {
    $this->denied = $denied;
  }
  /**
   * @return FirewallDenied[]
   */
  public function getDenied()
  {
    return $this->denied;
  }
  /**
   * An optional description of this resource. Provide this field when you
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
   * If destination ranges are specified, the firewall rule applies only to
   * traffic that has destination IP address in these ranges. These ranges must
   * be expressed inCIDR format. Both IPv4 and IPv6 are supported.
   *
   * @param string[] $destinationRanges
   */
  public function setDestinationRanges($destinationRanges)
  {
    $this->destinationRanges = $destinationRanges;
  }
  /**
   * @return string[]
   */
  public function getDestinationRanges()
  {
    return $this->destinationRanges;
  }
  /**
   * Direction of traffic to which this firewall applies, either `INGRESS` or
   * `EGRESS`. The default is `INGRESS`. For `EGRESS` traffic, you cannot
   * specify the sourceTags fields.
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
   * Denotes whether the firewall rule is disabled. When set to true, the
   * firewall rule is not enforced and the network behaves as if it did not
   * exist. If this is unspecified, the firewall rule will be enabled.
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
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always compute#firewall
   * for firewall rules.
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
   * This field denotes the logging options for a particular firewall rule. If
   * logging is enabled, logs will be exported to Cloud Logging.
   *
   * @param FirewallLogConfig $logConfig
   */
  public function setLogConfig(FirewallLogConfig $logConfig)
  {
    $this->logConfig = $logConfig;
  }
  /**
   * @return FirewallLogConfig
   */
  public function getLogConfig()
  {
    return $this->logConfig;
  }
  /**
   * Name of the resource; provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?`. The first character must be a
   * lowercase letter, and all following characters (except for the last
   * character) must be a dash, lowercase letter, or digit. The last character
   * must be a lowercase letter or digit.
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
   * URL of the network resource for this firewall rule. If not specified when
   * creating a firewall rule, the default network is used:
   *
   * global/networks/default
   *
   * If you choose to specify this field, you can specify the network as a full
   * or partial URL. For example, the following are all valid URLs:         -
   * https://www.googleapis.com/compute/v1/projects/myproject/global/networks/my
   * -network     - projects/myproject/global/networks/my-network     -
   * global/networks/default
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param FirewallParams $params
   */
  public function setParams(FirewallParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return FirewallParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Priority for this rule. This is an integer between `0` and `65535`, both
   * inclusive. The default value is `1000`. Relative priorities determine which
   * rule takes effect if multiple rules apply. Lower values indicate higher
   * priority. For example, a rule with priority `0` has higher precedence than
   * a rule with priority `1`. DENY rules take precedence over ALLOW rules if
   * they have equal priority. Note that VPC networks have implied rules with a
   * priority of `65535`. To avoid conflicts with the implied rules, use a
   * priority number less than `65535`.
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
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * If source ranges are specified, the firewall rule applies only to traffic
   * that has a source IP address in these ranges. These ranges must be
   * expressed inCIDR format. One or both of sourceRanges and sourceTags may be
   * set. If both fields are set, the rule applies to traffic that has a source
   * IP address within sourceRanges OR a source IP from a resource with a
   * matching tag listed in thesourceTags field. The connection does not need to
   * match both fields for the rule to apply. Both IPv4 and IPv6 are supported.
   *
   * @param string[] $sourceRanges
   */
  public function setSourceRanges($sourceRanges)
  {
    $this->sourceRanges = $sourceRanges;
  }
  /**
   * @return string[]
   */
  public function getSourceRanges()
  {
    return $this->sourceRanges;
  }
  /**
   * If source service accounts are specified, the firewall rules apply only to
   * traffic originating from an instance with a service account in this list.
   * Source service accounts cannot be used to control traffic to an instance's
   * external IP address because service accounts are associated with an
   * instance, not an IP address.sourceRanges can be set at the same time
   * assourceServiceAccounts. If both are set, the firewall applies to traffic
   * that has a source IP address within the sourceRanges OR a source IP that
   * belongs to an instance with service account listed insourceServiceAccount.
   * The connection does not need to match both fields for the firewall to
   * apply.sourceServiceAccounts cannot be used at the same time assourceTags or
   * targetTags.
   *
   * @param string[] $sourceServiceAccounts
   */
  public function setSourceServiceAccounts($sourceServiceAccounts)
  {
    $this->sourceServiceAccounts = $sourceServiceAccounts;
  }
  /**
   * @return string[]
   */
  public function getSourceServiceAccounts()
  {
    return $this->sourceServiceAccounts;
  }
  /**
   * If source tags are specified, the firewall rule applies only to traffic
   * with source IPs that match the primary network interfaces of VM instances
   * that have the tag and are in the same VPC network. Source tags cannot be
   * used to control traffic to an instance's external IP address, it only
   * applies to traffic between instances in the same virtual network. Because
   * tags are associated with instances, not IP addresses. One or both of
   * sourceRanges and sourceTags may be set. If both fields are set, the
   * firewall applies to traffic that has a source IP address within
   * sourceRanges OR a source IP from a resource with a matching tag listed in
   * the sourceTags field. The connection does not need to match both fields for
   * the firewall to apply.
   *
   * @param string[] $sourceTags
   */
  public function setSourceTags($sourceTags)
  {
    $this->sourceTags = $sourceTags;
  }
  /**
   * @return string[]
   */
  public function getSourceTags()
  {
    return $this->sourceTags;
  }
  /**
   * A list of service accounts indicating sets of instances located in the
   * network that may make network connections as specified
   * inallowed[].targetServiceAccounts cannot be used at the same time
   * astargetTags or sourceTags. If neither targetServiceAccounts nor targetTags
   * are specified, the firewall rule applies to all instances on the specified
   * network.
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
   * A list of tags that controls which instances the firewall rule applies to.
   * If targetTags are specified, then the firewall rule applies only to
   * instances in the VPC network that have one of those tags. If no targetTags
   * are specified, the firewall rule applies to all instances on the specified
   * network.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Firewall::class, 'Google_Service_Compute_Firewall');
