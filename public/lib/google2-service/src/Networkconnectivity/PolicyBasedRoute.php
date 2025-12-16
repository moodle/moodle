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

namespace Google\Service\Networkconnectivity;

class PolicyBasedRoute extends \Google\Collection
{
  /**
   * Default value.
   */
  public const NEXT_HOP_OTHER_ROUTES_OTHER_ROUTES_UNSPECIFIED = 'OTHER_ROUTES_UNSPECIFIED';
  /**
   * Use the routes from the default routing tables (system-generated routes,
   * custom routes, peering route) to determine the next hop. This effectively
   * excludes matching packets being applied on other PBRs with a lower
   * priority.
   */
  public const NEXT_HOP_OTHER_ROUTES_DEFAULT_ROUTING = 'DEFAULT_ROUTING';
  protected $collection_key = 'warnings';
  /**
   * Output only. Time when the policy-based route was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. An optional description of this resource. Provide this field when
   * you create the resource.
   *
   * @var string
   */
  public $description;
  protected $filterType = Filter::class;
  protected $filterDataType = '';
  protected $interconnectAttachmentType = InterconnectAttachment::class;
  protected $interconnectAttachmentDataType = '';
  /**
   * Output only. Type of this resource. Always
   * networkconnectivity#policyBasedRoute for policy-based Route resources.
   *
   * @var string
   */
  public $kind;
  /**
   * User-defined labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Identifier. A unique name of the resource in the form of `projec
   * ts/{project_number}/locations/global/PolicyBasedRoutes/{policy_based_route_
   * id}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. Fully-qualified URL of the network that this route applies to,
   * for example: projects/my-project/global/networks/my-network.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The IP address of a global-access-enabled L4 ILB that is the next
   * hop for matching packets. For this version, only nextHopIlbIp is supported.
   *
   * @var string
   */
  public $nextHopIlbIp;
  /**
   * Optional. Other routes that will be referenced to determine the next hop of
   * the packet.
   *
   * @var string
   */
  public $nextHopOtherRoutes;
  /**
   * Optional. The priority of this policy-based route. Priority is used to
   * break ties in cases where there are more than one matching policy-based
   * routes found. In cases where multiple policy-based routes are matched, the
   * one with the lowest-numbered priority value wins. The default value is
   * 1000. The priority value must be from 1 to 65535, inclusive.
   *
   * @var int
   */
  public $priority;
  /**
   * Output only. Server-defined fully-qualified URL for this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. Time when the policy-based route was updated.
   *
   * @var string
   */
  public $updateTime;
  protected $virtualMachineType = VirtualMachine::class;
  protected $virtualMachineDataType = '';
  protected $warningsType = Warnings::class;
  protected $warningsDataType = 'array';

  /**
   * Output only. Time when the policy-based route was created.
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
   * Optional. An optional description of this resource. Provide this field when
   * you create the resource.
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
   * Required. The filter to match L4 traffic.
   *
   * @param Filter $filter
   */
  public function setFilter(Filter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return Filter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. The interconnect attachments that this policy-based route applies
   * to.
   *
   * @param InterconnectAttachment $interconnectAttachment
   */
  public function setInterconnectAttachment(InterconnectAttachment $interconnectAttachment)
  {
    $this->interconnectAttachment = $interconnectAttachment;
  }
  /**
   * @return InterconnectAttachment
   */
  public function getInterconnectAttachment()
  {
    return $this->interconnectAttachment;
  }
  /**
   * Output only. Type of this resource. Always
   * networkconnectivity#policyBasedRoute for policy-based Route resources.
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
   * User-defined labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Immutable. Identifier. A unique name of the resource in the form of `projec
   * ts/{project_number}/locations/global/PolicyBasedRoutes/{policy_based_route_
   * id}`
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
   * Required. Fully-qualified URL of the network that this route applies to,
   * for example: projects/my-project/global/networks/my-network.
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
   * Optional. The IP address of a global-access-enabled L4 ILB that is the next
   * hop for matching packets. For this version, only nextHopIlbIp is supported.
   *
   * @param string $nextHopIlbIp
   */
  public function setNextHopIlbIp($nextHopIlbIp)
  {
    $this->nextHopIlbIp = $nextHopIlbIp;
  }
  /**
   * @return string
   */
  public function getNextHopIlbIp()
  {
    return $this->nextHopIlbIp;
  }
  /**
   * Optional. Other routes that will be referenced to determine the next hop of
   * the packet.
   *
   * Accepted values: OTHER_ROUTES_UNSPECIFIED, DEFAULT_ROUTING
   *
   * @param self::NEXT_HOP_OTHER_ROUTES_* $nextHopOtherRoutes
   */
  public function setNextHopOtherRoutes($nextHopOtherRoutes)
  {
    $this->nextHopOtherRoutes = $nextHopOtherRoutes;
  }
  /**
   * @return self::NEXT_HOP_OTHER_ROUTES_*
   */
  public function getNextHopOtherRoutes()
  {
    return $this->nextHopOtherRoutes;
  }
  /**
   * Optional. The priority of this policy-based route. Priority is used to
   * break ties in cases where there are more than one matching policy-based
   * routes found. In cases where multiple policy-based routes are matched, the
   * one with the lowest-numbered priority value wins. The default value is
   * 1000. The priority value must be from 1 to 65535, inclusive.
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
   * Output only. Server-defined fully-qualified URL for this resource.
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
   * Output only. Time when the policy-based route was updated.
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
  /**
   * Optional. VM instances that this policy-based route applies to.
   *
   * @param VirtualMachine $virtualMachine
   */
  public function setVirtualMachine(VirtualMachine $virtualMachine)
  {
    $this->virtualMachine = $virtualMachine;
  }
  /**
   * @return VirtualMachine
   */
  public function getVirtualMachine()
  {
    return $this->virtualMachine;
  }
  /**
   * Output only. If potential misconfigurations are detected for this route,
   * this field will be populated with warning messages.
   *
   * @param Warnings[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return Warnings[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyBasedRoute::class, 'Google_Service_Networkconnectivity_PolicyBasedRoute');
