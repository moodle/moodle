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

class AcceleratorType extends \Google\Model
{
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $deprecatedType = DeprecationStatus::class;
  protected $deprecatedDataType = '';
  /**
   * [Output Only] An optional textual description of the resource.
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
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#acceleratorType for accelerator types.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] Maximum number of accelerator cards allowed per instance.
   *
   * @var int
   */
  public $maximumCardsPerInstance;
  /**
   * [Output Only] Name of the resource.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] Server-defined, fully qualified URL for this
   * resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * [Output Only] The name of the zone where the accelerator type resides, such
   * as us-central1-a. You must specify this field as part of the HTTP request
   * URL. It is not settable as a field in the request body.
   *
   * @var string
   */
  public $zone;

  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
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
   * [Output Only] The deprecation status associated with this accelerator type.
   *
   * @param DeprecationStatus $deprecated
   */
  public function setDeprecated(DeprecationStatus $deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return DeprecationStatus
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * [Output Only] An optional textual description of the resource.
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
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#acceleratorType for accelerator types.
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
   * [Output Only] Maximum number of accelerator cards allowed per instance.
   *
   * @param int $maximumCardsPerInstance
   */
  public function setMaximumCardsPerInstance($maximumCardsPerInstance)
  {
    $this->maximumCardsPerInstance = $maximumCardsPerInstance;
  }
  /**
   * @return int
   */
  public function getMaximumCardsPerInstance()
  {
    return $this->maximumCardsPerInstance;
  }
  /**
   * [Output Only] Name of the resource.
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
   * Output only. [Output Only] Server-defined, fully qualified URL for this
   * resource.
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
   * [Output Only] The name of the zone where the accelerator type resides, such
   * as us-central1-a. You must specify this field as part of the HTTP request
   * URL. It is not settable as a field in the request body.
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
class_alias(AcceleratorType::class, 'Google_Service_Compute_AcceleratorType');
