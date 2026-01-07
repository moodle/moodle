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

class NetworkProfile extends \Google\Model
{
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * Output only. [Output Only] An optional description of this resource.
   *
   * @var string
   */
  public $description;
  protected $featuresType = NetworkProfileNetworkFeatures::class;
  protected $featuresDataType = '';
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#networkProfile for network profiles.
   *
   * @var string
   */
  public $kind;
  protected $locationType = NetworkProfileLocation::class;
  protected $locationDataType = '';
  /**
   * Output only. [Output Only] Name of the resource.
   *
   * @var string
   */
  public $name;
  protected $profileTypeType = NetworkProfileProfileType::class;
  protected $profileTypeDataType = '';
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @var string
   */
  public $selfLinkWithId;

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
   * Output only. [Output Only] An optional description of this resource.
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
   * Output only. [Output Only] Features supported by the network.
   *
   * @param NetworkProfileNetworkFeatures $features
   */
  public function setFeatures(NetworkProfileNetworkFeatures $features)
  {
    $this->features = $features;
  }
  /**
   * @return NetworkProfileNetworkFeatures
   */
  public function getFeatures()
  {
    return $this->features;
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
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#networkProfile for network profiles.
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
   * Output only. [Output Only] Location to which the network is restricted.
   *
   * @param NetworkProfileLocation $location
   */
  public function setLocation(NetworkProfileLocation $location)
  {
    $this->location = $location;
  }
  /**
   * @return NetworkProfileLocation
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. [Output Only] Name of the resource.
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
   * Output only. [Output Only] Type of the network profile.
   *
   * @param NetworkProfileProfileType $profileType
   */
  public function setProfileType(NetworkProfileProfileType $profileType)
  {
    $this->profileType = $profileType;
  }
  /**
   * @return NetworkProfileProfileType
   */
  public function getProfileType()
  {
    return $this->profileType;
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
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkProfile::class, 'Google_Service_Compute_NetworkProfile');
