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

class GoogleCloudWebriskV1ComputeThreatListDiffResponse extends \Google\Model
{
  /**
   * Unknown.
   */
  public const RESPONSE_TYPE_RESPONSE_TYPE_UNSPECIFIED = 'RESPONSE_TYPE_UNSPECIFIED';
  /**
   * Partial updates are applied to the client's existing local database.
   */
  public const RESPONSE_TYPE_DIFF = 'DIFF';
  /**
   * Full updates resets the client's entire local database. This means that
   * either the client had no state, was seriously out-of-date, or the client is
   * believed to be corrupt.
   */
  public const RESPONSE_TYPE_RESET = 'RESET';
  protected $additionsType = GoogleCloudWebriskV1ThreatEntryAdditions::class;
  protected $additionsDataType = '';
  protected $checksumType = GoogleCloudWebriskV1ComputeThreatListDiffResponseChecksum::class;
  protected $checksumDataType = '';
  /**
   * The new opaque client version token. This should be retained by the client
   * and passed into the next call of ComputeThreatListDiff as 'version_token'.
   * A separate version token should be stored and used for each threatList.
   *
   * @var string
   */
  public $newVersionToken;
  /**
   * The soonest the client should wait before issuing any diff request.
   * Querying sooner is unlikely to produce a meaningful diff. Waiting longer is
   * acceptable considering the use case. If this field is not set clients may
   * update as soon as they want.
   *
   * @var string
   */
  public $recommendedNextDiff;
  protected $removalsType = GoogleCloudWebriskV1ThreatEntryRemovals::class;
  protected $removalsDataType = '';
  /**
   * The type of response. This may indicate that an action must be taken by the
   * client when the response is received.
   *
   * @var string
   */
  public $responseType;

  /**
   * A set of entries to add to a local threat type's list.
   *
   * @param GoogleCloudWebriskV1ThreatEntryAdditions $additions
   */
  public function setAdditions(GoogleCloudWebriskV1ThreatEntryAdditions $additions)
  {
    $this->additions = $additions;
  }
  /**
   * @return GoogleCloudWebriskV1ThreatEntryAdditions
   */
  public function getAdditions()
  {
    return $this->additions;
  }
  /**
   * The expected SHA256 hash of the client state; that is, of the sorted list
   * of all hashes present in the database after applying the provided diff. If
   * the client state doesn't match the expected state, the client must discard
   * this diff and retry later.
   *
   * @param GoogleCloudWebriskV1ComputeThreatListDiffResponseChecksum $checksum
   */
  public function setChecksum(GoogleCloudWebriskV1ComputeThreatListDiffResponseChecksum $checksum)
  {
    $this->checksum = $checksum;
  }
  /**
   * @return GoogleCloudWebriskV1ComputeThreatListDiffResponseChecksum
   */
  public function getChecksum()
  {
    return $this->checksum;
  }
  /**
   * The new opaque client version token. This should be retained by the client
   * and passed into the next call of ComputeThreatListDiff as 'version_token'.
   * A separate version token should be stored and used for each threatList.
   *
   * @param string $newVersionToken
   */
  public function setNewVersionToken($newVersionToken)
  {
    $this->newVersionToken = $newVersionToken;
  }
  /**
   * @return string
   */
  public function getNewVersionToken()
  {
    return $this->newVersionToken;
  }
  /**
   * The soonest the client should wait before issuing any diff request.
   * Querying sooner is unlikely to produce a meaningful diff. Waiting longer is
   * acceptable considering the use case. If this field is not set clients may
   * update as soon as they want.
   *
   * @param string $recommendedNextDiff
   */
  public function setRecommendedNextDiff($recommendedNextDiff)
  {
    $this->recommendedNextDiff = $recommendedNextDiff;
  }
  /**
   * @return string
   */
  public function getRecommendedNextDiff()
  {
    return $this->recommendedNextDiff;
  }
  /**
   * A set of entries to remove from a local threat type's list. This field may
   * be empty.
   *
   * @param GoogleCloudWebriskV1ThreatEntryRemovals $removals
   */
  public function setRemovals(GoogleCloudWebriskV1ThreatEntryRemovals $removals)
  {
    $this->removals = $removals;
  }
  /**
   * @return GoogleCloudWebriskV1ThreatEntryRemovals
   */
  public function getRemovals()
  {
    return $this->removals;
  }
  /**
   * The type of response. This may indicate that an action must be taken by the
   * client when the response is received.
   *
   * Accepted values: RESPONSE_TYPE_UNSPECIFIED, DIFF, RESET
   *
   * @param self::RESPONSE_TYPE_* $responseType
   */
  public function setResponseType($responseType)
  {
    $this->responseType = $responseType;
  }
  /**
   * @return self::RESPONSE_TYPE_*
   */
  public function getResponseType()
  {
    return $this->responseType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudWebriskV1ComputeThreatListDiffResponse::class, 'Google_Service_WebRisk_GoogleCloudWebriskV1ComputeThreatListDiffResponse');
