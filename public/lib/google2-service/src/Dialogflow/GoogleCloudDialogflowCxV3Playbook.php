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

class GoogleCloudDialogflowCxV3Playbook extends \Google\Collection
{
  /**
   * Unspecified type. Default to TASK.
   */
  public const PLAYBOOK_TYPE_PLAYBOOK_TYPE_UNSPECIFIED = 'PLAYBOOK_TYPE_UNSPECIFIED';
  /**
   * Task playbook.
   */
  public const PLAYBOOK_TYPE_TASK = 'TASK';
  /**
   * Routine playbook.
   */
  public const PLAYBOOK_TYPE_ROUTINE = 'ROUTINE';
  protected $collection_key = 'referencedTools';
  protected $codeBlockType = GoogleCloudDialogflowCxV3CodeBlock::class;
  protected $codeBlockDataType = '';
  /**
   * Output only. The timestamp of initial playbook creation.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The human-readable name of the playbook, unique within an agent.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. High level description of the goal the playbook intend to
   * accomplish. A goal should be concise since it's visible to other playbooks
   * that may reference this playbook.
   *
   * @var string
   */
  public $goal;
  protected $handlersType = GoogleCloudDialogflowCxV3Handler::class;
  protected $handlersDataType = 'array';
  /**
   * Optional. Output only. Names of inline actions scoped to this playbook.
   * These actions are in addition to those belonging to referenced tools, child
   * playbooks, and flows, e.g. actions that are defined in the playbook's code
   * block.
   *
   * @var string[]
   */
  public $inlineActions;
  protected $inputParameterDefinitionsType = GoogleCloudDialogflowCxV3ParameterDefinition::class;
  protected $inputParameterDefinitionsDataType = 'array';
  protected $instructionType = GoogleCloudDialogflowCxV3PlaybookInstruction::class;
  protected $instructionDataType = '';
  protected $llmModelSettingsType = GoogleCloudDialogflowCxV3LlmModelSettings::class;
  protected $llmModelSettingsDataType = '';
  /**
   * The unique identifier of the playbook. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @var string
   */
  public $name;
  protected $outputParameterDefinitionsType = GoogleCloudDialogflowCxV3ParameterDefinition::class;
  protected $outputParameterDefinitionsDataType = 'array';
  /**
   * Optional. Type of the playbook.
   *
   * @var string
   */
  public $playbookType;
  /**
   * Output only. The resource name of flows referenced by the current playbook
   * in the instructions.
   *
   * @var string[]
   */
  public $referencedFlows;
  /**
   * Output only. The resource name of other playbooks referenced by the current
   * playbook in the instructions.
   *
   * @var string[]
   */
  public $referencedPlaybooks;
  /**
   * Optional. The resource name of tools referenced by the current playbook in
   * the instructions. If not provided explicitly, they are will be implied
   * using the tool being referenced in goal and steps.
   *
   * @var string[]
   */
  public $referencedTools;
  /**
   * Output only. Estimated number of tokes current playbook takes when sent to
   * the LLM.
   *
   * @var string
   */
  public $tokenCount;
  /**
   * Output only. Last time the playbook version was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The playbook's scoped code block, which may implement handlers
   * and actions.
   *
   * @param GoogleCloudDialogflowCxV3CodeBlock $codeBlock
   */
  public function setCodeBlock(GoogleCloudDialogflowCxV3CodeBlock $codeBlock)
  {
    $this->codeBlock = $codeBlock;
  }
  /**
   * @return GoogleCloudDialogflowCxV3CodeBlock
   */
  public function getCodeBlock()
  {
    return $this->codeBlock;
  }
  /**
   * Output only. The timestamp of initial playbook creation.
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
   * Required. The human-readable name of the playbook, unique within an agent.
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
   * Required. High level description of the goal the playbook intend to
   * accomplish. A goal should be concise since it's visible to other playbooks
   * that may reference this playbook.
   *
   * @param string $goal
   */
  public function setGoal($goal)
  {
    $this->goal = $goal;
  }
  /**
   * @return string
   */
  public function getGoal()
  {
    return $this->goal;
  }
  /**
   * Optional. A list of registered handlers to execuate based on the specified
   * triggers.
   *
   * @param GoogleCloudDialogflowCxV3Handler[] $handlers
   */
  public function setHandlers($handlers)
  {
    $this->handlers = $handlers;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Handler[]
   */
  public function getHandlers()
  {
    return $this->handlers;
  }
  /**
   * Optional. Output only. Names of inline actions scoped to this playbook.
   * These actions are in addition to those belonging to referenced tools, child
   * playbooks, and flows, e.g. actions that are defined in the playbook's code
   * block.
   *
   * @param string[] $inlineActions
   */
  public function setInlineActions($inlineActions)
  {
    $this->inlineActions = $inlineActions;
  }
  /**
   * @return string[]
   */
  public function getInlineActions()
  {
    return $this->inlineActions;
  }
  /**
   * Optional. Defined structured input parameters for this playbook.
   *
   * @param GoogleCloudDialogflowCxV3ParameterDefinition[] $inputParameterDefinitions
   */
  public function setInputParameterDefinitions($inputParameterDefinitions)
  {
    $this->inputParameterDefinitions = $inputParameterDefinitions;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ParameterDefinition[]
   */
  public function getInputParameterDefinitions()
  {
    return $this->inputParameterDefinitions;
  }
  /**
   * Instruction to accomplish target goal.
   *
   * @param GoogleCloudDialogflowCxV3PlaybookInstruction $instruction
   */
  public function setInstruction(GoogleCloudDialogflowCxV3PlaybookInstruction $instruction)
  {
    $this->instruction = $instruction;
  }
  /**
   * @return GoogleCloudDialogflowCxV3PlaybookInstruction
   */
  public function getInstruction()
  {
    return $this->instruction;
  }
  /**
   * Optional. Llm model settings for the playbook.
   *
   * @param GoogleCloudDialogflowCxV3LlmModelSettings $llmModelSettings
   */
  public function setLlmModelSettings(GoogleCloudDialogflowCxV3LlmModelSettings $llmModelSettings)
  {
    $this->llmModelSettings = $llmModelSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3LlmModelSettings
   */
  public function getLlmModelSettings()
  {
    return $this->llmModelSettings;
  }
  /**
   * The unique identifier of the playbook. Format:
   * `projects//locations//agents//playbooks/`.
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
   * Optional. Defined structured output parameters for this playbook.
   *
   * @param GoogleCloudDialogflowCxV3ParameterDefinition[] $outputParameterDefinitions
   */
  public function setOutputParameterDefinitions($outputParameterDefinitions)
  {
    $this->outputParameterDefinitions = $outputParameterDefinitions;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ParameterDefinition[]
   */
  public function getOutputParameterDefinitions()
  {
    return $this->outputParameterDefinitions;
  }
  /**
   * Optional. Type of the playbook.
   *
   * Accepted values: PLAYBOOK_TYPE_UNSPECIFIED, TASK, ROUTINE
   *
   * @param self::PLAYBOOK_TYPE_* $playbookType
   */
  public function setPlaybookType($playbookType)
  {
    $this->playbookType = $playbookType;
  }
  /**
   * @return self::PLAYBOOK_TYPE_*
   */
  public function getPlaybookType()
  {
    return $this->playbookType;
  }
  /**
   * Output only. The resource name of flows referenced by the current playbook
   * in the instructions.
   *
   * @param string[] $referencedFlows
   */
  public function setReferencedFlows($referencedFlows)
  {
    $this->referencedFlows = $referencedFlows;
  }
  /**
   * @return string[]
   */
  public function getReferencedFlows()
  {
    return $this->referencedFlows;
  }
  /**
   * Output only. The resource name of other playbooks referenced by the current
   * playbook in the instructions.
   *
   * @param string[] $referencedPlaybooks
   */
  public function setReferencedPlaybooks($referencedPlaybooks)
  {
    $this->referencedPlaybooks = $referencedPlaybooks;
  }
  /**
   * @return string[]
   */
  public function getReferencedPlaybooks()
  {
    return $this->referencedPlaybooks;
  }
  /**
   * Optional. The resource name of tools referenced by the current playbook in
   * the instructions. If not provided explicitly, they are will be implied
   * using the tool being referenced in goal and steps.
   *
   * @param string[] $referencedTools
   */
  public function setReferencedTools($referencedTools)
  {
    $this->referencedTools = $referencedTools;
  }
  /**
   * @return string[]
   */
  public function getReferencedTools()
  {
    return $this->referencedTools;
  }
  /**
   * Output only. Estimated number of tokes current playbook takes when sent to
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
   * Output only. Last time the playbook version was updated.
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
class_alias(GoogleCloudDialogflowCxV3Playbook::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Playbook');
