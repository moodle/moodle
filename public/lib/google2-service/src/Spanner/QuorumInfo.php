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

namespace Google\Service\Spanner;

class QuorumInfo extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const INITIATOR_INITIATOR_UNSPECIFIED = 'INITIATOR_UNSPECIFIED';
  /**
   * `ChangeQuorum` initiated by Google.
   */
  public const INITIATOR_GOOGLE = 'GOOGLE';
  /**
   * `ChangeQuorum` initiated by User.
   */
  public const INITIATOR_USER = 'USER';
  /**
   * Output only. The etag is used for optimistic concurrency control as a way
   * to help prevent simultaneous `ChangeQuorum` requests that might create a
   * race condition.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Whether this `ChangeQuorum` is Google or User initiated.
   *
   * @var string
   */
  public $initiator;
  protected $quorumTypeType = QuorumType::class;
  protected $quorumTypeDataType = '';
  /**
   * Output only. The timestamp when the request was triggered.
   *
   * @var string
   */
  public $startTime;

  /**
   * Output only. The etag is used for optimistic concurrency control as a way
   * to help prevent simultaneous `ChangeQuorum` requests that might create a
   * race condition.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Whether this `ChangeQuorum` is Google or User initiated.
   *
   * Accepted values: INITIATOR_UNSPECIFIED, GOOGLE, USER
   *
   * @param self::INITIATOR_* $initiator
   */
  public function setInitiator($initiator)
  {
    $this->initiator = $initiator;
  }
  /**
   * @return self::INITIATOR_*
   */
  public function getInitiator()
  {
    return $this->initiator;
  }
  /**
   * Output only. The type of this quorum. See QuorumType for more information
   * about quorum type specifications.
   *
   * @param QuorumType $quorumType
   */
  public function setQuorumType(QuorumType $quorumType)
  {
    $this->quorumType = $quorumType;
  }
  /**
   * @return QuorumType
   */
  public function getQuorumType()
  {
    return $this->quorumType;
  }
  /**
   * Output only. The timestamp when the request was triggered.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuorumInfo::class, 'Google_Service_Spanner_QuorumInfo');
