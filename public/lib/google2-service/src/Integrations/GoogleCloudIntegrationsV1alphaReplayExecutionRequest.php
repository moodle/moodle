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

class GoogleCloudIntegrationsV1alphaReplayExecutionRequest extends \Google\Model
{
  /**
   * Default value.
   */
  public const REPLAY_MODE_REPLAY_MODE_UNSPECIFIED = 'REPLAY_MODE_UNSPECIFIED';
  /**
   * Replay the original execution.
   */
  public const REPLAY_MODE_REPLAY_MODE_FROM_BEGINNING = 'REPLAY_MODE_FROM_BEGINNING';
  /**
   * Replay the execution with the modified parameters.
   */
  public const REPLAY_MODE_REPLAY_MODE_POINT_OF_FAILURE = 'REPLAY_MODE_POINT_OF_FAILURE';
  protected $modifiedParametersType = GoogleCloudIntegrationsV1alphaValueType::class;
  protected $modifiedParametersDataType = 'map';
  /**
   * Optional. The mode of the replay.
   *
   * @var string
   */
  public $replayMode;
  /**
   * Required. The user provided reason for replaying the execution.
   *
   * @var string
   */
  public $replayReason;
  /**
   * Optional. The list of parameters to be updated. - If the `update_mask` is
   * not specified, all the parameters from original execution will be ignored
   * and only the `modified_parameters` will be used. - It is an error to
   * include a parameter in `update_mask` but not in `modified_parameters`. -
   * Updating nested fields in a JSON parameter is not supported, please provide
   * the complete JSON in the `modified_parameters`.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Optional. The modified input parameters for replay. - Provide values for
   * all the fields in the 'update_mask'. Any field not present in the
   * 'update_mask' will be ignored and its value will be taken from the original
   * execution. - If the 'update_mask' is not specified, all the parameters from
   * original execution will be ignored and only the `modified_parameters` will
   * be used.
   *
   * @param GoogleCloudIntegrationsV1alphaValueType[] $modifiedParameters
   */
  public function setModifiedParameters($modifiedParameters)
  {
    $this->modifiedParameters = $modifiedParameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaValueType[]
   */
  public function getModifiedParameters()
  {
    return $this->modifiedParameters;
  }
  /**
   * Optional. The mode of the replay.
   *
   * Accepted values: REPLAY_MODE_UNSPECIFIED, REPLAY_MODE_FROM_BEGINNING,
   * REPLAY_MODE_POINT_OF_FAILURE
   *
   * @param self::REPLAY_MODE_* $replayMode
   */
  public function setReplayMode($replayMode)
  {
    $this->replayMode = $replayMode;
  }
  /**
   * @return self::REPLAY_MODE_*
   */
  public function getReplayMode()
  {
    return $this->replayMode;
  }
  /**
   * Required. The user provided reason for replaying the execution.
   *
   * @param string $replayReason
   */
  public function setReplayReason($replayReason)
  {
    $this->replayReason = $replayReason;
  }
  /**
   * @return string
   */
  public function getReplayReason()
  {
    return $this->replayReason;
  }
  /**
   * Optional. The list of parameters to be updated. - If the `update_mask` is
   * not specified, all the parameters from original execution will be ignored
   * and only the `modified_parameters` will be used. - It is an error to
   * include a parameter in `update_mask` but not in `modified_parameters`. -
   * Updating nested fields in a JSON parameter is not supported, please provide
   * the complete JSON in the `modified_parameters`.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaReplayExecutionRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaReplayExecutionRequest');
