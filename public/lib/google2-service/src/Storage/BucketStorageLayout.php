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

namespace Google\Service\Storage;

class BucketStorageLayout extends \Google\Model
{
  /**
   * The name of the bucket.
   *
   * @var string
   */
  public $bucket;
  protected $customPlacementConfigType = BucketStorageLayoutCustomPlacementConfig::class;
  protected $customPlacementConfigDataType = '';
  protected $hierarchicalNamespaceType = BucketStorageLayoutHierarchicalNamespace::class;
  protected $hierarchicalNamespaceDataType = '';
  /**
   * The kind of item this is. For storage layout, this is always
   * storage#storageLayout.
   *
   * @var string
   */
  public $kind;
  /**
   * The location of the bucket.
   *
   * @var string
   */
  public $location;
  /**
   * The type of the bucket location.
   *
   * @var string
   */
  public $locationType;

  /**
   * The name of the bucket.
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * The bucket's custom placement configuration for Custom Dual Regions.
   *
   * @param BucketStorageLayoutCustomPlacementConfig $customPlacementConfig
   */
  public function setCustomPlacementConfig(BucketStorageLayoutCustomPlacementConfig $customPlacementConfig)
  {
    $this->customPlacementConfig = $customPlacementConfig;
  }
  /**
   * @return BucketStorageLayoutCustomPlacementConfig
   */
  public function getCustomPlacementConfig()
  {
    return $this->customPlacementConfig;
  }
  /**
   * The bucket's hierarchical namespace configuration.
   *
   * @param BucketStorageLayoutHierarchicalNamespace $hierarchicalNamespace
   */
  public function setHierarchicalNamespace(BucketStorageLayoutHierarchicalNamespace $hierarchicalNamespace)
  {
    $this->hierarchicalNamespace = $hierarchicalNamespace;
  }
  /**
   * @return BucketStorageLayoutHierarchicalNamespace
   */
  public function getHierarchicalNamespace()
  {
    return $this->hierarchicalNamespace;
  }
  /**
   * The kind of item this is. For storage layout, this is always
   * storage#storageLayout.
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
   * The location of the bucket.
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
   * The type of the bucket location.
   *
   * @param string $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return string
   */
  public function getLocationType()
  {
    return $this->locationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketStorageLayout::class, 'Google_Service_Storage_BucketStorageLayout');
