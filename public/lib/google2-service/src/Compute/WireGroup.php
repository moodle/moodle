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

class WireGroup extends \Google\Collection
{
  protected $collection_key = 'wires';
  /**
   * Indicates whether the wires in the wire group are enabled. When false, the
   * wires in the wire group are disabled. When true and when there is
   * simultaneously no wire-specific override of `adminEnabled` to false, a
   * given wire is enabled. Defaults to true.
   *
   * @var bool
   */
  public $adminEnabled;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of the wire group.
   *
   * @var string
   */
  public $description;
  protected $endpointsType = WireGroupEndpoint::class;
  protected $endpointsDataType = 'map';
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource. Alwayscompute#wireGroups
   * for wire groups.
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
   * Output only. [Output Only] Indicates whether there are wire changes yet to
   * be processed.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $topologyType = WireGroupTopology::class;
  protected $topologyDataType = '';
  protected $wirePropertiesType = WireProperties::class;
  protected $wirePropertiesDataType = '';
  protected $wiresType = Wire::class;
  protected $wiresDataType = 'array';

  /**
   * Indicates whether the wires in the wire group are enabled. When false, the
   * wires in the wire group are disabled. When true and when there is
   * simultaneously no wire-specific override of `adminEnabled` to false, a
   * given wire is enabled. Defaults to true.
   *
   * @param bool $adminEnabled
   */
  public function setAdminEnabled($adminEnabled)
  {
    $this->adminEnabled = $adminEnabled;
  }
  /**
   * @return bool
   */
  public function getAdminEnabled()
  {
    return $this->adminEnabled;
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
   * An optional description of the wire group.
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
   * A map that contains the logical endpoints of the wire group. Specify key-
   * value pairs for the map as follows:        - Key: an RFC1035 user-specified
   * label.    - Value: an Endpoint object.
   *
   * @param WireGroupEndpoint[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return WireGroupEndpoint[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
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
   * Output only. [Output Only] Type of the resource. Alwayscompute#wireGroups
   * for wire groups.
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
   * Output only. [Output Only] Indicates whether there are wire changes yet to
   * be processed.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
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
   * Output only. Topology details for the wire group configuration.
   *
   * @param WireGroupTopology $topology
   */
  public function setTopology(WireGroupTopology $topology)
  {
    $this->topology = $topology;
  }
  /**
   * @return WireGroupTopology
   */
  public function getTopology()
  {
    return $this->topology;
  }
  /**
   * Properties for all wires in the wire group.
   *
   * @param WireProperties $wireProperties
   */
  public function setWireProperties(WireProperties $wireProperties)
  {
    $this->wireProperties = $wireProperties;
  }
  /**
   * @return WireProperties
   */
  public function getWireProperties()
  {
    return $this->wireProperties;
  }
  /**
   * Output only. The single/redundant wire(s) managed by the wire group.
   *
   * @param Wire[] $wires
   */
  public function setWires($wires)
  {
    $this->wires = $wires;
  }
  /**
   * @return Wire[]
   */
  public function getWires()
  {
    return $this->wires;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WireGroup::class, 'Google_Service_Compute_WireGroup');
