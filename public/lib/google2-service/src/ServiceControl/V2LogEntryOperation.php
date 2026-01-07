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

namespace Google\Service\ServiceControl;

class V2LogEntryOperation extends \Google\Model
{
  /**
   * Optional. Set this to True if this is the first log entry in the operation.
   *
   * @var bool
   */
  public $first;
  /**
   * Optional. An arbitrary operation identifier. Log entries with the same
   * identifier are assumed to be part of the same operation.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. Set this to True if this is the last log entry in the operation.
   *
   * @var bool
   */
  public $last;
  /**
   * Optional. An arbitrary producer identifier. The combination of `id` and
   * `producer` must be globally unique. Examples for `producer`:
   * `"MyDivision.MyBigCompany.com"`, `"github.com/MyProject/MyApplication"`.
   *
   * @var string
   */
  public $producer;

  /**
   * Optional. Set this to True if this is the first log entry in the operation.
   *
   * @param bool $first
   */
  public function setFirst($first)
  {
    $this->first = $first;
  }
  /**
   * @return bool
   */
  public function getFirst()
  {
    return $this->first;
  }
  /**
   * Optional. An arbitrary operation identifier. Log entries with the same
   * identifier are assumed to be part of the same operation.
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
   * Optional. Set this to True if this is the last log entry in the operation.
   *
   * @param bool $last
   */
  public function setLast($last)
  {
    $this->last = $last;
  }
  /**
   * @return bool
   */
  public function getLast()
  {
    return $this->last;
  }
  /**
   * Optional. An arbitrary producer identifier. The combination of `id` and
   * `producer` must be globally unique. Examples for `producer`:
   * `"MyDivision.MyBigCompany.com"`, `"github.com/MyProject/MyApplication"`.
   *
   * @param string $producer
   */
  public function setProducer($producer)
  {
    $this->producer = $producer;
  }
  /**
   * @return string
   */
  public function getProducer()
  {
    return $this->producer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V2LogEntryOperation::class, 'Google_Service_ServiceControl_V2LogEntryOperation');
