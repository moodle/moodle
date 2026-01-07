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

class GoogleCloudDialogflowCxV3Agent extends \Google\Collection
{
  protected $collection_key = 'supportedLanguageCodes';
  protected $advancedSettingsType = GoogleCloudDialogflowCxV3AdvancedSettings::class;
  protected $advancedSettingsDataType = '';
  protected $answerFeedbackSettingsType = GoogleCloudDialogflowCxV3AgentAnswerFeedbackSettings::class;
  protected $answerFeedbackSettingsDataType = '';
  /**
   * The URI of the agent's avatar. Avatars are used throughout the Dialogflow
   * console and in the self-hosted [Web
   * Demo](https://cloud.google.com/dialogflow/docs/integrations/web-demo)
   * integration.
   *
   * @var string
   */
  public $avatarUri;
  protected $clientCertificateSettingsType = GoogleCloudDialogflowCxV3AgentClientCertificateSettings::class;
  protected $clientCertificateSettingsDataType = '';
  /**
   * Required. Immutable. The default language of the agent as a language tag.
   * See [Language
   * Support](https://cloud.google.com/dialogflow/cx/docs/reference/language)
   * for a list of the currently supported language codes. This field cannot be
   * set by the Agents.UpdateAgent method.
   *
   * @var string
   */
  public $defaultLanguageCode;
  /**
   * The description of the agent. The maximum length is 500 characters. If
   * exceeded, the request is rejected.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The human-readable name of the agent, unique within the location.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Enable training multi-lingual models for this agent. These models
   * will be trained on all the languages supported by the agent.
   *
   * @var bool
   */
  public $enableMultiLanguageTraining;
  /**
   * Indicates if automatic spell correction is enabled in detect intent
   * requests.
   *
   * @var bool
   */
  public $enableSpellCorrection;
  /**
   * Indicates if stackdriver logging is enabled for the agent. Please use
   * agent.advanced_settings instead.
   *
   * @deprecated
   * @var bool
   */
  public $enableStackdriverLogging;
  protected $genAppBuilderSettingsType = GoogleCloudDialogflowCxV3AgentGenAppBuilderSettings::class;
  protected $genAppBuilderSettingsDataType = '';
  protected $gitIntegrationSettingsType = GoogleCloudDialogflowCxV3AgentGitIntegrationSettings::class;
  protected $gitIntegrationSettingsDataType = '';
  /**
   * Indicates whether the agent is locked for changes. If the agent is locked,
   * modifications to the agent will be rejected except for RestoreAgent.
   *
   * @var bool
   */
  public $locked;
  /**
   * The unique identifier of the agent. Required for the Agents.UpdateAgent
   * method. Agents.CreateAgent populates the name automatically. Format:
   * `projects//locations//agents/`.
   *
   * @var string
   */
  public $name;
  protected $personalizationSettingsType = GoogleCloudDialogflowCxV3AgentPersonalizationSettings::class;
  protected $personalizationSettingsDataType = '';
  /**
   * Optional. Output only. A read only boolean field reflecting Zone Isolation
   * status of the agent.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Optional. Output only. A read only boolean field reflecting Zone Separation
   * status of the agent.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Name of the SecuritySettings reference for the agent. Format:
   * `projects//locations//securitySettings/`.
   *
   * @var string
   */
  public $securitySettings;
  protected $speechToTextSettingsType = GoogleCloudDialogflowCxV3SpeechToTextSettings::class;
  protected $speechToTextSettingsDataType = '';
  /**
   * Name of the start flow in this agent. A start flow will be automatically
   * created when the agent is created, and can only be deleted by deleting the
   * agent. Format: `projects//locations//agents//flows/`. Currently only the
   * default start flow with id "00000000-0000-0000-0000-000000000000" is
   * allowed.
   *
   * @var string
   */
  public $startFlow;
  /**
   * Name of the start playbook in this agent. A start playbook will be
   * automatically created when the agent is created, and can only be deleted by
   * deleting the agent. Format: `projects//locations//agents//playbooks/`.
   * Currently only the default playbook with id
   * "00000000-0000-0000-0000-000000000000" is allowed.
   *
   * @var string
   */
  public $startPlaybook;
  /**
   * The list of all languages supported by the agent (except for the
   * `default_language_code`).
   *
   * @var string[]
   */
  public $supportedLanguageCodes;
  protected $textToSpeechSettingsType = GoogleCloudDialogflowCxV3TextToSpeechSettings::class;
  protected $textToSpeechSettingsDataType = '';
  /**
   * Required. The time zone of the agent from the [time zone
   * database](https://www.iana.org/time-zones), e.g., America/New_York,
   * Europe/Paris.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Hierarchical advanced settings for this agent. The settings exposed at the
   * lower level overrides the settings exposed at the higher level.
   *
   * @param GoogleCloudDialogflowCxV3AdvancedSettings $advancedSettings
   */
  public function setAdvancedSettings(GoogleCloudDialogflowCxV3AdvancedSettings $advancedSettings)
  {
    $this->advancedSettings = $advancedSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AdvancedSettings
   */
  public function getAdvancedSettings()
  {
    return $this->advancedSettings;
  }
  /**
   * Optional. Answer feedback collection settings.
   *
   * @param GoogleCloudDialogflowCxV3AgentAnswerFeedbackSettings $answerFeedbackSettings
   */
  public function setAnswerFeedbackSettings(GoogleCloudDialogflowCxV3AgentAnswerFeedbackSettings $answerFeedbackSettings)
  {
    $this->answerFeedbackSettings = $answerFeedbackSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AgentAnswerFeedbackSettings
   */
  public function getAnswerFeedbackSettings()
  {
    return $this->answerFeedbackSettings;
  }
  /**
   * The URI of the agent's avatar. Avatars are used throughout the Dialogflow
   * console and in the self-hosted [Web
   * Demo](https://cloud.google.com/dialogflow/docs/integrations/web-demo)
   * integration.
   *
   * @param string $avatarUri
   */
  public function setAvatarUri($avatarUri)
  {
    $this->avatarUri = $avatarUri;
  }
  /**
   * @return string
   */
  public function getAvatarUri()
  {
    return $this->avatarUri;
  }
  /**
   * Optional. Settings for custom client certificates.
   *
   * @param GoogleCloudDialogflowCxV3AgentClientCertificateSettings $clientCertificateSettings
   */
  public function setClientCertificateSettings(GoogleCloudDialogflowCxV3AgentClientCertificateSettings $clientCertificateSettings)
  {
    $this->clientCertificateSettings = $clientCertificateSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AgentClientCertificateSettings
   */
  public function getClientCertificateSettings()
  {
    return $this->clientCertificateSettings;
  }
  /**
   * Required. Immutable. The default language of the agent as a language tag.
   * See [Language
   * Support](https://cloud.google.com/dialogflow/cx/docs/reference/language)
   * for a list of the currently supported language codes. This field cannot be
   * set by the Agents.UpdateAgent method.
   *
   * @param string $defaultLanguageCode
   */
  public function setDefaultLanguageCode($defaultLanguageCode)
  {
    $this->defaultLanguageCode = $defaultLanguageCode;
  }
  /**
   * @return string
   */
  public function getDefaultLanguageCode()
  {
    return $this->defaultLanguageCode;
  }
  /**
   * The description of the agent. The maximum length is 500 characters. If
   * exceeded, the request is rejected.
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
   * Required. The human-readable name of the agent, unique within the location.
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
   * Optional. Enable training multi-lingual models for this agent. These models
   * will be trained on all the languages supported by the agent.
   *
   * @param bool $enableMultiLanguageTraining
   */
  public function setEnableMultiLanguageTraining($enableMultiLanguageTraining)
  {
    $this->enableMultiLanguageTraining = $enableMultiLanguageTraining;
  }
  /**
   * @return bool
   */
  public function getEnableMultiLanguageTraining()
  {
    return $this->enableMultiLanguageTraining;
  }
  /**
   * Indicates if automatic spell correction is enabled in detect intent
   * requests.
   *
   * @param bool $enableSpellCorrection
   */
  public function setEnableSpellCorrection($enableSpellCorrection)
  {
    $this->enableSpellCorrection = $enableSpellCorrection;
  }
  /**
   * @return bool
   */
  public function getEnableSpellCorrection()
  {
    return $this->enableSpellCorrection;
  }
  /**
   * Indicates if stackdriver logging is enabled for the agent. Please use
   * agent.advanced_settings instead.
   *
   * @deprecated
   * @param bool $enableStackdriverLogging
   */
  public function setEnableStackdriverLogging($enableStackdriverLogging)
  {
    $this->enableStackdriverLogging = $enableStackdriverLogging;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableStackdriverLogging()
  {
    return $this->enableStackdriverLogging;
  }
  /**
   * Gen App Builder-related agent-level settings.
   *
   * @param GoogleCloudDialogflowCxV3AgentGenAppBuilderSettings $genAppBuilderSettings
   */
  public function setGenAppBuilderSettings(GoogleCloudDialogflowCxV3AgentGenAppBuilderSettings $genAppBuilderSettings)
  {
    $this->genAppBuilderSettings = $genAppBuilderSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AgentGenAppBuilderSettings
   */
  public function getGenAppBuilderSettings()
  {
    return $this->genAppBuilderSettings;
  }
  /**
   * Git integration settings for this agent.
   *
   * @param GoogleCloudDialogflowCxV3AgentGitIntegrationSettings $gitIntegrationSettings
   */
  public function setGitIntegrationSettings(GoogleCloudDialogflowCxV3AgentGitIntegrationSettings $gitIntegrationSettings)
  {
    $this->gitIntegrationSettings = $gitIntegrationSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AgentGitIntegrationSettings
   */
  public function getGitIntegrationSettings()
  {
    return $this->gitIntegrationSettings;
  }
  /**
   * Indicates whether the agent is locked for changes. If the agent is locked,
   * modifications to the agent will be rejected except for RestoreAgent.
   *
   * @param bool $locked
   */
  public function setLocked($locked)
  {
    $this->locked = $locked;
  }
  /**
   * @return bool
   */
  public function getLocked()
  {
    return $this->locked;
  }
  /**
   * The unique identifier of the agent. Required for the Agents.UpdateAgent
   * method. Agents.CreateAgent populates the name automatically. Format:
   * `projects//locations//agents/`.
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
   * Optional. Settings for end user personalization.
   *
   * @param GoogleCloudDialogflowCxV3AgentPersonalizationSettings $personalizationSettings
   */
  public function setPersonalizationSettings(GoogleCloudDialogflowCxV3AgentPersonalizationSettings $personalizationSettings)
  {
    $this->personalizationSettings = $personalizationSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AgentPersonalizationSettings
   */
  public function getPersonalizationSettings()
  {
    return $this->personalizationSettings;
  }
  /**
   * Optional. Output only. A read only boolean field reflecting Zone Isolation
   * status of the agent.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Optional. Output only. A read only boolean field reflecting Zone Separation
   * status of the agent.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Name of the SecuritySettings reference for the agent. Format:
   * `projects//locations//securitySettings/`.
   *
   * @param string $securitySettings
   */
  public function setSecuritySettings($securitySettings)
  {
    $this->securitySettings = $securitySettings;
  }
  /**
   * @return string
   */
  public function getSecuritySettings()
  {
    return $this->securitySettings;
  }
  /**
   * Speech recognition related settings.
   *
   * @param GoogleCloudDialogflowCxV3SpeechToTextSettings $speechToTextSettings
   */
  public function setSpeechToTextSettings(GoogleCloudDialogflowCxV3SpeechToTextSettings $speechToTextSettings)
  {
    $this->speechToTextSettings = $speechToTextSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SpeechToTextSettings
   */
  public function getSpeechToTextSettings()
  {
    return $this->speechToTextSettings;
  }
  /**
   * Name of the start flow in this agent. A start flow will be automatically
   * created when the agent is created, and can only be deleted by deleting the
   * agent. Format: `projects//locations//agents//flows/`. Currently only the
   * default start flow with id "00000000-0000-0000-0000-000000000000" is
   * allowed.
   *
   * @param string $startFlow
   */
  public function setStartFlow($startFlow)
  {
    $this->startFlow = $startFlow;
  }
  /**
   * @return string
   */
  public function getStartFlow()
  {
    return $this->startFlow;
  }
  /**
   * Name of the start playbook in this agent. A start playbook will be
   * automatically created when the agent is created, and can only be deleted by
   * deleting the agent. Format: `projects//locations//agents//playbooks/`.
   * Currently only the default playbook with id
   * "00000000-0000-0000-0000-000000000000" is allowed.
   *
   * @param string $startPlaybook
   */
  public function setStartPlaybook($startPlaybook)
  {
    $this->startPlaybook = $startPlaybook;
  }
  /**
   * @return string
   */
  public function getStartPlaybook()
  {
    return $this->startPlaybook;
  }
  /**
   * The list of all languages supported by the agent (except for the
   * `default_language_code`).
   *
   * @param string[] $supportedLanguageCodes
   */
  public function setSupportedLanguageCodes($supportedLanguageCodes)
  {
    $this->supportedLanguageCodes = $supportedLanguageCodes;
  }
  /**
   * @return string[]
   */
  public function getSupportedLanguageCodes()
  {
    return $this->supportedLanguageCodes;
  }
  /**
   * Settings on instructing the speech synthesizer on how to generate the
   * output audio content.
   *
   * @param GoogleCloudDialogflowCxV3TextToSpeechSettings $textToSpeechSettings
   */
  public function setTextToSpeechSettings(GoogleCloudDialogflowCxV3TextToSpeechSettings $textToSpeechSettings)
  {
    $this->textToSpeechSettings = $textToSpeechSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3TextToSpeechSettings
   */
  public function getTextToSpeechSettings()
  {
    return $this->textToSpeechSettings;
  }
  /**
   * Required. The time zone of the agent from the [time zone
   * database](https://www.iana.org/time-zones), e.g., America/New_York,
   * Europe/Paris.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Agent::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Agent');
