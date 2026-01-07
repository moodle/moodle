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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoExecutionTraceInfo extends \Google\Model
{
  /**
   * Parent event execution info id that triggers the current execution through
   * SubWorkflowExecutorTask.
   *
   * @var string
   */
  public $parentEventExecutionInfoId;
  /**
   * Used to aggregate ExecutionTraceInfo.
   *
   * @var string
   */
  public $traceId;

  /**
   * Parent event execution info id that triggers the current execution through
   * SubWorkflowExecutorTask.
   *
   * @param string $parentEventExecutionInfoId
   */
  public function setParentEventExecutionInfoId($parentEventExecutionInfoId)
  {
    $this->parentEventExecutionInfoId = $parentEventExecutionInfoId;
  }
  /**
   * @return string
   */
  public function getParentEventExecutionInfoId()
  {
    return $this->parentEventExecutionInfoId;
  }
  /**
   * Used to aggregate ExecutionTraceInfo.
   *
   * @param string $traceId
   */
  public function setTraceId($traceId)
  {
    $this->traceId = $traceId;
  }
  /**
   * @return string
   */
  public function getTraceId()
  {
    return $this->traceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoExecutionTraceInfo::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoExecutionTraceInfo');
