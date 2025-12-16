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

namespace Google\Service\Firestore;

class BloomFilter extends \Google\Model
{
  protected $bitsType = BitSequence::class;
  protected $bitsDataType = '';
  /**
   * The number of hashes used by the algorithm.
   *
   * @var int
   */
  public $hashCount;

  /**
   * The bloom filter data.
   *
   * @param BitSequence $bits
   */
  public function setBits(BitSequence $bits)
  {
    $this->bits = $bits;
  }
  /**
   * @return BitSequence
   */
  public function getBits()
  {
    return $this->bits;
  }
  /**
   * The number of hashes used by the algorithm.
   *
   * @param int $hashCount
   */
  public function setHashCount($hashCount)
  {
    $this->hashCount = $hashCount;
  }
  /**
   * @return int
   */
  public function getHashCount()
  {
    return $this->hashCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BloomFilter::class, 'Google_Service_Firestore_BloomFilter');
