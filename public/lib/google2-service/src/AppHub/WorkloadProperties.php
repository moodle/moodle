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

namespace Google\Service\AppHub;

class WorkloadProperties extends \Google\Model
{
  protected $extendedMetadataType = ExtendedMetadata::class;
  protected $extendedMetadataDataType = 'map';
  protected $functionalTypeType = FunctionalType::class;
  protected $functionalTypeDataType = '';
  /**
   * Output only. The service project identifier that the underlying cloud
   * resource resides in. Empty for non-cloud resources.
   *
   * @var string
   */
  public $gcpProject;
  protected $identityType = Identity::class;
  protected $identityDataType = '';
  /**
   * Output only. The location that the underlying compute resource resides in
   * (for example, us-west1).
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The location that the underlying compute resource resides in
   * if it is zonal (for example, us-west1-a).
   *
   * @var string
   */
  public $zone;

  /**
   * Output only. Additional metadata specific to the resource type. The key is
   * a string that identifies the type of metadata and the value is the metadata
   * contents specific to that type. Key format:
   * `apphub.googleapis.com/{metadataType}`
   *
   * @param ExtendedMetadata[] $extendedMetadata
   */
  public function setExtendedMetadata($extendedMetadata)
  {
    $this->extendedMetadata = $extendedMetadata;
  }
  /**
   * @return ExtendedMetadata[]
   */
  public function getExtendedMetadata()
  {
    return $this->extendedMetadata;
  }
  /**
   * Output only. The type of the workload.
   *
   * @param FunctionalType $functionalType
   */
  public function setFunctionalType(FunctionalType $functionalType)
  {
    $this->functionalType = $functionalType;
  }
  /**
   * @return FunctionalType
   */
  public function getFunctionalType()
  {
    return $this->functionalType;
  }
  /**
   * Output only. The service project identifier that the underlying cloud
   * resource resides in. Empty for non-cloud resources.
   *
   * @param string $gcpProject
   */
  public function setGcpProject($gcpProject)
  {
    $this->gcpProject = $gcpProject;
  }
  /**
   * @return string
   */
  public function getGcpProject()
  {
    return $this->gcpProject;
  }
  /**
   * Output only. The identity associated with the workload.
   *
   * @param Identity $identity
   */
  public function setIdentity(Identity $identity)
  {
    $this->identity = $identity;
  }
  /**
   * @return Identity
   */
  public function getIdentity()
  {
    return $this->identity;
  }
  /**
   * Output only. The location that the underlying compute resource resides in
   * (for example, us-west1).
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. The location that the underlying compute resource resides in
   * if it is zonal (for example, us-west1-a).
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
class_alias(WorkloadProperties::class, 'Google_Service_AppHub_WorkloadProperties');
