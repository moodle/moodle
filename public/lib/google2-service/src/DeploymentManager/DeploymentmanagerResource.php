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

namespace Google\Service\DeploymentManager;

class DeploymentmanagerResource extends \Google\Collection
{
  protected $collection_key = 'warnings';
  protected $accessControlType = ResourceAccessControl::class;
  protected $accessControlDataType = '';
  /**
   * Output only. The evaluated properties of the resource with references
   * expanded. Returned as serialized YAML.
   *
   * @var string
   */
  public $finalProperties;
  /**
   * @var string
   */
  public $id;
  /**
   * Output only. Creation timestamp in RFC3339 text format.
   *
   * @var string
   */
  public $insertTime;
  /**
   * Output only. URL of the manifest representing the current configuration of
   * this resource.
   *
   * @var string
   */
  public $manifest;
  /**
   * Output only. The name of the resource as it appears in the YAML config.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The current properties of the resource before any references
   * have been filled in. Returned as serialized YAML.
   *
   * @var string
   */
  public $properties;
  /**
   * Output only. The type of the resource, for example `compute.v1.instance`,
   * or `cloudfunctions.v1beta1.function`.
   *
   * @var string
   */
  public $type;
  protected $updateType = ResourceUpdate::class;
  protected $updateDataType = '';
  /**
   * Output only. Update timestamp in RFC3339 text format.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The URL of the actual resource.
   *
   * @var string
   */
  public $url;
  protected $warningsType = DeploymentmanagerResourceWarnings::class;
  protected $warningsDataType = 'array';

  /**
   * The Access Control Policy set on this resource.
   *
   * @param ResourceAccessControl $accessControl
   */
  public function setAccessControl(ResourceAccessControl $accessControl)
  {
    $this->accessControl = $accessControl;
  }
  /**
   * @return ResourceAccessControl
   */
  public function getAccessControl()
  {
    return $this->accessControl;
  }
  /**
   * Output only. The evaluated properties of the resource with references
   * expanded. Returned as serialized YAML.
   *
   * @param string $finalProperties
   */
  public function setFinalProperties($finalProperties)
  {
    $this->finalProperties = $finalProperties;
  }
  /**
   * @return string
   */
  public function getFinalProperties()
  {
    return $this->finalProperties;
  }
  /**
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
   * Output only. Creation timestamp in RFC3339 text format.
   *
   * @param string $insertTime
   */
  public function setInsertTime($insertTime)
  {
    $this->insertTime = $insertTime;
  }
  /**
   * @return string
   */
  public function getInsertTime()
  {
    return $this->insertTime;
  }
  /**
   * Output only. URL of the manifest representing the current configuration of
   * this resource.
   *
   * @param string $manifest
   */
  public function setManifest($manifest)
  {
    $this->manifest = $manifest;
  }
  /**
   * @return string
   */
  public function getManifest()
  {
    return $this->manifest;
  }
  /**
   * Output only. The name of the resource as it appears in the YAML config.
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
   * Output only. The current properties of the resource before any references
   * have been filled in. Returned as serialized YAML.
   *
   * @param string $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return string
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Output only. The type of the resource, for example `compute.v1.instance`,
   * or `cloudfunctions.v1beta1.function`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. If Deployment Manager is currently updating or previewing an
   * update to this resource, the updated configuration appears here.
   *
   * @param ResourceUpdate $update
   */
  public function setUpdate(ResourceUpdate $update)
  {
    $this->update = $update;
  }
  /**
   * @return ResourceUpdate
   */
  public function getUpdate()
  {
    return $this->update;
  }
  /**
   * Output only. Update timestamp in RFC3339 text format.
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
   * Output only. The URL of the actual resource.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  /**
   * Output only. If warning messages are generated during processing of this
   * resource, this field will be populated.
   *
   * @param DeploymentmanagerResourceWarnings[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return DeploymentmanagerResourceWarnings[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeploymentmanagerResource::class, 'Google_Service_DeploymentManager_DeploymentmanagerResource');
