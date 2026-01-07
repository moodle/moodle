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

namespace Google\Service\Dns;

class Change extends \Google\Collection
{
  public const STATUS_pending = 'pending';
  public const STATUS_done = 'done';
  protected $collection_key = 'deletions';
  protected $additionsType = ResourceRecordSet::class;
  protected $additionsDataType = 'array';
  protected $deletionsType = ResourceRecordSet::class;
  protected $deletionsDataType = 'array';
  /**
   * Unique identifier for the resource; defined by the server (output only).
   *
   * @var string
   */
  public $id;
  /**
   * If the DNS queries for the zone will be served.
   *
   * @var bool
   */
  public $isServing;
  /**
   * @var string
   */
  public $kind;
  /**
   * The time that this operation was started by the server (output only). This
   * is in RFC3339 text format.
   *
   * @var string
   */
  public $startTime;
  /**
   * Status of the operation (output only). A status of "done" means that the
   * request to update the authoritative servers has been sent, but the servers
   * might not be updated yet.
   *
   * @var string
   */
  public $status;

  /**
   * Which ResourceRecordSets to add?
   *
   * @param ResourceRecordSet[] $additions
   */
  public function setAdditions($additions)
  {
    $this->additions = $additions;
  }
  /**
   * @return ResourceRecordSet[]
   */
  public function getAdditions()
  {
    return $this->additions;
  }
  /**
   * Which ResourceRecordSets to remove? Must match existing data exactly.
   *
   * @param ResourceRecordSet[] $deletions
   */
  public function setDeletions($deletions)
  {
    $this->deletions = $deletions;
  }
  /**
   * @return ResourceRecordSet[]
   */
  public function getDeletions()
  {
    return $this->deletions;
  }
  /**
   * Unique identifier for the resource; defined by the server (output only).
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * If the DNS queries for the zone will be served.
   *
   * @param bool $isServing
   */
  public function setIsServing($isServing)
  {
    $this->isServing = $isServing;
  }
  /**
   * @return bool
   */
  public function getIsServing()
  {
    return $this->isServing;
  }
  /**
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The time that this operation was started by the server (output only). This
   * is in RFC3339 text format.
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
  /**
   * Status of the operation (output only). A status of "done" means that the
   * request to update the authoritative servers has been sent, but the servers
   * might not be updated yet.
   *
   * Accepted values: pending, done
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Change::class, 'Google_Service_Dns_Change');
