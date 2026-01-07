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

class InstancesGetEffectiveFirewallsResponseEffectiveFirewallPolicy extends \Google\Collection
{
  public const TYPE_HIERARCHY = 'HIERARCHY';
  public const TYPE_NETWORK = 'NETWORK';
  public const TYPE_NETWORK_REGIONAL = 'NETWORK_REGIONAL';
  public const TYPE_SYSTEM_GLOBAL = 'SYSTEM_GLOBAL';
  public const TYPE_SYSTEM_REGIONAL = 'SYSTEM_REGIONAL';
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  protected $collection_key = 'rules';
  /**
   * Output only. [Output Only] Deprecated, please use short name instead. The
   * display name of the firewall policy.
   *
   * @deprecated
   * @var string
   */
  public $displayName;
  /**
   * Output only. [Output Only] The name of the firewall policy.
   *
   * @var string
   */
  public $name;
  protected $packetMirroringRulesType = FirewallPolicyRule::class;
  protected $packetMirroringRulesDataType = 'array';
  /**
   * Output only. [Output only] Priority of firewall policy association. Not
   * applicable for type=HIERARCHY.
   *
   * @var int
   */
  public $priority;
  protected $rulesType = FirewallPolicyRule::class;
  protected $rulesDataType = 'array';
  /**
   * Output only. [Output Only] The short name of the firewall policy.
   *
   * @var string
   */
  public $shortName;
  /**
   * Output only. [Output Only] The type of the firewall policy. Can be one of
   * HIERARCHY, NETWORK, NETWORK_REGIONAL, SYSTEM_GLOBAL, SYSTEM_REGIONAL.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. [Output Only] Deprecated, please use short name instead. The
   * display name of the firewall policy.
   *
   * @deprecated
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. [Output Only] The name of the firewall policy.
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
   * Output only. [Output Only] The packet mirroring rules that apply to the
   * instance.
   *
   * @param FirewallPolicyRule[] $packetMirroringRules
   */
  public function setPacketMirroringRules($packetMirroringRules)
  {
    $this->packetMirroringRules = $packetMirroringRules;
  }
  /**
   * @return FirewallPolicyRule[]
   */
  public function getPacketMirroringRules()
  {
    return $this->packetMirroringRules;
  }
  /**
   * Output only. [Output only] Priority of firewall policy association. Not
   * applicable for type=HIERARCHY.
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
   * [Output Only] The rules that apply to the instance. Only rules that target
   * the specific VM instance are returned if target service accounts or target
   * secure tags are specified in the rules.
   *
   * @param FirewallPolicyRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return FirewallPolicyRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Output only. [Output Only] The short name of the firewall policy.
   *
   * @param string $shortName
   */
  public function setShortName($shortName)
  {
    $this->shortName = $shortName;
  }
  /**
   * @return string
   */
  public function getShortName()
  {
    return $this->shortName;
  }
  /**
   * Output only. [Output Only] The type of the firewall policy. Can be one of
   * HIERARCHY, NETWORK, NETWORK_REGIONAL, SYSTEM_GLOBAL, SYSTEM_REGIONAL.
   *
   * Accepted values: HIERARCHY, NETWORK, NETWORK_REGIONAL, SYSTEM_GLOBAL,
   * SYSTEM_REGIONAL, UNSPECIFIED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesGetEffectiveFirewallsResponseEffectiveFirewallPolicy::class, 'Google_Service_Compute_InstancesGetEffectiveFirewallsResponseEffectiveFirewallPolicy');
