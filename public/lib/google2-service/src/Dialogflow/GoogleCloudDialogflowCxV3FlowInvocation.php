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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3FlowInvocation extends \Google\Model
{
  /**
   * Unspecified output.
   */
  public const FLOW_STATE_OUTPUT_STATE_UNSPECIFIED = 'OUTPUT_STATE_UNSPECIFIED';
  /**
   * Succeeded.
   */
  public const FLOW_STATE_OUTPUT_STATE_OK = 'OUTPUT_STATE_OK';
  /**
   * Cancelled.
   */
  public const FLOW_STATE_OUTPUT_STATE_CANCELLED = 'OUTPUT_STATE_CANCELLED';
  /**
   * Failed.
   */
  public const FLOW_STATE_OUTPUT_STATE_FAILED = 'OUTPUT_STATE_FAILED';
  /**
   * Escalated.
   */
  public const FLOW_STATE_OUTPUT_STATE_ESCALATED = 'OUTPUT_STATE_ESCALATED';
  /**
   * Pending.
   */
  public const FLOW_STATE_OUTPUT_STATE_PENDING = 'OUTPUT_STATE_PENDING';
  /**
   * Output only. The display name of the flow.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The unique identifier of the flow. Format:
   * `projects//locations//agents//flows/`.
   *
   * @var string
   */
  public $flow;
  /**
   * Required. Flow invocation's output state.
   *
   * @var string
   */
  public $flowState;

  /**
   * Output only. The display name of the flow.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. The unique identifier of the flow. Format:
   * `projects//locations//agents//flows/`.
   *
   * @param string $flow
   */
  public function setFlow($flow)
  {
    $this->flow = $flow;
  }
  /**
   * @return string
   */
  public function getFlow()
  {
    return $this->flow;
  }
  /**
   * Required. Flow invocation's output state.
   *
   * Accepted values: OUTPUT_STATE_UNSPECIFIED, OUTPUT_STATE_OK,
   * OUTPUT_STATE_CANCELLED, OUTPUT_STATE_FAILED, OUTPUT_STATE_ESCALATED,
   * OUTPUT_STATE_PENDING
   *
   * @param self::FLOW_STATE_* $flowState
   */
  public function setFlowState($flowState)
  {
    $this->flowState = $flowState;
  }
  /**
   * @return self::FLOW_STATE_*
   */
  public function getFlowState()
  {
    return $this->flowState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3FlowInvocation::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3FlowInvocation');
