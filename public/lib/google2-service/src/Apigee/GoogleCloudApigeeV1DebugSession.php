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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1DebugSession extends \Google\Model
{
  /**
   * Optional. The number of request to be traced. Min = 1, Max = 15, Default =
   * 10.
   *
   * @var int
   */
  public $count;
  /**
   * Output only. The first transaction creation timestamp, recorded by UAP.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A conditional statement which is evaluated against the request
   * message to determine if it should be traced. Syntax matches that of on API
   * Proxy bundle flow Condition.
   *
   * @var string
   */
  public $filter;
  /**
   * A unique ID for this DebugSession.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The time in seconds after which this DebugSession should end.
   * This value will override the value in query param, if both are provided.
   *
   * @var string
   */
  public $timeout;
  /**
   * Optional. The maximum number of bytes captured from the response payload.
   * Min = 0, Max = 5120, Default = 5120.
   *
   * @var int
   */
  public $tracesize;
  /**
   * Optional. The length of time, in seconds, that this debug session is valid,
   * starting from when it's received in the control plane. Min = 1, Max = 15,
   * Default = 10.
   *
   * @var int
   */
  public $validity;

  /**
   * Optional. The number of request to be traced. Min = 1, Max = 15, Default =
   * 10.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Output only. The first transaction creation timestamp, recorded by UAP.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. A conditional statement which is evaluated against the request
   * message to determine if it should be traced. Syntax matches that of on API
   * Proxy bundle flow Condition.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * A unique ID for this DebugSession.
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
   * Optional. The time in seconds after which this DebugSession should end.
   * This value will override the value in query param, if both are provided.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Optional. The maximum number of bytes captured from the response payload.
   * Min = 0, Max = 5120, Default = 5120.
   *
   * @param int $tracesize
   */
  public function setTracesize($tracesize)
  {
    $this->tracesize = $tracesize;
  }
  /**
   * @return int
   */
  public function getTracesize()
  {
    return $this->tracesize;
  }
  /**
   * Optional. The length of time, in seconds, that this debug session is valid,
   * starting from when it's received in the control plane. Min = 1, Max = 15,
   * Default = 10.
   *
   * @param int $validity
   */
  public function setValidity($validity)
  {
    $this->validity = $validity;
  }
  /**
   * @return int
   */
  public function getValidity()
  {
    return $this->validity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DebugSession::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DebugSession');
