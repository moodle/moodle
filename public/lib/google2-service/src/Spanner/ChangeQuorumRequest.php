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

class ChangeQuorumRequest extends \Google\Model
{
  /**
   * Optional. The etag is the hash of the `QuorumInfo`. The `ChangeQuorum`
   * operation is only performed if the etag matches that of the `QuorumInfo` in
   * the current database resource. Otherwise the API returns an `ABORTED`
   * error. The etag is used for optimistic concurrency control as a way to help
   * prevent simultaneous change quorum requests that could create a race
   * condition.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. Name of the database in which to apply `ChangeQuorum`. Values are
   * of the form `projects//instances//databases/`.
   *
   * @var string
   */
  public $name;
  protected $quorumTypeType = QuorumType::class;
  protected $quorumTypeDataType = '';

  /**
   * Optional. The etag is the hash of the `QuorumInfo`. The `ChangeQuorum`
   * operation is only performed if the etag matches that of the `QuorumInfo` in
   * the current database resource. Otherwise the API returns an `ABORTED`
   * error. The etag is used for optimistic concurrency control as a way to help
   * prevent simultaneous change quorum requests that could create a race
   * condition.
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
   * Required. Name of the database in which to apply `ChangeQuorum`. Values are
   * of the form `projects//instances//databases/`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The type of this quorum.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChangeQuorumRequest::class, 'Google_Service_Spanner_ChangeQuorumRequest');
