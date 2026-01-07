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

class TargetInstance extends \Google\Model
{
  /**
   * No NAT performed.
   */
  public const NAT_POLICY_NO_NAT = 'NO_NAT';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * A URL to the virtual machine instance that handles traffic for this target
   * instance. When creating a target instance, you can provide the fully-
   * qualified URL or a valid partial URL to the desired virtual machine. For
   * example, the following are all valid URLs:        - https://www.googleapis.
   * com/compute/v1/projects/project/zones/zone/instances/instance     -
   * projects/project/zones/zone/instances/instance     -
   * zones/zone/instances/instance
   *
   * @var string
   */
  public $instance;
  /**
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#targetInstance for target instances.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * Must have a value of NO_NAT. Protocol forwarding delivers packets while
   * preserving the destination IP address of the forwarding rule referencing
   * the target instance.
   *
   * @var string
   */
  public $natPolicy;
  /**
   * The URL of the network this target instance uses to forward traffic. If not
   * specified, the traffic will be forwarded to the network that the default
   * network interface belongs to.
   *
   * @var string
   */
  public $network;
  /**
   * [Output Only] The resource URL for the security policy associated with this
   * target instance.
   *
   * @var string
   */
  public $securityPolicy;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] URL of the zone where the target instance
   * resides. You must specify this field as part of the HTTP request URL. It is
   * not settable as a field in the request body.
   *
   * @var string
   */
  public $zone;

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
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
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
   * A URL to the virtual machine instance that handles traffic for this target
   * instance. When creating a target instance, you can provide the fully-
   * qualified URL or a valid partial URL to the desired virtual machine. For
   * example, the following are all valid URLs:        - https://www.googleapis.
   * com/compute/v1/projects/project/zones/zone/instances/instance     -
   * projects/project/zones/zone/instances/instance     -
   * zones/zone/instances/instance
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#targetInstance for target instances.
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
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * Must have a value of NO_NAT. Protocol forwarding delivers packets while
   * preserving the destination IP address of the forwarding rule referencing
   * the target instance.
   *
   * Accepted values: NO_NAT
   *
   * @param self::NAT_POLICY_* $natPolicy
   */
  public function setNatPolicy($natPolicy)
  {
    $this->natPolicy = $natPolicy;
  }
  /**
   * @return self::NAT_POLICY_*
   */
  public function getNatPolicy()
  {
    return $this->natPolicy;
  }
  /**
   * The URL of the network this target instance uses to forward traffic. If not
   * specified, the traffic will be forwarded to the network that the default
   * network interface belongs to.
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
   * [Output Only] The resource URL for the security policy associated with this
   * target instance.
   *
   * @param string $securityPolicy
   */
  public function setSecurityPolicy($securityPolicy)
  {
    $this->securityPolicy = $securityPolicy;
  }
  /**
   * @return string
   */
  public function getSecurityPolicy()
  {
    return $this->securityPolicy;
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
   * Output only. [Output Only] URL of the zone where the target instance
   * resides. You must specify this field as part of the HTTP request URL. It is
   * not settable as a field in the request body.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetInstance::class, 'Google_Service_Compute_TargetInstance');
