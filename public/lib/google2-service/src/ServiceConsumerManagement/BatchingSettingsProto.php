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

namespace Google\Service\ServiceConsumerManagement;

class BatchingSettingsProto extends \Google\Model
{
  /**
   * Default behavior, system-defined.
   */
  public const FLOW_CONTROL_LIMIT_EXCEEDED_BEHAVIOR_UNSET_BEHAVIOR = 'UNSET_BEHAVIOR';
  /**
   * Stop operation, raise error.
   */
  public const FLOW_CONTROL_LIMIT_EXCEEDED_BEHAVIOR_THROW_EXCEPTION = 'THROW_EXCEPTION';
  /**
   * Pause operation until limit clears.
   */
  public const FLOW_CONTROL_LIMIT_EXCEEDED_BEHAVIOR_BLOCK = 'BLOCK';
  /**
   * Continue operation, disregard limit.
   */
  public const FLOW_CONTROL_LIMIT_EXCEEDED_BEHAVIOR_IGNORE = 'IGNORE';
  /**
   * The duration after which a batch should be sent, starting from the addition
   * of the first message to that batch.
   *
   * @var string
   */
  public $delayThreshold;
  /**
   * The maximum number of elements collected in a batch that could be accepted
   * by server.
   *
   * @var int
   */
  public $elementCountLimit;
  /**
   * The number of elements of a field collected into a batch which, if
   * exceeded, causes the batch to be sent.
   *
   * @var int
   */
  public $elementCountThreshold;
  /**
   * The maximum size of data allowed by flow control.
   *
   * @var int
   */
  public $flowControlByteLimit;
  /**
   * The maximum number of elements allowed by flow control.
   *
   * @var int
   */
  public $flowControlElementLimit;
  /**
   * The behavior to take when the flow control limit is exceeded.
   *
   * @var string
   */
  public $flowControlLimitExceededBehavior;
  /**
   * The maximum size of the request that could be accepted by server.
   *
   * @var int
   */
  public $requestByteLimit;
  /**
   * The aggregated size of the batched field which, if exceeded, causes the
   * batch to be sent. This size is computed by aggregating the sizes of the
   * request field to be batched, not of the entire request message.
   *
   * @var string
   */
  public $requestByteThreshold;

  /**
   * The duration after which a batch should be sent, starting from the addition
   * of the first message to that batch.
   *
   * @param string $delayThreshold
   */
  public function setDelayThreshold($delayThreshold)
  {
    $this->delayThreshold = $delayThreshold;
  }
  /**
   * @return string
   */
  public function getDelayThreshold()
  {
    return $this->delayThreshold;
  }
  /**
   * The maximum number of elements collected in a batch that could be accepted
   * by server.
   *
   * @param int $elementCountLimit
   */
  public function setElementCountLimit($elementCountLimit)
  {
    $this->elementCountLimit = $elementCountLimit;
  }
  /**
   * @return int
   */
  public function getElementCountLimit()
  {
    return $this->elementCountLimit;
  }
  /**
   * The number of elements of a field collected into a batch which, if
   * exceeded, causes the batch to be sent.
   *
   * @param int $elementCountThreshold
   */
  public function setElementCountThreshold($elementCountThreshold)
  {
    $this->elementCountThreshold = $elementCountThreshold;
  }
  /**
   * @return int
   */
  public function getElementCountThreshold()
  {
    return $this->elementCountThreshold;
  }
  /**
   * The maximum size of data allowed by flow control.
   *
   * @param int $flowControlByteLimit
   */
  public function setFlowControlByteLimit($flowControlByteLimit)
  {
    $this->flowControlByteLimit = $flowControlByteLimit;
  }
  /**
   * @return int
   */
  public function getFlowControlByteLimit()
  {
    return $this->flowControlByteLimit;
  }
  /**
   * The maximum number of elements allowed by flow control.
   *
   * @param int $flowControlElementLimit
   */
  public function setFlowControlElementLimit($flowControlElementLimit)
  {
    $this->flowControlElementLimit = $flowControlElementLimit;
  }
  /**
   * @return int
   */
  public function getFlowControlElementLimit()
  {
    return $this->flowControlElementLimit;
  }
  /**
   * The behavior to take when the flow control limit is exceeded.
   *
   * Accepted values: UNSET_BEHAVIOR, THROW_EXCEPTION, BLOCK, IGNORE
   *
   * @param self::FLOW_CONTROL_LIMIT_EXCEEDED_BEHAVIOR_* $flowControlLimitExceededBehavior
   */
  public function setFlowControlLimitExceededBehavior($flowControlLimitExceededBehavior)
  {
    $this->flowControlLimitExceededBehavior = $flowControlLimitExceededBehavior;
  }
  /**
   * @return self::FLOW_CONTROL_LIMIT_EXCEEDED_BEHAVIOR_*
   */
  public function getFlowControlLimitExceededBehavior()
  {
    return $this->flowControlLimitExceededBehavior;
  }
  /**
   * The maximum size of the request that could be accepted by server.
   *
   * @param int $requestByteLimit
   */
  public function setRequestByteLimit($requestByteLimit)
  {
    $this->requestByteLimit = $requestByteLimit;
  }
  /**
   * @return int
   */
  public function getRequestByteLimit()
  {
    return $this->requestByteLimit;
  }
  /**
   * The aggregated size of the batched field which, if exceeded, causes the
   * batch to be sent. This size is computed by aggregating the sizes of the
   * request field to be batched, not of the entire request message.
   *
   * @param string $requestByteThreshold
   */
  public function setRequestByteThreshold($requestByteThreshold)
  {
    $this->requestByteThreshold = $requestByteThreshold;
  }
  /**
   * @return string
   */
  public function getRequestByteThreshold()
  {
    return $this->requestByteThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchingSettingsProto::class, 'Google_Service_ServiceConsumerManagement_BatchingSettingsProto');
