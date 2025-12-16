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

namespace Google\Service\Kmsinventory;

class GoogleCloudKmsInventoryV1ProtectedResource extends \Google\Collection
{
  protected $collection_key = 'cryptoKeyVersions';
  /**
   * The Cloud product that owns the resource. Example: `compute`
   *
   * @var string
   */
  public $cloudProduct;
  /**
   * Output only. The time at which this resource was created. The granularity
   * is in seconds. Timestamp.nanos will always be 0.
   *
   * @var string
   */
  public $createTime;
  /**
   * The name of the Cloud KMS [CryptoKeyVersion](https://cloud.google.com/kms/d
   * ocs/reference/rest/v1/projects.locations.keyRings.cryptoKeys.cryptoKeyVersi
   * ons?hl=en) used to protect this resource via CMEK. This field is empty if
   * the Google Cloud product owning the resource does not provide key version
   * data to Asset Inventory. If there are multiple key versions protecting the
   * resource, then this is same value as the first element of
   * crypto_key_versions.
   *
   * @var string
   */
  public $cryptoKeyVersion;
  /**
   * The names of the Cloud KMS [CryptoKeyVersion](https://cloud.google.com/kms/
   * docs/reference/rest/v1/projects.locations.keyRings.cryptoKeys.cryptoKeyVers
   * ions?hl=en) used to protect this resource via CMEK. This field is empty if
   * the Google Cloud product owning the resource does not provide key versions
   * data to Asset Inventory. The first element of this field is stored in
   * crypto_key_version.
   *
   * @var string[]
   */
  public $cryptoKeyVersions;
  /**
   * A key-value pair of the resource's labels (v1) to their values.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Location can be `global`, regional like `us-east1`, or zonal like `us-
   * west1-b`.
   *
   * @var string
   */
  public $location;
  /**
   * The full resource name of the resource. Example: `//compute.googleapis.com/
   * projects/my_project_123/zones/zone1/instances/instance1`.
   *
   * @var string
   */
  public $name;
  /**
   * Format: `projects/{PROJECT_NUMBER}`.
   *
   * @var string
   */
  public $project;
  /**
   * The ID of the project that owns the resource.
   *
   * @var string
   */
  public $projectId;
  /**
   * Example: `compute.googleapis.com/Disk`
   *
   * @var string
   */
  public $resourceType;

  /**
   * The Cloud product that owns the resource. Example: `compute`
   *
   * @param string $cloudProduct
   */
  public function setCloudProduct($cloudProduct)
  {
    $this->cloudProduct = $cloudProduct;
  }
  /**
   * @return string
   */
  public function getCloudProduct()
  {
    return $this->cloudProduct;
  }
  /**
   * Output only. The time at which this resource was created. The granularity
   * is in seconds. Timestamp.nanos will always be 0.
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
   * The name of the Cloud KMS [CryptoKeyVersion](https://cloud.google.com/kms/d
   * ocs/reference/rest/v1/projects.locations.keyRings.cryptoKeys.cryptoKeyVersi
   * ons?hl=en) used to protect this resource via CMEK. This field is empty if
   * the Google Cloud product owning the resource does not provide key version
   * data to Asset Inventory. If there are multiple key versions protecting the
   * resource, then this is same value as the first element of
   * crypto_key_versions.
   *
   * @param string $cryptoKeyVersion
   */
  public function setCryptoKeyVersion($cryptoKeyVersion)
  {
    $this->cryptoKeyVersion = $cryptoKeyVersion;
  }
  /**
   * @return string
   */
  public function getCryptoKeyVersion()
  {
    return $this->cryptoKeyVersion;
  }
  /**
   * The names of the Cloud KMS [CryptoKeyVersion](https://cloud.google.com/kms/
   * docs/reference/rest/v1/projects.locations.keyRings.cryptoKeys.cryptoKeyVers
   * ions?hl=en) used to protect this resource via CMEK. This field is empty if
   * the Google Cloud product owning the resource does not provide key versions
   * data to Asset Inventory. The first element of this field is stored in
   * crypto_key_version.
   *
   * @param string[] $cryptoKeyVersions
   */
  public function setCryptoKeyVersions($cryptoKeyVersions)
  {
    $this->cryptoKeyVersions = $cryptoKeyVersions;
  }
  /**
   * @return string[]
   */
  public function getCryptoKeyVersions()
  {
    return $this->cryptoKeyVersions;
  }
  /**
   * A key-value pair of the resource's labels (v1) to their values.
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
   * Location can be `global`, regional like `us-east1`, or zonal like `us-
   * west1-b`.
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
   * The full resource name of the resource. Example: `//compute.googleapis.com/
   * projects/my_project_123/zones/zone1/instances/instance1`.
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
   * Format: `projects/{PROJECT_NUMBER}`.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * The ID of the project that owns the resource.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Example: `compute.googleapis.com/Disk`
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudKmsInventoryV1ProtectedResource::class, 'Google_Service_Kmsinventory_GoogleCloudKmsInventoryV1ProtectedResource');
