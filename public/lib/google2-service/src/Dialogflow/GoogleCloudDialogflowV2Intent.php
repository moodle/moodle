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

class GoogleCloudDialogflowV2Intent extends \Google\Collection
{
  /**
   * Webhook is disabled in the agent and in the intent.
   */
  public const WEBHOOK_STATE_WEBHOOK_STATE_UNSPECIFIED = 'WEBHOOK_STATE_UNSPECIFIED';
  /**
   * Webhook is enabled in the agent and in the intent.
   */
  public const WEBHOOK_STATE_WEBHOOK_STATE_ENABLED = 'WEBHOOK_STATE_ENABLED';
  /**
   * Webhook is enabled in the agent and in the intent. Also, each slot filling
   * prompt is forwarded to the webhook.
   */
  public const WEBHOOK_STATE_WEBHOOK_STATE_ENABLED_FOR_SLOT_FILLING = 'WEBHOOK_STATE_ENABLED_FOR_SLOT_FILLING';
  protected $collection_key = 'trainingPhrases';
  /**
   * Optional. The name of the action associated with the intent. Note: The
   * action name must not contain whitespaces.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. The list of platforms for which the first responses will be
   * copied from the messages in PLATFORM_UNSPECIFIED (i.e. default platform).
   *
   * @var string[]
   */
  public $defaultResponsePlatforms;
  /**
   * Required. The name of this intent.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Indicates that this intent ends an interaction. Some integrations
   * (e.g., Actions on Google or Dialogflow phone gateway) use this information
   * to close interaction with an end user. Default is false.
   *
   * @var bool
   */
  public $endInteraction;
  /**
   * Optional. The collection of event names that trigger the intent. If the
   * collection of input contexts is not empty, all of the contexts must be
   * present in the active user session for an event to trigger this intent.
   * Event names are limited to 150 characters.
   *
   * @var string[]
   */
  public $events;
  protected $followupIntentInfoType = GoogleCloudDialogflowV2IntentFollowupIntentInfo::class;
  protected $followupIntentInfoDataType = 'array';
  /**
   * Optional. The list of context names required for this intent to be
   * triggered. Format: `projects//agent/sessions/-/contexts/`.
   *
   * @var string[]
   */
  public $inputContextNames;
  /**
   * Optional. Indicates whether this is a fallback intent.
   *
   * @var bool
   */
  public $isFallback;
  /**
   * Optional. Indicates that a live agent should be brought in to handle the
   * interaction with the user. In most cases, when you set this flag to true,
   * you would also want to set end_interaction to true as well. Default is
   * false.
   *
   * @var bool
   */
  public $liveAgentHandoff;
  protected $messagesType = GoogleCloudDialogflowV2IntentMessage::class;
  protected $messagesDataType = 'array';
  /**
   * Optional. Indicates whether Machine Learning is disabled for the intent.
   * Note: If `ml_disabled` setting is set to true, then this intent is not
   * taken into account during inference in `ML ONLY` match mode. Also, auto-
   * markup in the UI is turned off.
   *
   * @var bool
   */
  public $mlDisabled;
  /**
   * Optional. The unique identifier of this intent. Required for
   * Intents.UpdateIntent and Intents.BatchUpdateIntents methods. Format:
   * `projects//agent/intents/`.
   *
   * @var string
   */
  public $name;
  protected $outputContextsType = GoogleCloudDialogflowV2Context::class;
  protected $outputContextsDataType = 'array';
  protected $parametersType = GoogleCloudDialogflowV2IntentParameter::class;
  protected $parametersDataType = 'array';
  /**
   * Read-only after creation. The unique identifier of the parent intent in the
   * chain of followup intents. You can set this field when creating an intent,
   * for example with CreateIntent or BatchUpdateIntents, in order to make this
   * intent a followup intent. It identifies the parent followup intent. Format:
   * `projects//agent/intents/`.
   *
   * @var string
   */
  public $parentFollowupIntentName;
  /**
   * Optional. The priority of this intent. Higher numbers represent higher
   * priorities. - If the supplied value is unspecified or 0, the service
   * translates the value to 500,000, which corresponds to the `Normal` priority
   * in the console. - If the supplied value is negative, the intent is ignored
   * in runtime detect intent requests.
   *
   * @var int
   */
  public $priority;
  /**
   * Optional. Indicates whether to delete all contexts in the current session
   * when this intent is matched.
   *
   * @var bool
   */
  public $resetContexts;
  /**
   * Output only. Read-only. The unique identifier of the root intent in the
   * chain of followup intents. It identifies the correct followup intents chain
   * for this intent. We populate this field only in the output. Format:
   * `projects//agent/intents/`.
   *
   * @var string
   */
  public $rootFollowupIntentName;
  protected $trainingPhrasesType = GoogleCloudDialogflowV2IntentTrainingPhrase::class;
  protected $trainingPhrasesDataType = 'array';
  /**
   * Optional. Indicates whether webhooks are enabled for the intent.
   *
   * @var string
   */
  public $webhookState;

  /**
   * Optional. The name of the action associated with the intent. Note: The
   * action name must not contain whitespaces.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Optional. The list of platforms for which the first responses will be
   * copied from the messages in PLATFORM_UNSPECIFIED (i.e. default platform).
   *
   * @param string[] $defaultResponsePlatforms
   */
  public function setDefaultResponsePlatforms($defaultResponsePlatforms)
  {
    $this->defaultResponsePlatforms = $defaultResponsePlatforms;
  }
  /**
   * @return string[]
   */
  public function getDefaultResponsePlatforms()
  {
    return $this->defaultResponsePlatforms;
  }
  /**
   * Required. The name of this intent.
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
   * Optional. Indicates that this intent ends an interaction. Some integrations
   * (e.g., Actions on Google or Dialogflow phone gateway) use this information
   * to close interaction with an end user. Default is false.
   *
   * @param bool $endInteraction
   */
  public function setEndInteraction($endInteraction)
  {
    $this->endInteraction = $endInteraction;
  }
  /**
   * @return bool
   */
  public function getEndInteraction()
  {
    return $this->endInteraction;
  }
  /**
   * Optional. The collection of event names that trigger the intent. If the
   * collection of input contexts is not empty, all of the contexts must be
   * present in the active user session for an event to trigger this intent.
   * Event names are limited to 150 characters.
   *
   * @param string[] $events
   */
  public function setEvents($events)
  {
    $this->events = $events;
  }
  /**
   * @return string[]
   */
  public function getEvents()
  {
    return $this->events;
  }
  /**
   * Output only. Read-only. Information about all followup intents that have
   * this intent as a direct or indirect parent. We populate this field only in
   * the output.
   *
   * @param GoogleCloudDialogflowV2IntentFollowupIntentInfo[] $followupIntentInfo
   */
  public function setFollowupIntentInfo($followupIntentInfo)
  {
    $this->followupIntentInfo = $followupIntentInfo;
  }
  /**
   * @return GoogleCloudDialogflowV2IntentFollowupIntentInfo[]
   */
  public function getFollowupIntentInfo()
  {
    return $this->followupIntentInfo;
  }
  /**
   * Optional. The list of context names required for this intent to be
   * triggered. Format: `projects//agent/sessions/-/contexts/`.
   *
   * @param string[] $inputContextNames
   */
  public function setInputContextNames($inputContextNames)
  {
    $this->inputContextNames = $inputContextNames;
  }
  /**
   * @return string[]
   */
  public function getInputContextNames()
  {
    return $this->inputContextNames;
  }
  /**
   * Optional. Indicates whether this is a fallback intent.
   *
   * @param bool $isFallback
   */
  public function setIsFallback($isFallback)
  {
    $this->isFallback = $isFallback;
  }
  /**
   * @return bool
   */
  public function getIsFallback()
  {
    return $this->isFallback;
  }
  /**
   * Optional. Indicates that a live agent should be brought in to handle the
   * interaction with the user. In most cases, when you set this flag to true,
   * you would also want to set end_interaction to true as well. Default is
   * false.
   *
   * @param bool $liveAgentHandoff
   */
  public function setLiveAgentHandoff($liveAgentHandoff)
  {
    $this->liveAgentHandoff = $liveAgentHandoff;
  }
  /**
   * @return bool
   */
  public function getLiveAgentHandoff()
  {
    return $this->liveAgentHandoff;
  }
  /**
   * Optional. The collection of rich messages corresponding to the `Response`
   * field in the Dialogflow console.
   *
   * @param GoogleCloudDialogflowV2IntentMessage[] $messages
   */
  public function setMessages($messages)
  {
    $this->messages = $messages;
  }
  /**
   * @return GoogleCloudDialogflowV2IntentMessage[]
   */
  public function getMessages()
  {
    return $this->messages;
  }
  /**
   * Optional. Indicates whether Machine Learning is disabled for the intent.
   * Note: If `ml_disabled` setting is set to true, then this intent is not
   * taken into account during inference in `ML ONLY` match mode. Also, auto-
   * markup in the UI is turned off.
   *
   * @param bool $mlDisabled
   */
  public function setMlDisabled($mlDisabled)
  {
    $this->mlDisabled = $mlDisabled;
  }
  /**
   * @return bool
   */
  public function getMlDisabled()
  {
    return $this->mlDisabled;
  }
  /**
   * Optional. The unique identifier of this intent. Required for
   * Intents.UpdateIntent and Intents.BatchUpdateIntents methods. Format:
   * `projects//agent/intents/`.
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
   * Optional. The collection of contexts that are activated when the intent is
   * matched. Context messages in this collection should not set the parameters
   * field. Setting the `lifespan_count` to 0 will reset the context when the
   * intent is matched. Format: `projects//agent/sessions/-/contexts/`.
   *
   * @param GoogleCloudDialogflowV2Context[] $outputContexts
   */
  public function setOutputContexts($outputContexts)
  {
    $this->outputContexts = $outputContexts;
  }
  /**
   * @return GoogleCloudDialogflowV2Context[]
   */
  public function getOutputContexts()
  {
    return $this->outputContexts;
  }
  /**
   * Optional. The collection of parameters associated with the intent.
   *
   * @param GoogleCloudDialogflowV2IntentParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudDialogflowV2IntentParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Read-only after creation. The unique identifier of the parent intent in the
   * chain of followup intents. You can set this field when creating an intent,
   * for example with CreateIntent or BatchUpdateIntents, in order to make this
   * intent a followup intent. It identifies the parent followup intent. Format:
   * `projects//agent/intents/`.
   *
   * @param string $parentFollowupIntentName
   */
  public function setParentFollowupIntentName($parentFollowupIntentName)
  {
    $this->parentFollowupIntentName = $parentFollowupIntentName;
  }
  /**
   * @return string
   */
  public function getParentFollowupIntentName()
  {
    return $this->parentFollowupIntentName;
  }
  /**
   * Optional. The priority of this intent. Higher numbers represent higher
   * priorities. - If the supplied value is unspecified or 0, the service
   * translates the value to 500,000, which corresponds to the `Normal` priority
   * in the console. - If the supplied value is negative, the intent is ignored
   * in runtime detect intent requests.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Optional. Indicates whether to delete all contexts in the current session
   * when this intent is matched.
   *
   * @param bool $resetContexts
   */
  public function setResetContexts($resetContexts)
  {
    $this->resetContexts = $resetContexts;
  }
  /**
   * @return bool
   */
  public function getResetContexts()
  {
    return $this->resetContexts;
  }
  /**
   * Output only. Read-only. The unique identifier of the root intent in the
   * chain of followup intents. It identifies the correct followup intents chain
   * for this intent. We populate this field only in the output. Format:
   * `projects//agent/intents/`.
   *
   * @param string $rootFollowupIntentName
   */
  public function setRootFollowupIntentName($rootFollowupIntentName)
  {
    $this->rootFollowupIntentName = $rootFollowupIntentName;
  }
  /**
   * @return string
   */
  public function getRootFollowupIntentName()
  {
    return $this->rootFollowupIntentName;
  }
  /**
   * Optional. The collection of examples that the agent is trained on.
   *
   * @param GoogleCloudDialogflowV2IntentTrainingPhrase[] $trainingPhrases
   */
  public function setTrainingPhrases($trainingPhrases)
  {
    $this->trainingPhrases = $trainingPhrases;
  }
  /**
   * @return GoogleCloudDialogflowV2IntentTrainingPhrase[]
   */
  public function getTrainingPhrases()
  {
    return $this->trainingPhrases;
  }
  /**
   * Optional. Indicates whether webhooks are enabled for the intent.
   *
   * Accepted values: WEBHOOK_STATE_UNSPECIFIED, WEBHOOK_STATE_ENABLED,
   * WEBHOOK_STATE_ENABLED_FOR_SLOT_FILLING
   *
   * @param self::WEBHOOK_STATE_* $webhookState
   */
  public function setWebhookState($webhookState)
  {
    $this->webhookState = $webhookState;
  }
  /**
   * @return self::WEBHOOK_STATE_*
   */
  public function getWebhookState()
  {
    return $this->webhookState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2Intent::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2Intent');
