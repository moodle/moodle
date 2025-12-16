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

namespace Google\Service\Firebasestorage;

class DefaultBucket extends \Google\Model
{
  protected $bucketType = Bucket::class;
  protected $bucketDataType = '';
  /**
   * Immutable. Location of the default bucket.
   *
   * @var string
   */
  public $location;
  /**
   * Identifier. Resource name of the default bucket.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. Storage class of the default bucket. Supported values are
   * available at https://cloud.google.com/storage/docs/storage-classes#classes.
   *
   * @var string
   */
  public $storageClass;

  /**
   * Output only. Underlying bucket resource.
   *
   * @param Bucket $bucket
   */
  public function setBucket(Bucket $bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return Bucket
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Immutable. Location of the default bucket.
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
   * Identifier. Resource name of the default bucket.
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
   * Immutable. Storage class of the default bucket. Supported values are
   * available at https://cloud.google.com/storage/docs/storage-classes#classes.
   *
   * @param string $storageClass
   */
  public function setStorageClass($storageClass)
  {
    $this->storageClass = $storageClass;
  }
  /**
   * @return string
   */
  public function getStorageClass()
  {
    return $this->storageClass;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DefaultBucket::class, 'Google_Service_Firebasestorage_DefaultBucket');
