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

class GoogleCloudDialogflowCxV3Page extends \Google\Collection
{
  protected $collection_key = 'transitionRoutes';
  protected $advancedSettingsType = GoogleCloudDialogflowCxV3AdvancedSettings::class;
  protected $advancedSettingsDataType = '';
  /**
   * The description of the page. The maximum length is 500 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The human-readable name of the page, unique within the flow.
   *
   * @var string
   */
  public $displayName;
  protected $entryFulfillmentType = GoogleCloudDialogflowCxV3Fulfillment::class;
  protected $entryFulfillmentDataType = '';
  protected $eventHandlersType = GoogleCloudDialogflowCxV3EventHandler::class;
  protected $eventHandlersDataType = 'array';
  protected $formType = GoogleCloudDialogflowCxV3Form::class;
  protected $formDataType = '';
  protected $knowledgeConnectorSettingsType = GoogleCloudDialogflowCxV3KnowledgeConnectorSettings::class;
  protected $knowledgeConnectorSettingsDataType = '';
  /**
   * The unique identifier of the page. Required for the Pages.UpdatePage
   * method. Pages.CreatePage populates the name automatically. Format:
   * `projects//locations//agents//flows//pages/`.
   *
   * @var string
   */
  public $name;
  /**
   * Ordered list of `TransitionRouteGroups` added to the page. Transition route
   * groups must be unique within a page. If the page links both flow-level
   * transition route groups and agent-level transition route groups, the flow-
   * level ones will have higher priority and will be put before the agent-level
   * ones. * If multiple transition routes within a page scope refer to the same
   * intent, then the precedence order is: page's transition route -> page's
   * transition route group -> flow's transition routes. * If multiple
   * transition route groups within a page contain the same intent, then the
   * first group in the ordered list takes precedence.
   * Format:`projects//locations//agents//flows//transitionRouteGroups/` or
   * `projects//locations//agents//transitionRouteGroups/` for agent-level
   * groups.
   *
   * @var string[]
   */
  public $transitionRouteGroups;
  protected $transitionRoutesType = GoogleCloudDialogflowCxV3TransitionRoute::class;
  protected $transitionRoutesDataType = 'array';

  /**
   * Hierarchical advanced settings for this page. The settings exposed at the
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
   * The description of the page. The maximum length is 500 characters.
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
   * Required. The human-readable name of the page, unique within the flow.
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
   * The fulfillment to call when the session is entering the page.
   *
   * @param GoogleCloudDialogflowCxV3Fulfillment $entryFulfillment
   */
  public function setEntryFulfillment(GoogleCloudDialogflowCxV3Fulfillment $entryFulfillment)
  {
    $this->entryFulfillment = $entryFulfillment;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Fulfillment
   */
  public function getEntryFulfillment()
  {
    return $this->entryFulfillment;
  }
  /**
   * Handlers associated with the page to handle events such as webhook errors,
   * no match or no input.
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
   * The form associated with the page, used for collecting parameters relevant
   * to the page.
   *
   * @param GoogleCloudDialogflowCxV3Form $form
   */
  public function setForm(GoogleCloudDialogflowCxV3Form $form)
  {
    $this->form = $form;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Form
   */
  public function getForm()
  {
    return $this->form;
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
   * The unique identifier of the page. Required for the Pages.UpdatePage
   * method. Pages.CreatePage populates the name automatically. Format:
   * `projects//locations//agents//flows//pages/`.
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
   * Ordered list of `TransitionRouteGroups` added to the page. Transition route
   * groups must be unique within a page. If the page links both flow-level
   * transition route groups and agent-level transition route groups, the flow-
   * level ones will have higher priority and will be put before the agent-level
   * ones. * If multiple transition routes within a page scope refer to the same
   * intent, then the precedence order is: page's transition route -> page's
   * transition route group -> flow's transition routes. * If multiple
   * transition route groups within a page contain the same intent, then the
   * first group in the ordered list takes precedence.
   * Format:`projects//locations//agents//flows//transitionRouteGroups/` or
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
   * A list of transitions for the transition rules of this page. They route the
   * conversation to another page in the same flow, or another flow. When we are
   * in a certain page, the TransitionRoutes are evaluated in the following
   * order: * TransitionRoutes defined in the page with intent specified. *
   * TransitionRoutes defined in the transition route groups with intent
   * specified. * TransitionRoutes defined in flow with intent specified. *
   * TransitionRoutes defined in the transition route groups with intent
   * specified. * TransitionRoutes defined in the page with only condition
   * specified. * TransitionRoutes defined in the transition route groups with
   * only condition specified.
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
class_alias(GoogleCloudDialogflowCxV3Page::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Page');
