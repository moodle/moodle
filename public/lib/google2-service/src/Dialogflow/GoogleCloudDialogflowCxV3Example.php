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

class GoogleCloudDialogflowCxV3Example extends \Google\Collection
{
  /**
   * Unspecified output.
   */
  public const CONVERSATION_STATE_OUTPUT_STATE_UNSPECIFIED = 'OUTPUT_STATE_UNSPECIFIED';
  /**
   * Succeeded.
   */
  public const CONVERSATION_STATE_OUTPUT_STATE_OK = 'OUTPUT_STATE_OK';
  /**
   * Cancelled.
   */
  public const CONVERSATION_STATE_OUTPUT_STATE_CANCELLED = 'OUTPUT_STATE_CANCELLED';
  /**
   * Failed.
   */
  public const CONVERSATION_STATE_OUTPUT_STATE_FAILED = 'OUTPUT_STATE_FAILED';
  /**
   * Escalated.
   */
  public const CONVERSATION_STATE_OUTPUT_STATE_ESCALATED = 'OUTPUT_STATE_ESCALATED';
  /**
   * Pending.
   */
  public const CONVERSATION_STATE_OUTPUT_STATE_PENDING = 'OUTPUT_STATE_PENDING';
  protected $collection_key = 'actions';
  protected $actionsType = GoogleCloudDialogflowCxV3Action::class;
  protected $actionsDataType = 'array';
  /**
   * Required. Example's output state.
   *
   * @var string
   */
  public $conversationState;
  /**
   * Output only. The timestamp of initial example creation.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The high level concise description of the example. The max number
   * of characters is 200.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the example.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. The language code of the example. If not specified, the agent's
   * default language is used. Note: languages must be enabled in the agent
   * before they can be used. Note: example's language code is not currently
   * used in dialogflow agents.
   *
   * @var string
   */
  public $languageCode;
  /**
   * The unique identifier of the playbook example. Format:
   * `projects//locations//agents//playbooks//examples/`.
   *
   * @var string
   */
  public $name;
  protected $playbookInputType = GoogleCloudDialogflowCxV3PlaybookInput::class;
  protected $playbookInputDataType = '';
  protected $playbookOutputType = GoogleCloudDialogflowCxV3PlaybookOutput::class;
  protected $playbookOutputDataType = '';
  /**
   * Output only. Estimated number of tokes current example takes when sent to
   * the LLM.
   *
   * @var string
   */
  public $tokenCount;
  /**
   * Output only. Last time the example was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The ordered list of actions performed by the end user and the
   * Dialogflow agent.
   *
   * @param GoogleCloudDialogflowCxV3Action[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Action[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Required. Example's output state.
   *
   * Accepted values: OUTPUT_STATE_UNSPECIFIED, OUTPUT_STATE_OK,
   * OUTPUT_STATE_CANCELLED, OUTPUT_STATE_FAILED, OUTPUT_STATE_ESCALATED,
   * OUTPUT_STATE_PENDING
   *
   * @param self::CONVERSATION_STATE_* $conversationState
   */
  public function setConversationState($conversationState)
  {
    $this->conversationState = $conversationState;
  }
  /**
   * @return self::CONVERSATION_STATE_*
   */
  public function getConversationState()
  {
    return $this->conversationState;
  }
  /**
   * Output only. The timestamp of initial example creation.
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
   * Optional. The high level concise description of the example. The max number
   * of characters is 200.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the example.
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
   * Optional. The language code of the example. If not specified, the agent's
   * default language is used. Note: languages must be enabled in the agent
   * before they can be used. Note: example's language code is not currently
   * used in dialogflow agents.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The unique identifier of the playbook example. Format:
   * `projects//locations//agents//playbooks//examples/`.
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
   * Optional. The input to the playbook in the example.
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
   * Optional. The output of the playbook in the example.
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
   * Output only. Estimated number of tokes current example takes when sent to
   * the LLM.
   *
   * @param string $tokenCount
   */
  public function setTokenCount($tokenCount)
  {
    $this->tokenCount = $tokenCount;
  }
  /**
   * @return string
   */
  public function getTokenCount()
  {
    return $this->tokenCount;
  }
  /**
   * Output only. Last time the example was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Example::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Example');
