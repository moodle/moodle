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

namespace Google\Service\FirebaseAppDistribution;

class GdataDiffChecksumsResponse extends \Google\Model
{
  protected $checksumsLocationType = GdataCompositeMedia::class;
  protected $checksumsLocationDataType = '';
  /**
   * The chunk size of checksums. Must be a multiple of 256KB.
   *
   * @var string
   */
  public $chunkSizeBytes;
  protected $objectLocationType = GdataCompositeMedia::class;
  protected $objectLocationDataType = '';
  /**
   * The total size of the server object.
   *
   * @var string
   */
  public $objectSizeBytes;
  /**
   * The object version of the object the checksums are being returned for.
   *
   * @var string
   */
  public $objectVersion;

  /**
   * Exactly one of these fields must be populated. If checksums_location is
   * filled, the server will return the corresponding contents to the user. If
   * object_location is filled, the server will calculate the checksums based on
   * the content there and return that to the user. For details on the format of
   * the checksums, see http://go/scotty-diff-protocol.
   *
   * @param GdataCompositeMedia $checksumsLocation
   */
  public function setChecksumsLocation(GdataCompositeMedia $checksumsLocation)
  {
    $this->checksumsLocation = $checksumsLocation;
  }
  /**
   * @return GdataCompositeMedia
   */
  public function getChecksumsLocation()
  {
    return $this->checksumsLocation;
  }
  /**
   * The chunk size of checksums. Must be a multiple of 256KB.
   *
   * @param string $chunkSizeBytes
   */
  public function setChunkSizeBytes($chunkSizeBytes)
  {
    $this->chunkSizeBytes = $chunkSizeBytes;
  }
  /**
   * @return string
   */
  public function getChunkSizeBytes()
  {
    return $this->chunkSizeBytes;
  }
  /**
   * If set, calculate the checksums based on the contents and return them to
   * the caller.
   *
   * @param GdataCompositeMedia $objectLocation
   */
  public function setObjectLocation(GdataCompositeMedia $objectLocation)
  {
    $this->objectLocation = $objectLocation;
  }
  /**
   * @return GdataCompositeMedia
   */
  public function getObjectLocation()
  {
    return $this->objectLocation;
  }
  /**
   * The total size of the server object.
   *
   * @param string $objectSizeBytes
   */
  public function setObjectSizeBytes($objectSizeBytes)
  {
    $this->objectSizeBytes = $objectSizeBytes;
  }
  /**
   * @return string
   */
  public function getObjectSizeBytes()
  {
    return $this->objectSizeBytes;
  }
  /**
   * The object version of the object the checksums are being returned for.
   *
   * @param string $objectVersion
   */
  public function setObjectVersion($objectVersion)
  {
    $this->objectVersion = $objectVersion;
  }
  /**
   * @return string
   */
  public function getObjectVersion()
  {
    return $this->objectVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GdataDiffChecksumsResponse::class, 'Google_Service_FirebaseAppDistribution_GdataDiffChecksumsResponse');
