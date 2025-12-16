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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2MemoryHashSignature extends \Google\Collection
{
  protected $collection_key = 'detections';
  /**
   * The binary family.
   *
   * @var string
   */
  public $binaryFamily;
  protected $detectionsType = GoogleCloudSecuritycenterV2Detection::class;
  protected $detectionsDataType = 'array';

  /**
   * The binary family.
   *
   * @param string $binaryFamily
   */
  public function setBinaryFamily($binaryFamily)
  {
    $this->binaryFamily = $binaryFamily;
  }
  /**
   * @return string
   */
  public function getBinaryFamily()
  {
    return $this->binaryFamily;
  }
  /**
   * The list of memory hash detections contributing to the binary family match.
   *
   * @param GoogleCloudSecuritycenterV2Detection[] $detections
   */
  public function setDetections($detections)
  {
    $this->detections = $detections;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Detection[]
   */
  public function getDetections()
  {
    return $this->detections;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2MemoryHashSignature::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2MemoryHashSignature');
