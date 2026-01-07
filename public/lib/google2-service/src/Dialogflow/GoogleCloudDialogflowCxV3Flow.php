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

class GoogleCloudDialogflowCxV3Flow extends \Google\Collection
{
  protected $collection_key = 'transitionRoutes';
  protected $advancedSettingsType = GoogleCloudDialogflowCxV3AdvancedSettings::class;
  protected $advancedSettingsDataType = '';
  /**
   * The description of the flow. The maximum length is 500 characters. If
   * exceeded, the request is rejected.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The human-readable name of the flow.
   *
   * @var string
   */
  public $displayName;
  protected $eventHandlersType = GoogleCloudDialogflowCxV3EventHandler::class;
  protected $eventHandlersDataType = 'array';
  protected $inputParameterDefinitionsType = GoogleCloudDialogflowCxV3ParameterDefinition::class;
  protected $inputParameterDefinitionsDataType = 'array';
  protected $knowledgeConnectorSettingsType = GoogleCloudDialogflowCxV3KnowledgeConnectorSettings::class;
  protected $knowledgeConnectorSettingsDataType = '';
  /**
   * Indicates whether the flow is locked for changes. If the flow is locked,
   * modifications to the flow will be rejected.
   *
   * @var bool
   */
  public $locked;
  protected $multiLanguageSettingsType = GoogleCloudDialogflowCxV3FlowMultiLanguageSettings::class;
  protected $multiLanguageSettingsDataType = '';
  /**
   * The unique identifier of the flow. Format:
   * `projects//locations//agents//flows/`.
   *
   * @var string
   */
  public $name;
  protected $nluSettingsType = GoogleCloudDialogflowCxV3NluSettings::class;
  protected $nluSettingsDataType = '';
  protected $outputParameterDefinitionsType = GoogleCloudDialogflowCxV3ParameterDefinition::class;
  protected $outputParameterDefinitionsDataType = 'array';
  /**
   * A flow's transition route group serve two purposes: * They are responsible
   * for matching the user's first utterances in the flow. * They are inherited
   * by every page's transition route groups. Transition route groups defined in
   * the page have higher priority than those defined in the flow. Format:
   * `projects//locations//agents//flows//transitionRouteGroups/` or
   * `projects//locations//agents//transitionRouteGroups/` for agent-level
   * groups.
   *
   * @var string[]
   */
  public $transitionRouteGroups;
  protected $transitionRoutesType = GoogleCloudDialogflowCxV3TransitionRoute::class;
  protected $transitionRoutesDataType = 'array';

  /**
   * Hierarchical advanced settings for this flow. The settings exposed at the
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
   * The description of the flow. The maximum length is 500 characters. If
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
   * Required. The human-readable name of the flow.
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
   * A flow's event handlers serve two purposes: * They are responsible for
   * handling events (e.g. no match, webhook errors) in the flow. * They are
   * inherited by every page's event handlers, which can be used to handle
   * common events regardless of the current page. Event handlers defined in the
   * page have higher priority than those defined in the flow. Unlike
   * transition_routes, these handlers are evaluated on a first-match basis. The
   * first one that matches the event get executed, with the rest being ignored.
   *
   * @param GoogleCloudDialogflowCxV3EventHandler[] $eventHandlers
   */
  public function setEventHandlers($eventHandlers)
  {
    $this->eventHandlers = $eventHandlers;
  }
  /**
   * @return GoogleCloudDialogflowCxV3EventHandler[]
   */
  public function getEventHandlers()
  {
    return $this->eventHandlers;
  }
  /**
   * Optional. Defined structured input parameters for this flow.
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
   * Optional. Knowledge connector configuration.
   *
   * @param GoogleCloudDialogflowCxV3KnowledgeConnectorSettings $knowledgeConnectorSettings
   */
  public function setKnowledgeConnectorSettings(GoogleCloudDialogflowCxV3KnowledgeConnectorSettings $knowledgeConnectorSettings)
  {
    $this->knowledgeConnectorSettings = $knowledgeConnectorSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3KnowledgeConnectorSettings
   */
  public function getKnowledgeConnectorSettings()
  {
    return $this->knowledgeConnectorSettings;
  }
  /**
   * Indicates whether the flow is locked for changes. If the flow is locked,
   * modifications to the flow will be rejected.
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
   * Optional. Multi-lingual agent settings for this flow.
   *
   * @param GoogleCloudDialogflowCxV3FlowMultiLanguageSettings $multiLanguageSettings
   */
  public function setMultiLanguageSettings(GoogleCloudDialogflowCxV3FlowMultiLanguageSettings $multiLanguageSettings)
  {
    $this->multiLanguageSettings = $multiLanguageSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3FlowMultiLanguageSettings
   */
  public function getMultiLanguageSettings()
  {
    return $this->multiLanguageSettings;
  }
  /**
   * The unique identifier of the flow. Format:
   * `projects//locations//agents//flows/`.
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
   * NLU related settings of the flow.
   *
   * @param GoogleCloudDialogflowCxV3NluSettings $nluSettings
   */
  public function setNluSettings(GoogleCloudDialogflowCxV3NluSettings $nluSettings)
  {
    $this->nluSettings = $nluSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3NluSettings
   */
  public function getNluSettings()
  {
    return $this->nluSettings;
  }
  /**
   * Optional. Defined structured output parameters for this flow.
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
   * A flow's transition route group serve two purposes: * They are responsible
   * for matching the user's first utterances in the flow. * They are inherited
   * by every page's transition route groups. Transition route groups defined in
   * the page have higher priority than those defined in the flow. Format:
   * `projects//locations//agents//flows//transitionRouteGroups/` or
   * `projects//locations//agents//transitionRouteGroups/` for agent-level
   * groups.
   *
   * @param string[] $transitionRouteGroups
   */
  public function setTransitionRouteGroups($transitionRouteGroups)
  {
    $this->transitionRouteGroups = $transitionRouteGroups;
  }
  /**
   * @return string[]
   */
  public function getTransitionRouteGroups()
  {
    return $this->transitionRouteGroups;
  }
  /**
   * A flow's transition routes serve two purposes: * They are responsible for
   * matching the user's first utterances in the flow. * They are inherited by
   * every page's transition routes and can support use cases such as the user
   * saying "help" or "can I talk to a human?", which can be handled in a common
   * way regardless of the current page. Transition routes defined in the page
   * have higher priority than those defined in the flow. TransitionRoutes are
   * evaluated in the following order: * TransitionRoutes with intent specified.
   * * TransitionRoutes with only condition specified. TransitionRoutes with
   * intent specified are inherited by pages in the flow.
   *
   * @param GoogleCloudDialogflowCxV3TransitionRoute[] $transitionRoutes
   */
  public function setTransitionRoutes($transitionRoutes)
  {
    $this->transitionRoutes = $transitionRoutes;
  }
  /**
   * @return GoogleCloudDialogflowCxV3TransitionRoute[]
   */
  public function getTransitionRoutes()
  {
    return $this->transitionRoutes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Flow::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Flow');
