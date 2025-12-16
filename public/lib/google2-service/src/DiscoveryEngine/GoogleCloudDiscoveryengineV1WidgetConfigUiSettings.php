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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1WidgetConfigUiSettings extends \Google\Collection
{
  /**
   * Not specified. Defaults to SEARCH_ONLY.
   */
  public const INTERACTION_TYPE_INTERACTION_TYPE_UNSPECIFIED = 'INTERACTION_TYPE_UNSPECIFIED';
  /**
   * Search without a generative answer.
   */
  public const INTERACTION_TYPE_SEARCH_ONLY = 'SEARCH_ONLY';
  /**
   * Search with the generative answer.
   */
  public const INTERACTION_TYPE_SEARCH_WITH_ANSWER = 'SEARCH_WITH_ANSWER';
  /**
   * Search with the generative answer that supports follow up questions. Also
   * known as multi-turn search.
   */
  public const INTERACTION_TYPE_SEARCH_WITH_FOLLOW_UPS = 'SEARCH_WITH_FOLLOW_UPS';
  /**
   * Unspecified display type (default to showing snippet).
   */
  public const RESULT_DESCRIPTION_TYPE_RESULT_DISPLAY_TYPE_UNSPECIFIED = 'RESULT_DISPLAY_TYPE_UNSPECIFIED';
  /**
   * Display results from the snippet field.
   */
  public const RESULT_DESCRIPTION_TYPE_SNIPPET = 'SNIPPET';
  /**
   * Display results from extractive answers field.
   */
  public const RESULT_DESCRIPTION_TYPE_EXTRACTIVE_ANSWER = 'EXTRACTIVE_ANSWER';
  protected $collection_key = 'dataStoreUiConfigs';
  protected $dataStoreUiConfigsType = GoogleCloudDiscoveryengineV1WidgetConfigDataStoreUiConfig::class;
  protected $dataStoreUiConfigsDataType = 'array';
  /**
   * The default ordering for search results if specified. Used to set
   * SearchRequest#order_by on applicable requests.
   * https://cloud.google.com/generative-ai-app-builder/docs/reference/rest/v1al
   * pha/projects.locations.dataStores.servingConfigs/search#request-body
   *
   * @var string
   */
  public $defaultSearchRequestOrderBy;
  /**
   * If set to true, the widget will not collect user events.
   *
   * @var bool
   */
  public $disableUserEventsCollection;
  /**
   * Whether or not to enable autocomplete.
   *
   * @var bool
   */
  public $enableAutocomplete;
  /**
   * Optional. If set to true, the widget will enable the create agent button.
   *
   * @var bool
   */
  public $enableCreateAgentButton;
  /**
   * Optional. If set to true, the widget will enable people search.
   *
   * @var bool
   */
  public $enablePeopleSearch;
  /**
   * Turn on or off collecting the search result quality feedback from end
   * users.
   *
   * @var bool
   */
  public $enableQualityFeedback;
  /**
   * Whether to enable safe search.
   *
   * @var bool
   */
  public $enableSafeSearch;
  /**
   * Whether to enable search-as-you-type behavior for the search widget.
   *
   * @var bool
   */
  public $enableSearchAsYouType;
  /**
   * If set to true, the widget will enable visual content summary on applicable
   * search requests. Only used by healthcare search.
   *
   * @var bool
   */
  public $enableVisualContentSummary;
  /**
   * Output only. Feature config for the engine to opt in or opt out of
   * features. Supported keys: * `agent-gallery` * `no-code-agent-builder` *
   * `prompt-gallery` * `model-selector` * `notebook-lm` * `people-search` *
   * `people-search-org-chart` * `bi-directional-audio` * `feedback` * `session-
   * sharing` * `personalization-memory` * `disable-agent-sharing` * `disable-
   * image-generation` * `disable-video-generation` * `disable-onedrive-upload`
   * * `disable-talk-to-content` * `disable-google-drive-upload`
   *
   * @var string[]
   */
  public $features;
  protected $generativeAnswerConfigType = GoogleCloudDiscoveryengineV1WidgetConfigUiSettingsGenerativeAnswerConfig::class;
  protected $generativeAnswerConfigDataType = '';
  /**
   * Describes widget (or web app) interaction type
   *
   * @var string
   */
  public $interactionType;
  /**
   * Output only. Maps a model name to its specific configuration for this
   * engine. This allows admin users to turn on/off individual models. This only
   * stores models whose states are overridden by the admin. When the state is
   * unspecified, or model_configs is empty for this model, the system will
   * decide if this model should be available or not based on the default
   * configuration. For example, a preview model should be disabled by default
   * if the admin has not chosen to enable it.
   *
   * @var string[]
   */
  public $modelConfigs;
  /**
   * Controls whether result extract is display and how (snippet or extractive
   * answer). Default to no result if unspecified.
   *
   * @var string
   */
  public $resultDescriptionType;

  /**
   * Per data store configuration.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigDataStoreUiConfig[] $dataStoreUiConfigs
   */
  public function setDataStoreUiConfigs($dataStoreUiConfigs)
  {
    $this->dataStoreUiConfigs = $dataStoreUiConfigs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigDataStoreUiConfig[]
   */
  public function getDataStoreUiConfigs()
  {
    return $this->dataStoreUiConfigs;
  }
  /**
   * The default ordering for search results if specified. Used to set
   * SearchRequest#order_by on applicable requests.
   * https://cloud.google.com/generative-ai-app-builder/docs/reference/rest/v1al
   * pha/projects.locations.dataStores.servingConfigs/search#request-body
   *
   * @param string $defaultSearchRequestOrderBy
   */
  public function setDefaultSearchRequestOrderBy($defaultSearchRequestOrderBy)
  {
    $this->defaultSearchRequestOrderBy = $defaultSearchRequestOrderBy;
  }
  /**
   * @return string
   */
  public function getDefaultSearchRequestOrderBy()
  {
    return $this->defaultSearchRequestOrderBy;
  }
  /**
   * If set to true, the widget will not collect user events.
   *
   * @param bool $disableUserEventsCollection
   */
  public function setDisableUserEventsCollection($disableUserEventsCollection)
  {
    $this->disableUserEventsCollection = $disableUserEventsCollection;
  }
  /**
   * @return bool
   */
  public function getDisableUserEventsCollection()
  {
    return $this->disableUserEventsCollection;
  }
  /**
   * Whether or not to enable autocomplete.
   *
   * @param bool $enableAutocomplete
   */
  public function setEnableAutocomplete($enableAutocomplete)
  {
    $this->enableAutocomplete = $enableAutocomplete;
  }
  /**
   * @return bool
   */
  public function getEnableAutocomplete()
  {
    return $this->enableAutocomplete;
  }
  /**
   * Optional. If set to true, the widget will enable the create agent button.
   *
   * @param bool $enableCreateAgentButton
   */
  public function setEnableCreateAgentButton($enableCreateAgentButton)
  {
    $this->enableCreateAgentButton = $enableCreateAgentButton;
  }
  /**
   * @return bool
   */
  public function getEnableCreateAgentButton()
  {
    return $this->enableCreateAgentButton;
  }
  /**
   * Optional. If set to true, the widget will enable people search.
   *
   * @param bool $enablePeopleSearch
   */
  public function setEnablePeopleSearch($enablePeopleSearch)
  {
    $this->enablePeopleSearch = $enablePeopleSearch;
  }
  /**
   * @return bool
   */
  public function getEnablePeopleSearch()
  {
    return $this->enablePeopleSearch;
  }
  /**
   * Turn on or off collecting the search result quality feedback from end
   * users.
   *
   * @param bool $enableQualityFeedback
   */
  public function setEnableQualityFeedback($enableQualityFeedback)
  {
    $this->enableQualityFeedback = $enableQualityFeedback;
  }
  /**
   * @return bool
   */
  public function getEnableQualityFeedback()
  {
    return $this->enableQualityFeedback;
  }
  /**
   * Whether to enable safe search.
   *
   * @param bool $enableSafeSearch
   */
  public function setEnableSafeSearch($enableSafeSearch)
  {
    $this->enableSafeSearch = $enableSafeSearch;
  }
  /**
   * @return bool
   */
  public function getEnableSafeSearch()
  {
    return $this->enableSafeSearch;
  }
  /**
   * Whether to enable search-as-you-type behavior for the search widget.
   *
   * @param bool $enableSearchAsYouType
   */
  public function setEnableSearchAsYouType($enableSearchAsYouType)
  {
    $this->enableSearchAsYouType = $enableSearchAsYouType;
  }
  /**
   * @return bool
   */
  public function getEnableSearchAsYouType()
  {
    return $this->enableSearchAsYouType;
  }
  /**
   * If set to true, the widget will enable visual content summary on applicable
   * search requests. Only used by healthcare search.
   *
   * @param bool $enableVisualContentSummary
   */
  public function setEnableVisualContentSummary($enableVisualContentSummary)
  {
    $this->enableVisualContentSummary = $enableVisualContentSummary;
  }
  /**
   * @return bool
   */
  public function getEnableVisualContentSummary()
  {
    return $this->enableVisualContentSummary;
  }
  /**
   * Output only. Feature config for the engine to opt in or opt out of
   * features. Supported keys: * `agent-gallery` * `no-code-agent-builder` *
   * `prompt-gallery` * `model-selector` * `notebook-lm` * `people-search` *
   * `people-search-org-chart` * `bi-directional-audio` * `feedback` * `session-
   * sharing` * `personalization-memory` * `disable-agent-sharing` * `disable-
   * image-generation` * `disable-video-generation` * `disable-onedrive-upload`
   * * `disable-talk-to-content` * `disable-google-drive-upload`
   *
   * @param string[] $features
   */
  public function setFeatures($features)
  {
    $this->features = $features;
  }
  /**
   * @return string[]
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * Describes generative answer configuration.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigUiSettingsGenerativeAnswerConfig $generativeAnswerConfig
   */
  public function setGenerativeAnswerConfig(GoogleCloudDiscoveryengineV1WidgetConfigUiSettingsGenerativeAnswerConfig $generativeAnswerConfig)
  {
    $this->generativeAnswerConfig = $generativeAnswerConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigUiSettingsGenerativeAnswerConfig
   */
  public function getGenerativeAnswerConfig()
  {
    return $this->generativeAnswerConfig;
  }
  /**
   * Describes widget (or web app) interaction type
   *
   * Accepted values: INTERACTION_TYPE_UNSPECIFIED, SEARCH_ONLY,
   * SEARCH_WITH_ANSWER, SEARCH_WITH_FOLLOW_UPS
   *
   * @param self::INTERACTION_TYPE_* $interactionType
   */
  public function setInteractionType($interactionType)
  {
    $this->interactionType = $interactionType;
  }
  /**
   * @return self::INTERACTION_TYPE_*
   */
  public function getInteractionType()
  {
    return $this->interactionType;
  }
  /**
   * Output only. Maps a model name to its specific configuration for this
   * engine. This allows admin users to turn on/off individual models. This only
   * stores models whose states are overridden by the admin. When the state is
   * unspecified, or model_configs is empty for this model, the system will
   * decide if this model should be available or not based on the default
   * configuration. For example, a preview model should be disabled by default
   * if the admin has not chosen to enable it.
   *
   * @param string[] $modelConfigs
   */
  public function setModelConfigs($modelConfigs)
  {
    $this->modelConfigs = $modelConfigs;
  }
  /**
   * @return string[]
   */
  public function getModelConfigs()
  {
    return $this->modelConfigs;
  }
  /**
   * Controls whether result extract is display and how (snippet or extractive
   * answer). Default to no result if unspecified.
   *
   * Accepted values: RESULT_DISPLAY_TYPE_UNSPECIFIED, SNIPPET,
   * EXTRACTIVE_ANSWER
   *
   * @param self::RESULT_DESCRIPTION_TYPE_* $resultDescriptionType
   */
  public function setResultDescriptionType($resultDescriptionType)
  {
    $this->resultDescriptionType = $resultDescriptionType;
  }
  /**
   * @return self::RESULT_DESCRIPTION_TYPE_*
   */
  public function getResultDescriptionType()
  {
    return $this->resultDescriptionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigUiSettings::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigUiSettings');
