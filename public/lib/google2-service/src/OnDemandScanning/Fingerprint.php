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

namespace Google\Service\OnDemandScanning;

class Fingerprint extends \Google\Collection
{
  protected $collection_key = 'v2Blob';
  /**
   * Required. The layer ID of the final layer in the Docker image's v1
   * representation.
   *
   * @var string
   */
  public $v1Name;
  /**
   * Required. The ordered list of v2 blobs that represent a given image.
   *
   * @var string[]
   */
  public $v2Blob;
  /**
   * Output only. The name of the image's v2 blobs computed via: [bottom] :=
   * v2_blobbottom := sha256(v2_blob[N] + " " + v2_name[N+1]) Only the name of
   * the final blob is kept.
   *
   * @var string
   */
  public $v2Name;

  /**
   * Required. The layer ID of the final layer in the Docker image's v1
   * representation.
   *
   * @param string $v1Name
   */
  public function setV1Name($v1Name)
  {
    $this->v1Name = $v1Name;
  }
  /**
   * @return string
   */
  public function getV1Name()
  {
    return $this->v1Name;
  }
  /**
   * Required. The ordered list of v2 blobs that represent a given image.
   *
   * @param string[] $v2Blob
   */
  public function setV2Blob($v2Blob)
  {
    $this->v2Blob = $v2Blob;
  }
  /**
   * @return string[]
   */
  public function getV2Blob()
  {
    return $this->v2Blob;
  }
  /**
   * Output only. The name of the image's v2 blobs computed via: [bottom] :=
   * v2_blobbottom := sha256(v2_blob[N] + " " + v2_name[N+1]) Only the name of
   * the final blob is kept.
   *
   * @param string $v2Name
   */
  public function setV2Name($v2Name)
  {
    $this->v2Name = $v2Name;
  }
  /**
   * @return string
   */
  public function getV2Name()
  {
    return $this->v2Name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Fingerprint::class, 'Google_Service_OnDemandScanning_Fingerprint');
