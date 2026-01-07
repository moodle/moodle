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

class GoogleCloudWebriskV1ThreatEntryAdditions extends \Google\Collection
{
  protected $collection_key = 'rawHashes';
  protected $rawHashesType = GoogleCloudWebriskV1RawHashes::class;
  protected $rawHashesDataType = 'array';
  protected $riceHashesType = GoogleCloudWebriskV1RiceDeltaEncoding::class;
  protected $riceHashesDataType = '';

  /**
   * The raw SHA256-formatted entries. Repeated to allow returning sets of
   * hashes with different prefix sizes.
   *
   * @param GoogleCloudWebriskV1RawHashes[] $rawHashes
   */
  public function setRawHashes($rawHashes)
  {
    $this->rawHashes = $rawHashes;
  }
  /**
   * @return GoogleCloudWebriskV1RawHashes[]
   */
  public function getRawHashes()
  {
    return $this->rawHashes;
  }
  /**
   * The encoded 4-byte prefixes of SHA256-formatted entries, using a Golomb-
   * Rice encoding. The hashes are converted to uint32, sorted in ascending
   * order, then delta encoded and stored as encoded_data.
   *
   * @param GoogleCloudWebriskV1RiceDeltaEncoding $riceHashes
   */
  public function setRiceHashes(GoogleCloudWebriskV1RiceDeltaEncoding $riceHashes)
  {
    $this->riceHashes = $riceHashes;
  }
  /**
   * @return GoogleCloudWebriskV1RiceDeltaEncoding
   */
  public function getRiceHashes()
  {
    return $this->riceHashes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudWebriskV1ThreatEntryAdditions::class, 'Google_Service_WebRisk_GoogleCloudWebriskV1ThreatEntryAdditions');
