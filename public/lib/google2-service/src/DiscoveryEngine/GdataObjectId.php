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

namespace Google\Service\DiscoveryEngine;

class GdataObjectId extends \Google\Model
{
  /**
   * The name of the bucket to which this object belongs.
   *
   * @var string
   */
  public $bucketName;
  /**
   * Generation of the object. Generations are monotonically increasing across
   * writes, allowing them to be be compared to determine which generation is
   * newer. If this is omitted in a request, then you are requesting the live
   * object. See http://go/bigstore-versions
   *
   * @var string
   */
  public $generation;
  /**
   * The name of the object.
   *
   * @var string
   */
  public $objectName;

  /**
   * The name of the bucket to which this object belongs.
   *
   * @param string $bucketName
   */
  public function setBucketName($bucketName)
  {
    $this->bucketName = $bucketName;
  }
  /**
   * @return string
   */
  public function getBucketName()
  {
    return $this->bucketName;
  }
  /**
   * Generation of the object. Generations are monotonically increasing across
   * writes, allowing them to be be compared to determine which generation is
   * newer. If this is omitted in a request, then you are requesting the live
   * object. See http://go/bigstore-versions
   *
   * @param string $generation
   */
  public function setGeneration($generation)
  {
    $this->generation = $generation;
  }
  /**
   * @return string
   */
  public function getGeneration()
  {
    return $this->generation;
  }
  /**
   * The name of the object.
   *
   * @param string $objectName
   */
  public function setObjectName($objectName)
  {
    $this->objectName = $objectName;
  }
  /**
   * @return string
   */
  public function getObjectName()
  {
    return $this->objectName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GdataObjectId::class, 'Google_Service_DiscoveryEngine_GdataObjectId');
