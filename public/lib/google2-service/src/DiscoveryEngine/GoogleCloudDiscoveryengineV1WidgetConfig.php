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

class GoogleCloudDiscoveryengineV1WidgetConfig extends \Google\Collection
{
  /**
   * Unspecified data store type.
   */
  public const DATA_STORE_TYPE_DATA_STORE_TYPE_UNSPECIFIED = 'DATA_STORE_TYPE_UNSPECIFIED';
  /**
   * The parent data store contains a site search engine.
   */
  public const DATA_STORE_TYPE_SITE_SEARCH = 'SITE_SEARCH';
  /**
   * The parent data store contains a search engine for structured data.
   */
  public const DATA_STORE_TYPE_STRUCTURED = 'STRUCTURED';
  /**
   * The parent data store contains a search engine for unstructured data.
   */
  public const DATA_STORE_TYPE_UNSTRUCTURED = 'UNSTRUCTURED';
  /**
   * The parent data store is served for blended search with multiple data
   * stores.
   */
  public const DATA_STORE_TYPE_BLENDED = 'BLENDED';
  /**
   * Value used when unset.
   */
  public const INDUSTRY_VERTICAL_INDUSTRY_VERTICAL_UNSPECIFIED = 'INDUSTRY_VERTICAL_UNSPECIFIED';
  /**
   * The generic vertical for documents that are not specific to any industry
   * vertical.
   */
  public const INDUSTRY_VERTICAL_GENERIC = 'GENERIC';
  /**
   * The media industry vertical.
   */
  public const INDUSTRY_VERTICAL_MEDIA = 'MEDIA';
  /**
   * The healthcare FHIR vertical.
   */
  public const INDUSTRY_VERTICAL_HEALTHCARE_FHIR = 'HEALTHCARE_FHIR';
  /**
   * Unspecified display type (default to showing snippet).
   */
  public const RESULT_DISPLAY_TYPE_RESULT_DISPLAY_TYPE_UNSPECIFIED = 'RESULT_DISPLAY_TYPE_UNSPECIFIED';
  /**
   * Display results from the snippet field.
   */
  public const RESULT_DISPLAY_TYPE_SNIPPET = 'SNIPPET';
  /**
   * Display results from extractive answers field.
   */
  public const RESULT_DISPLAY_TYPE_EXTRACTIVE_ANSWER = 'EXTRACTIVE_ANSWER';
  /**
   * Default value.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_UNSPECIFIED = 'SOLUTION_TYPE_UNSPECIFIED';
  /**
   * Used for Recommendations AI.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_RECOMMENDATION = 'SOLUTION_TYPE_RECOMMENDATION';
  /**
   * Used for Discovery Search.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_SEARCH = 'SOLUTION_TYPE_SEARCH';
  /**
   * Used for use cases related to the Generative AI agent.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_CHAT = 'SOLUTION_TYPE_CHAT';
  /**
   * Used for use cases related to the Generative Chat agent. It's used for
   * Generative chat engine only, the associated data stores must enrolled with
   * `SOLUTION_TYPE_CHAT` solution.
   */
  public const SOLUTION_TYPE_SOLUTION_TYPE_GENERATIVE_CHAT = 'SOLUTION_TYPE_GENERATIVE_CHAT';
  protected $collection_key = 'facetField';
  protected $accessSettingsType = GoogleCloudDiscoveryengineV1WidgetConfigAccessSettings::class;
  protected $accessSettingsDataType = '';
  /**
   * Whether allow no-auth integration with widget. If set true, public access
   * to search or other solutions from widget is allowed without authenication
   * token provided by customer hosted backend server.
   *
   * @deprecated
   * @var bool
   */
  public $allowPublicAccess;
  /**
   * Allowlisted domains that can load this widget.
   *
   * @deprecated
   * @var string[]
   */
  public $allowlistedDomains;
  protected $assistantSettingsType = GoogleCloudDiscoveryengineV1WidgetConfigAssistantSettings::class;
  protected $assistantSettingsDataType = '';
  protected $collectionComponentsType = GoogleCloudDiscoveryengineV1WidgetConfigCollectionComponent::class;
  protected $collectionComponentsDataType = 'array';
  /**
   * Output only. Unique obfuscated identifier of a WidgetConfig.
   *
   * @var string
   */
  public $configId;
  protected $contentSearchSpecType = GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpec::class;
  protected $contentSearchSpecDataType = '';
  /**
   * Output only. Timestamp the WidgetConfig was created.
   *
   * @var string
   */
  public $createTime;
  protected $customerProvidedConfigType = GoogleCloudDiscoveryengineV1WidgetConfigCustomerProvidedConfig::class;
  protected $customerProvidedConfigDataType = '';
  /**
   * Output only. The type of the parent data store.
   *
   * @var string
   */
  public $dataStoreType;
  protected $dataStoreUiConfigsType = GoogleCloudDiscoveryengineV1WidgetConfigDataStoreUiConfig::class;
  protected $dataStoreUiConfigsDataType = 'array';
  /**
   * The default ordering for search results if specified. Used to set
   * SearchRequest#order_by on applicable requests.
   * https://cloud.google.com/generative-ai-app-builder/docs/reference/rest/v1al
   * pha/projects.locations.dataStores.servingConfigs/search#request-body
   *
   * @deprecated
   * @var string
   */
  public $defaultSearchRequestOrderBy;
  /**
   * Required. The human readable widget config display name. Used in Discovery
   * UI. This field must be a UTF-8 encoded string with a length limit of 128
   * characters. Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $displayName;
  /**
   * Whether or not to enable autocomplete.
   *
   * @deprecated
   * @var bool
   */
  public $enableAutocomplete;
  /**
   * Whether to allow conversational search (LLM, multi-turn) or not (non-LLM,
   * single-turn).
   *
   * @deprecated
   * @var bool
   */
  public $enableConversationalSearch;
  /**
   * Optional. Output only. Whether to enable private knowledge graph.
   *
   * @var bool
   */
  public $enablePrivateKnowledgeGraph;
  /**
   * Turn on or off collecting the search result quality feedback from end
   * users.
   *
   * @deprecated
   * @var bool
   */
  public $enableQualityFeedback;
  /**
   * Whether to show the result score.
   *
   * @var bool
   */
  public $enableResultScore;
  /**
   * Whether to enable safe search.
   *
   * @deprecated
   * @var bool
   */
  public $enableSafeSearch;
  /**
   * Whether to enable search-as-you-type behavior for the search widget
   *
   * @deprecated
   * @var bool
   */
  public $enableSearchAsYouType;
  /**
   * Turn on or off summary for each snippets result.
   *
   * @deprecated
   * @var bool
   */
  public $enableSnippetResultSummary;
  /**
   * Turn on or off summarization for the search response.
   *
   * @deprecated
   * @var bool
   */
  public $enableSummarization;
  /**
   * Whether to enable standalone web app.
   *
   * @deprecated
   * @var bool
   */
  public $enableWebApp;
  protected $facetFieldType = GoogleCloudDiscoveryengineV1WidgetConfigFacetField::class;
  protected $facetFieldDataType = 'array';
  protected $fieldsUiComponentsMapType = GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField::class;
  protected $fieldsUiComponentsMapDataType = 'map';
  /**
   * Output only. Whether the subscription is gemini bundle or not.
   *
   * @var bool
   */
  public $geminiBundle;
  protected $homepageSettingType = GoogleCloudDiscoveryengineV1WidgetConfigHomepageSetting::class;
  protected $homepageSettingDataType = '';
  /**
   * Output only. The industry vertical that the WidgetConfig registers. The
   * WidgetConfig industry vertical is based on the associated Engine.
   *
   * @var string
   */
  public $industryVertical;
  /**
   * Output only. Whether LLM is enabled in the corresponding data store.
   *
   * @var bool
   */
  public $llmEnabled;
  /**
   * Output only. Whether the customer accepted data use terms.
   *
   * @var bool
   */
  public $minimumDataTermAccepted;
  /**
   * Immutable. The full resource name of the widget config. Format: `projects/{
   * project}/locations/{location}/collections/{collection_id}/dataStores/{data_
   * store_id}/widgetConfigs/{widget_config_id}`. This field must be a UTF-8
   * encoded string with a length limit of 1024 characters.
   *
   * @var string
   */
  public $name;
  /**
   * The type of snippet to display in UCS widget. -
   * RESULT_DISPLAY_TYPE_UNSPECIFIED for existing users. - SNIPPET for new non-
   * enterprise search users. - EXTRACTIVE_ANSWER for new enterprise search
   * users.
   *
   * @deprecated
   * @var string
   */
  public $resultDisplayType;
  /**
   * Required. Immutable. Specifies the solution type that this WidgetConfig can
   * be used for.
   *
   * @var string
   */
  public $solutionType;
  protected $uiBrandingType = GoogleCloudDiscoveryengineV1WidgetConfigUiBrandingSettings::class;
  protected $uiBrandingDataType = '';
  protected $uiSettingsType = GoogleCloudDiscoveryengineV1WidgetConfigUiSettings::class;
  protected $uiSettingsDataType = '';
  /**
   * Output only. Timestamp the WidgetConfig was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Will be used for all widget access settings seen in cloud console
   * integration page. Replaces top deprecated top level properties.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigAccessSettings $accessSettings
   */
  public function setAccessSettings(GoogleCloudDiscoveryengineV1WidgetConfigAccessSettings $accessSettings)
  {
    $this->accessSettings = $accessSettings;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigAccessSettings
   */
  public function getAccessSettings()
  {
    return $this->accessSettings;
  }
  /**
   * Whether allow no-auth integration with widget. If set true, public access
   * to search or other solutions from widget is allowed without authenication
   * token provided by customer hosted backend server.
   *
   * @deprecated
   * @param bool $allowPublicAccess
   */
  public function setAllowPublicAccess($allowPublicAccess)
  {
    $this->allowPublicAccess = $allowPublicAccess;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getAllowPublicAccess()
  {
    return $this->allowPublicAccess;
  }
  /**
   * Allowlisted domains that can load this widget.
   *
   * @deprecated
   * @param string[] $allowlistedDomains
   */
  public function setAllowlistedDomains($allowlistedDomains)
  {
    $this->allowlistedDomains = $allowlistedDomains;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getAllowlistedDomains()
  {
    return $this->allowlistedDomains;
  }
  /**
   * Optional. Output only. Describes the assistant settings of the widget.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigAssistantSettings $assistantSettings
   */
  public function setAssistantSettings(GoogleCloudDiscoveryengineV1WidgetConfigAssistantSettings $assistantSettings)
  {
    $this->assistantSettings = $assistantSettings;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigAssistantSettings
   */
  public function getAssistantSettings()
  {
    return $this->assistantSettings;
  }
  /**
   * Output only. Collection components that lists all collections and child
   * data stores associated with the widget config, those data sources can be
   * used for filtering in widget service APIs, users can return results that
   * from selected data sources.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigCollectionComponent[] $collectionComponents
   */
  public function setCollectionComponents($collectionComponents)
  {
    $this->collectionComponents = $collectionComponents;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigCollectionComponent[]
   */
  public function getCollectionComponents()
  {
    return $this->collectionComponents;
  }
  /**
   * Output only. Unique obfuscated identifier of a WidgetConfig.
   *
   * @param string $configId
   */
  public function setConfigId($configId)
  {
    $this->configId = $configId;
  }
  /**
   * @return string
   */
  public function getConfigId()
  {
    return $this->configId;
  }
  /**
   * The content search spec that configs the desired behavior of content
   * search.
   *
   * @deprecated
   * @param GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpec $contentSearchSpec
   */
  public function setContentSearchSpec(GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpec $contentSearchSpec)
  {
    $this->contentSearchSpec = $contentSearchSpec;
  }
  /**
   * @deprecated
   * @return GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpec
   */
  public function getContentSearchSpec()
  {
    return $this->contentSearchSpec;
  }
  /**
   * Output only. Timestamp the WidgetConfig was created.
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
   * Optional. Output only. Describes the customer related configurations,
   * currently only used for government customers. This field cannot be modified
   * after project onboarding.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigCustomerProvidedConfig $customerProvidedConfig
   */
  public function setCustomerProvidedConfig(GoogleCloudDiscoveryengineV1WidgetConfigCustomerProvidedConfig $customerProvidedConfig)
  {
    $this->customerProvidedConfig = $customerProvidedConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigCustomerProvidedConfig
   */
  public function getCustomerProvidedConfig()
  {
    return $this->customerProvidedConfig;
  }
  /**
   * Output only. The type of the parent data store.
   *
   * Accepted values: DATA_STORE_TYPE_UNSPECIFIED, SITE_SEARCH, STRUCTURED,
   * UNSTRUCTURED, BLENDED
   *
   * @param self::DATA_STORE_TYPE_* $dataStoreType
   */
  public function setDataStoreType($dataStoreType)
  {
    $this->dataStoreType = $dataStoreType;
  }
  /**
   * @return self::DATA_STORE_TYPE_*
   */
  public function getDataStoreType()
  {
    return $this->dataStoreType;
  }
  /**
   * Configurable UI configurations per data store.
   *
   * @deprecated
   * @param GoogleCloudDiscoveryengineV1WidgetConfigDataStoreUiConfig[] $dataStoreUiConfigs
   */
  public function setDataStoreUiConfigs($dataStoreUiConfigs)
  {
    $this->dataStoreUiConfigs = $dataStoreUiConfigs;
  }
  /**
   * @deprecated
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
   * @deprecated
   * @param string $defaultSearchRequestOrderBy
   */
  public function setDefaultSearchRequestOrderBy($defaultSearchRequestOrderBy)
  {
    $this->defaultSearchRequestOrderBy = $defaultSearchRequestOrderBy;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDefaultSearchRequestOrderBy()
  {
    return $this->defaultSearchRequestOrderBy;
  }
  /**
   * Required. The human readable widget config display name. Used in Discovery
   * UI. This field must be a UTF-8 encoded string with a length limit of 128
   * characters. Otherwise, an INVALID_ARGUMENT error is returned.
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
   * Whether or not to enable autocomplete.
   *
   * @deprecated
   * @param bool $enableAutocomplete
   */
  public function setEnableAutocomplete($enableAutocomplete)
  {
    $this->enableAutocomplete = $enableAutocomplete;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableAutocomplete()
  {
    return $this->enableAutocomplete;
  }
  /**
   * Whether to allow conversational search (LLM, multi-turn) or not (non-LLM,
   * single-turn).
   *
   * @deprecated
   * @param bool $enableConversationalSearch
   */
  public function setEnableConversationalSearch($enableConversationalSearch)
  {
    $this->enableConversationalSearch = $enableConversationalSearch;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableConversationalSearch()
  {
    return $this->enableConversationalSearch;
  }
  /**
   * Optional. Output only. Whether to enable private knowledge graph.
   *
   * @param bool $enablePrivateKnowledgeGraph
   */
  public function setEnablePrivateKnowledgeGraph($enablePrivateKnowledgeGraph)
  {
    $this->enablePrivateKnowledgeGraph = $enablePrivateKnowledgeGraph;
  }
  /**
   * @return bool
   */
  public function getEnablePrivateKnowledgeGraph()
  {
    return $this->enablePrivateKnowledgeGraph;
  }
  /**
   * Turn on or off collecting the search result quality feedback from end
   * users.
   *
   * @deprecated
   * @param bool $enableQualityFeedback
   */
  public function setEnableQualityFeedback($enableQualityFeedback)
  {
    $this->enableQualityFeedback = $enableQualityFeedback;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableQualityFeedback()
  {
    return $this->enableQualityFeedback;
  }
  /**
   * Whether to show the result score.
   *
   * @param bool $enableResultScore
   */
  public function setEnableResultScore($enableResultScore)
  {
    $this->enableResultScore = $enableResultScore;
  }
  /**
   * @return bool
   */
  public function getEnableResultScore()
  {
    return $this->enableResultScore;
  }
  /**
   * Whether to enable safe search.
   *
   * @deprecated
   * @param bool $enableSafeSearch
   */
  public function setEnableSafeSearch($enableSafeSearch)
  {
    $this->enableSafeSearch = $enableSafeSearch;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableSafeSearch()
  {
    return $this->enableSafeSearch;
  }
  /**
   * Whether to enable search-as-you-type behavior for the search widget
   *
   * @deprecated
   * @param bool $enableSearchAsYouType
   */
  public function setEnableSearchAsYouType($enableSearchAsYouType)
  {
    $this->enableSearchAsYouType = $enableSearchAsYouType;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableSearchAsYouType()
  {
    return $this->enableSearchAsYouType;
  }
  /**
   * Turn on or off summary for each snippets result.
   *
   * @deprecated
   * @param bool $enableSnippetResultSummary
   */
  public function setEnableSnippetResultSummary($enableSnippetResultSummary)
  {
    $this->enableSnippetResultSummary = $enableSnippetResultSummary;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableSnippetResultSummary()
  {
    return $this->enableSnippetResultSummary;
  }
  /**
   * Turn on or off summarization for the search response.
   *
   * @deprecated
   * @param bool $enableSummarization
   */
  public function setEnableSummarization($enableSummarization)
  {
    $this->enableSummarization = $enableSummarization;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableSummarization()
  {
    return $this->enableSummarization;
  }
  /**
   * Whether to enable standalone web app.
   *
   * @deprecated
   * @param bool $enableWebApp
   */
  public function setEnableWebApp($enableWebApp)
  {
    $this->enableWebApp = $enableWebApp;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableWebApp()
  {
    return $this->enableWebApp;
  }
  /**
   * The configuration and appearance of facets in the end user view.
   *
   * @deprecated
   * @param GoogleCloudDiscoveryengineV1WidgetConfigFacetField[] $facetField
   */
  public function setFacetField($facetField)
  {
    $this->facetField = $facetField;
  }
  /**
   * @deprecated
   * @return GoogleCloudDiscoveryengineV1WidgetConfigFacetField[]
   */
  public function getFacetField()
  {
    return $this->facetField;
  }
  /**
   * The key is the UI component. Mock. Currently supported `title`,
   * `thumbnail`, `url`, `custom1`, `custom2`, `custom3`. The value is the name
   * of the field along with its device visibility. The 3 custom fields are
   * optional and can be added or removed. `title`, `thumbnail`, `url` are
   * required UI components that cannot be removed.
   *
   * @deprecated
   * @param GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField[] $fieldsUiComponentsMap
   */
  public function setFieldsUiComponentsMap($fieldsUiComponentsMap)
  {
    $this->fieldsUiComponentsMap = $fieldsUiComponentsMap;
  }
  /**
   * @deprecated
   * @return GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField[]
   */
  public function getFieldsUiComponentsMap()
  {
    return $this->fieldsUiComponentsMap;
  }
  /**
   * Output only. Whether the subscription is gemini bundle or not.
   *
   * @param bool $geminiBundle
   */
  public function setGeminiBundle($geminiBundle)
  {
    $this->geminiBundle = $geminiBundle;
  }
  /**
   * @return bool
   */
  public function getGeminiBundle()
  {
    return $this->geminiBundle;
  }
  /**
   * Optional. Describes the homepage settings of the widget.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigHomepageSetting $homepageSetting
   */
  public function setHomepageSetting(GoogleCloudDiscoveryengineV1WidgetConfigHomepageSetting $homepageSetting)
  {
    $this->homepageSetting = $homepageSetting;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigHomepageSetting
   */
  public function getHomepageSetting()
  {
    return $this->homepageSetting;
  }
  /**
   * Output only. The industry vertical that the WidgetConfig registers. The
   * WidgetConfig industry vertical is based on the associated Engine.
   *
   * Accepted values: INDUSTRY_VERTICAL_UNSPECIFIED, GENERIC, MEDIA,
   * HEALTHCARE_FHIR
   *
   * @param self::INDUSTRY_VERTICAL_* $industryVertical
   */
  public function setIndustryVertical($industryVertical)
  {
    $this->industryVertical = $industryVertical;
  }
  /**
   * @return self::INDUSTRY_VERTICAL_*
   */
  public function getIndustryVertical()
  {
    return $this->industryVertical;
  }
  /**
   * Output only. Whether LLM is enabled in the corresponding data store.
   *
   * @param bool $llmEnabled
   */
  public function setLlmEnabled($llmEnabled)
  {
    $this->llmEnabled = $llmEnabled;
  }
  /**
   * @return bool
   */
  public function getLlmEnabled()
  {
    return $this->llmEnabled;
  }
  /**
   * Output only. Whether the customer accepted data use terms.
   *
   * @param bool $minimumDataTermAccepted
   */
  public function setMinimumDataTermAccepted($minimumDataTermAccepted)
  {
    $this->minimumDataTermAccepted = $minimumDataTermAccepted;
  }
  /**
   * @return bool
   */
  public function getMinimumDataTermAccepted()
  {
    return $this->minimumDataTermAccepted;
  }
  /**
   * Immutable. The full resource name of the widget config. Format: `projects/{
   * project}/locations/{location}/collections/{collection_id}/dataStores/{data_
   * store_id}/widgetConfigs/{widget_config_id}`. This field must be a UTF-8
   * encoded string with a length limit of 1024 characters.
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
   * The type of snippet to display in UCS widget. -
   * RESULT_DISPLAY_TYPE_UNSPECIFIED for existing users. - SNIPPET for new non-
   * enterprise search users. - EXTRACTIVE_ANSWER for new enterprise search
   * users.
   *
   * Accepted values: RESULT_DISPLAY_TYPE_UNSPECIFIED, SNIPPET,
   * EXTRACTIVE_ANSWER
   *
   * @deprecated
   * @param self::RESULT_DISPLAY_TYPE_* $resultDisplayType
   */
  public function setResultDisplayType($resultDisplayType)
  {
    $this->resultDisplayType = $resultDisplayType;
  }
  /**
   * @deprecated
   * @return self::RESULT_DISPLAY_TYPE_*
   */
  public function getResultDisplayType()
  {
    return $this->resultDisplayType;
  }
  /**
   * Required. Immutable. Specifies the solution type that this WidgetConfig can
   * be used for.
   *
   * Accepted values: SOLUTION_TYPE_UNSPECIFIED, SOLUTION_TYPE_RECOMMENDATION,
   * SOLUTION_TYPE_SEARCH, SOLUTION_TYPE_CHAT, SOLUTION_TYPE_GENERATIVE_CHAT
   *
   * @param self::SOLUTION_TYPE_* $solutionType
   */
  public function setSolutionType($solutionType)
  {
    $this->solutionType = $solutionType;
  }
  /**
   * @return self::SOLUTION_TYPE_*
   */
  public function getSolutionType()
  {
    return $this->solutionType;
  }
  /**
   * Describes search widget UI branding settings, such as the widget title,
   * logo, favicons, and colors.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigUiBrandingSettings $uiBranding
   */
  public function setUiBranding(GoogleCloudDiscoveryengineV1WidgetConfigUiBrandingSettings $uiBranding)
  {
    $this->uiBranding = $uiBranding;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigUiBrandingSettings
   */
  public function getUiBranding()
  {
    return $this->uiBranding;
  }
  /**
   * Describes general widget search settings as seen in cloud console widget
   * configuration page. Replaces top deprecated top level properties.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigUiSettings $uiSettings
   */
  public function setUiSettings(GoogleCloudDiscoveryengineV1WidgetConfigUiSettings $uiSettings)
  {
    $this->uiSettings = $uiSettings;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigUiSettings
   */
  public function getUiSettings()
  {
    return $this->uiSettings;
  }
  /**
   * Output only. Timestamp the WidgetConfig was updated.
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
class_alias(GoogleCloudDiscoveryengineV1WidgetConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfig');
