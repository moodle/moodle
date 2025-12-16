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

namespace Google\Service\WebRisk;

class GoogleCloudWebriskV1RiceDeltaEncoding extends \Google\Model
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
  public $entryCount;
  /**
   * The offset of the first entry in the encoded data, or, if only a single
   * integer was encoded, that single integer's value. If the field is empty or
   * missing, assume zero.
   *
   * @var string
   */
  public $firstValue;
  /**
   * The Golomb-Rice parameter, which is a number between 2 and 28. This field
   * is missing (that is, zero) if `num_entries` is zero.
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
   * @param int $entryCount
   */
  public function setEntryCount($entryCount)
  {
    $this->entryCount = $entryCount;
  }
  /**
   * @return int
   */
  public function getEntryCount()
  {
    return $this->entryCount;
  }
  /**
   * The offset of the first entry in the encoded data, or, if only a single
   * integer was encoded, that single integer's value. If the field is empty or
   * missing, assume zero.
   *
   * @param string $firstValue
   */
  public function setFirstValue($firstValue)
  {
    $this->firstValue = $firstValue;
  }
  /**
   * @return string
   */
  public function getFirstValue()
  {
    return $this->firstValue;
  }
  /**
   * The Golomb-Rice parameter, which is a number between 2 and 28. This field
   * is missing (that is, zero) if `num_entries` is zero.
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
class_alias(GoogleCloudWebriskV1RiceDeltaEncoding::class, 'Google_Service_WebRisk_GoogleCloudWebriskV1RiceDeltaEncoding');
