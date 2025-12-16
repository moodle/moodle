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

namespace Google\Service\Safebrowsing;

class ThreatEntrySet extends \Google\Model
{
  /**
   * @var string
   */
  public $compressionType;
  protected $rawHashesType = RawHashes::class;
  protected $rawHashesDataType = '';
  public $rawHashes;
  protected $rawIndicesType = RawIndices::class;
  protected $rawIndicesDataType = '';
  public $rawIndices;
  protected $riceHashesType = RiceDeltaEncoding::class;
  protected $riceHashesDataType = '';
  public $riceHashes;
  protected $riceIndicesType = RiceDeltaEncoding::class;
  protected $riceIndicesDataType = '';
  public $riceIndices;

  /**
   * @param string
   */
  public function setCompressionType($compressionType)
  {
    $this->compressionType = $compressionType;
  }
  /**
   * @return string
   */
  public function getCompressionType()
  {
    return $this->compressionType;
  }
  /**
   * @param RawHashes
   */
  public function setRawHashes(RawHashes $rawHashes)
  {
    $this->rawHashes = $rawHashes;
  }
  /**
   * @return RawHashes
   */
  public function getRawHashes()
  {
    return $this->rawHashes;
  }
  /**
   * @param RawIndices
   */
  public function setRawIndices(RawIndices $rawIndices)
  {
    $this->rawIndices = $rawIndices;
  }
  /**
   * @return RawIndices
   */
  public function getRawIndices()
  {
    return $this->rawIndices;
  }
  /**
   * @param RiceDeltaEncoding
   */
  public function setRiceHashes(RiceDeltaEncoding $riceHashes)
  {
    $this->riceHashes = $riceHashes;
  }
  /**
   * @return RiceDeltaEncoding
   */
  public function getRiceHashes()
  {
    return $this->riceHashes;
  }
  /**
   * @param RiceDeltaEncoding
   */
  public function setRiceIndices(RiceDeltaEncoding $riceIndices)
  {
    $this->riceIndices = $riceIndices;
  }
  /**
   * @return RiceDeltaEncoding
   */
  public function getRiceIndices()
  {
    return $this->riceIndices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThreatEntrySet::class, 'Google_Service_Safebrowsing_ThreatEntrySet');
