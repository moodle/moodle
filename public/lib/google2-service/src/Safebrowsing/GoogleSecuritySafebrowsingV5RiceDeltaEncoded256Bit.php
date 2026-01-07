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

class GoogleSecuritySafebrowsingV5RiceDeltaEncoded256Bit extends \Google\Model
{
  /**
   * The encoded deltas that are encoded using the Golomb-Rice coder.
   *
   * @var string
   */
  public $encodedData;
  /**
   * The number of entries that are delta encoded in the encoded data. If only a
   * single integer was encoded, this will be zero and the single value will be
   * stored in `first_value`.
   *
   * @var int
   */
  public $entriesCount;
  /**
   * The first 64 bits of the first entry in the encoded data (hashes). If the
   * field is empty, the first 64 bits are all zero.
   *
   * @var string
   */
  public $firstValueFirstPart;
  /**
   * The last 64 bits of the first entry in the encoded data (hashes). If the
   * field is empty, the last 64 bits are all zero.
   *
   * @var string
   */
  public $firstValueFourthPart;
  /**
   * The 65 through 128th bits of the first entry in the encoded data (hashes).
   * If the field is empty, the 65 through 128th bits are all zero.
   *
   * @var string
   */
  public $firstValueSecondPart;
  /**
   * The 129 through 192th bits of the first entry in the encoded data (hashes).
   * If the field is empty, the 129 through 192th bits are all zero.
   *
   * @var string
   */
  public $firstValueThirdPart;
  /**
   * The Golomb-Rice parameter. This parameter is guaranteed to be between 227
   * and 254, inclusive.
   *
   * @var int
   */
  public $riceParameter;

  /**
   * The encoded deltas that are encoded using the Golomb-Rice coder.
   *
   * @param string $encodedData
   */
  public function setEncodedData($encodedData)
  {
    $this->encodedData = $encodedData;
  }
  /**
   * @return string
   */
  public function getEncodedData()
  {
    return $this->encodedData;
  }
  /**
   * The number of entries that are delta encoded in the encoded data. If only a
   * single integer was encoded, this will be zero and the single value will be
   * stored in `first_value`.
   *
   * @param int $entriesCount
   */
  public function setEntriesCount($entriesCount)
  {
    $this->entriesCount = $entriesCount;
  }
  /**
   * @return int
   */
  public function getEntriesCount()
  {
    return $this->entriesCount;
  }
  /**
   * The first 64 bits of the first entry in the encoded data (hashes). If the
   * field is empty, the first 64 bits are all zero.
   *
   * @param string $firstValueFirstPart
   */
  public function setFirstValueFirstPart($firstValueFirstPart)
  {
    $this->firstValueFirstPart = $firstValueFirstPart;
  }
  /**
   * @return string
   */
  public function getFirstValueFirstPart()
  {
    return $this->firstValueFirstPart;
  }
  /**
   * The last 64 bits of the first entry in the encoded data (hashes). If the
   * field is empty, the last 64 bits are all zero.
   *
   * @param string $firstValueFourthPart
   */
  public function setFirstValueFourthPart($firstValueFourthPart)
  {
    $this->firstValueFourthPart = $firstValueFourthPart;
  }
  /**
   * @return string
   */
  public function getFirstValueFourthPart()
  {
    return $this->firstValueFourthPart;
  }
  /**
   * The 65 through 128th bits of the first entry in the encoded data (hashes).
   * If the field is empty, the 65 through 128th bits are all zero.
   *
   * @param string $firstValueSecondPart
   */
  public function setFirstValueSecondPart($firstValueSecondPart)
  {
    $this->firstValueSecondPart = $firstValueSecondPart;
  }
  /**
   * @return string
   */
  public function getFirstValueSecondPart()
  {
    return $this->firstValueSecondPart;
  }
  /**
   * The 129 through 192th bits of the first entry in the encoded data (hashes).
   * If the field is empty, the 129 through 192th bits are all zero.
   *
   * @param string $firstValueThirdPart
   */
  public function setFirstValueThirdPart($firstValueThirdPart)
  {
    $this->firstValueThirdPart = $firstValueThirdPart;
  }
  /**
   * @return string
   */
  public function getFirstValueThirdPart()
  {
    return $this->firstValueThirdPart;
  }
  /**
   * The Golomb-Rice parameter. This parameter is guaranteed to be between 227
   * and 254, inclusive.
   *
   * @param int $riceParameter
   */
  public function setRiceParameter($riceParameter)
  {
    $this->riceParameter = $riceParameter;
  }
  /**
   * @return int
   */
  public function getRiceParameter()
  {
    return $this->riceParameter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleSecuritySafebrowsingV5RiceDeltaEncoded256Bit::class, 'Google_Service_Safebrowsing_GoogleSecuritySafebrowsingV5RiceDeltaEncoded256Bit');
