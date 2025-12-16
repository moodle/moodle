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

class ForwardingRuleInfo extends \Google\Model
{
  /**
   * Name of the forwarding rule.
   *
   * @var string
   */
  public $displayName;
  /**
   * Name of the load balancer the forwarding rule belongs to. Empty for
   * forwarding rules not related to load balancers (like PSC forwarding rules).
   *
   * @var string
   */
  public $loadBalancerName;
  /**
   * Port range defined in the forwarding rule that matches the packet.
   *
   * @var string
   */
  public $matchedPortRange;
  /**
   * Protocol defined in the forwarding rule that matches the packet.
   *
   * @var string
   */
  public $matchedProtocol;
  /**
   * Network URI.
   *
   * @var string
   */
  public $networkUri;
  /**
   * PSC Google API target this forwarding rule targets (if applicable).
   *
   * @var string
   */
  public $pscGoogleApiTarget;
  /**
   * URI of the PSC service attachment this forwarding rule targets (if
   * applicable).
   *
   * @var string
   */
  public $pscServiceAttachmentUri;
  /**
   * Region of the forwarding rule. Set only for regional forwarding rules.
   *
   * @var string
   */
  public $region;
  /**
   * Target type of the forwarding rule.
   *
   * @var string
   */
  public $target;
  /**
   * URI of the forwarding rule.
   *
   * @var string
   */
  public $uri;
  /**
   * VIP of the forwarding rule.
   *
   * @var string
   */
  public $vip;

  /**
   * Name of the forwarding rule.
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
   * Name of the load balancer the forwarding rule belongs to. Empty for
   * forwarding rules not related to load balancers (like PSC forwarding rules).
   *
   * @param string $loadBalancerName
   */
  public function setLoadBalancerName($loadBalancerName)
  {
    $this->loadBalancerName = $loadBalancerName;
  }
  /**
   * @return string
   */
  public function getLoadBalancerName()
  {
    return $this->loadBalancerName;
  }
  /**
   * Port range defined in the forwarding rule that matches the packet.
   *
   * @param string $matchedPortRange
   */
  public function setMatchedPortRange($matchedPortRange)
  {
    $this->matchedPortRange = $matchedPortRange;
  }
  /**
   * @return string
   */
  public function getMatchedPortRange()
  {
    return $this->matchedPortRange;
  }
  /**
   * Protocol defined in the forwarding rule that matches the packet.
   *
   * @param string $matchedProtocol
   */
  public function setMatchedProtocol($matchedProtocol)
  {
    $this->matchedProtocol = $matchedProtocol;
  }
  /**
   * @return string
   */
  public function getMatchedProtocol()
  {
    return $this->matchedProtocol;
  }
  /**
   * Network URI.
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
   * PSC Google API target this forwarding rule targets (if applicable).
   *
   * @param string $pscGoogleApiTarget
   */
  public function setPscGoogleApiTarget($pscGoogleApiTarget)
  {
    $this->pscGoogleApiTarget = $pscGoogleApiTarget;
  }
  /**
   * @return string
   */
  public function getPscGoogleApiTarget()
  {
    return $this->pscGoogleApiTarget;
  }
  /**
   * URI of the PSC service attachment this forwarding rule targets (if
   * applicable).
   *
   * @param string $pscServiceAttachmentUri
   */
  public function setPscServiceAttachmentUri($pscServiceAttachmentUri)
  {
    $this->pscServiceAttachmentUri = $pscServiceAttachmentUri;
  }
  /**
   * @return string
   */
  public function getPscServiceAttachmentUri()
  {
    return $this->pscServiceAttachmentUri;
  }
  /**
   * Region of the forwarding rule. Set only for regional forwarding rules.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Target type of the forwarding rule.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * URI of the forwarding rule.
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
  /**
   * VIP of the forwarding rule.
   *
   * @param string $vip
   */
  public function setVip($vip)
  {
    $this->vip = $vip;
  }
  /**
   * @return string
   */
  public function getVip()
  {
    return $this->vip;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ForwardingRuleInfo::class, 'Google_Service_NetworkManagement_ForwardingRuleInfo');
