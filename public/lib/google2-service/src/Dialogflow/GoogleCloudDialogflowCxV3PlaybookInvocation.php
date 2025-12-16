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

class GoogleCloudDialogflowCxV3PlaybookInvocation extends \Google\Model
{
  /**
   * Unspecified output.
   */
  public const PLAYBOOK_STATE_OUTPUT_STATE_UNSPECIFIED = 'OUTPUT_STATE_UNSPECIFIED';
  /**
   * Succeeded.
   */
  public const PLAYBOOK_STATE_OUTPUT_STATE_OK = 'OUTPUT_STATE_OK';
  /**
   * Cancelled.
   */
  public const PLAYBOOK_STATE_OUTPUT_STATE_CANCELLED = 'OUTPUT_STATE_CANCELLED';
  /**
   * Failed.
   */
  public const PLAYBOOK_STATE_OUTPUT_STATE_FAILED = 'OUTPUT_STATE_FAILED';
  /**
   * Escalated.
   */
  public const PLAYBOOK_STATE_OUTPUT_STATE_ESCALATED = 'OUTPUT_STATE_ESCALATED';
  /**
   * Pending.
   */
  public const PLAYBOOK_STATE_OUTPUT_STATE_PENDING = 'OUTPUT_STATE_PENDING';
  /**
   * Output only. The display name of the playbook.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The unique identifier of the playbook. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @var string
   */
  public $playbook;
  protected $playbookInputType = GoogleCloudDialogflowCxV3PlaybookInput::class;
  protected $playbookInputDataType = '';
  protected $playbookOutputType = GoogleCloudDialogflowCxV3PlaybookOutput::class;
  protected $playbookOutputDataType = '';
  /**
   * Required. Playbook invocation's output state.
   *
   * @var string
   */
  public $playbookState;

  /**
   * Output only. The display name of the playbook.
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
   * Required. The unique identifier of the playbook. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @param string $playbook
   */
  public function setPlaybook($playbook)
  {
    $this->playbook = $playbook;
  }
  /**
   * @return string
   */
  public function getPlaybook()
  {
    return $this->playbook;
  }
  /**
   * Optional. Input of the child playbook invocation.
   *
   * @param GoogleCloudDialogflowCxV3PlaybookInput $playbookInput
   */
  public function setPlaybookInput(GoogleCloudDialogflowCxV3PlaybookInput $playbookInput)
  {
    $this->playbookInput = $playbookInput;
  }
  /**
   * @return GoogleCloudDialogflowCxV3PlaybookInput
   */
  public function getPlaybookInput()
  {
    return $this->playbookInput;
  }
  /**
   * Optional. Output of the child playbook invocation.
   *
   * @param GoogleCloudDialogflowCxV3PlaybookOutput $playbookOutput
   */
  public function setPlaybookOutput(GoogleCloudDialogflowCxV3PlaybookOutput $playbookOutput)
  {
    $this->playbookOutput = $playbookOutput;
  }
  /**
   * @return GoogleCloudDialogflowCxV3PlaybookOutput
   */
  public function getPlaybookOutput()
  {
    return $this->playbookOutput;
  }
  /**
   * Required. Playbook invocation's output state.
   *
   * Accepted values: OUTPUT_STATE_UNSPECIFIED, OUTPUT_STATE_OK,
   * OUTPUT_STATE_CANCELLED, OUTPUT_STATE_FAILED, OUTPUT_STATE_ESCALATED,
   * OUTPUT_STATE_PENDING
   *
   * @param self::PLAYBOOK_STATE_* $playbookState
   */
  public function setPlaybookState($playbookState)
  {
    $this->playbookState = $playbookState;
  }
  /**
   * @return self::PLAYBOOK_STATE_*
   */
  public function getPlaybookState()
  {
    return $this->playbookState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3PlaybookInvocation::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3PlaybookInvocation');
