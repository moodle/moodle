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

class InstanceTemplate extends \Google\Model
{
  /**
   * Output only. [Output Only] The creation timestamp for this instance
   * template inRFC3339 text format.
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
   * Output only. [Output Only] A unique identifier for this instance template.
   * The server defines this identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] The resource type, which is
   * alwayscompute#instanceTemplate for instance templates.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource; provided by the client when the resource is created.
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
  protected $propertiesType = InstanceProperties::class;
  protected $propertiesDataType = '';
  /**
   * Output only. [Output Only] URL of the region where the instance template
   * resides. Only applicable for regional resources.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] The URL for this instance template. The server
   * defines this URL.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The source instance used to create the template. You can provide this as a
   * partial or full URL to the resource. For example, the following are valid
   * values:              - https://www.googleapis.com/compute/v1/projects/proje
   * ct/zones/zone/instances/instance     -
   * projects/project/zones/zone/instances/instance
   *
   * @var string
   */
  public $sourceInstance;
  protected $sourceInstanceParamsType = SourceInstanceParams::class;
  protected $sourceInstanceParamsDataType = '';

  /**
   * Output only. [Output Only] The creation timestamp for this instance
   * template inRFC3339 text format.
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
   * Output only. [Output Only] A unique identifier for this instance template.
   * The server defines this identifier.
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
   * Output only. [Output Only] The resource type, which is
   * alwayscompute#instanceTemplate for instance templates.
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
   * Name of the resource; provided by the client when the resource is created.
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
   * The instance properties for this instance template.
   *
   * @param InstanceProperties $properties
   */
  public function setProperties(InstanceProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return InstanceProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Output only. [Output Only] URL of the region where the instance template
   * resides. Only applicable for regional resources.
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
   * Output only. [Output Only] The URL for this instance template. The server
   * defines this URL.
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
   * The source instance used to create the template. You can provide this as a
   * partial or full URL to the resource. For example, the following are valid
   * values:              - https://www.googleapis.com/compute/v1/projects/proje
   * ct/zones/zone/instances/instance     -
   * projects/project/zones/zone/instances/instance
   *
   * @param string $sourceInstance
   */
  public function setSourceInstance($sourceInstance)
  {
    $this->sourceInstance = $sourceInstance;
  }
  /**
   * @return string
   */
  public function getSourceInstance()
  {
    return $this->sourceInstance;
  }
  /**
   * The source instance params to use to create this instance template.
   *
   * @param SourceInstanceParams $sourceInstanceParams
   */
  public function setSourceInstanceParams(SourceInstanceParams $sourceInstanceParams)
  {
    $this->sourceInstanceParams = $sourceInstanceParams;
  }
  /**
   * @return SourceInstanceParams
   */
  public function getSourceInstanceParams()
  {
    return $this->sourceInstanceParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceTemplate::class, 'Google_Service_Compute_InstanceTemplate');
