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

class GoogleCloudDiscoveryengineV1betaEngine extends \Google\Collection
{
  /**
   * All non specified apps.
   */
  public const APP_TYPE_APP_TYPE_UNSPECIFIED = 'APP_TYPE_UNSPECIFIED';
  /**
   * App type for intranet search and Agentspace.
   */
  public const APP_TYPE_APP_TYPE_INTRANET = 'APP_TYPE_INTRANET';
  /**
   * Default value. For Spark and non-Spark non-configurable billing approach.
   * General pricing model.
   */
  public const CONFIGURABLE_BILLING_APPROACH_CONFIGURABLE_BILLING_APPROACH_UNSPECIFIED = 'CONFIGURABLE_BILLING_APPROACH_UNSPECIFIED';
  /**
   * The billing approach follows configurations specified by customer.
   */
  public const CONFIGURABLE_BILLING_APPROACH_CONFIGURABLE_BILLING_APPROACH_ENABLED = 'CONFIGURABLE_BILLING_APPROACH_ENABLED';
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
  protected $collection_key = 'dataStoreIds';
  /**
   * Optional. Immutable. This the application type which this engine resource
   * represents. NOTE: this is a new concept independ of existing industry
   * vertical or solution type.
   *
   * @var string
   */
  public $appType;
  protected $chatEngineConfigType = GoogleCloudDiscoveryengineV1betaEngineChatEngineConfig::class;
  protected $chatEngineConfigDataType = '';
  protected $chatEngineMetadataType = GoogleCloudDiscoveryengineV1betaEngineChatEngineMetadata::class;
  protected $chatEngineMetadataDataType = '';
  protected $cmekConfigType = GoogleCloudDiscoveryengineV1betaCmekConfig::class;
  protected $cmekConfigDataType = '';
  protected $commonConfigType = GoogleCloudDiscoveryengineV1betaEngineCommonConfig::class;
  protected $commonConfigDataType = '';
  /**
   * Optional. Configuration for configurable billing approach.
   *
   * @var string
   */
  public $configurableBillingApproach;
  /**
   * Output only. Timestamp the Recommendation Engine was created at.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The data stores associated with this engine. For
   * SOLUTION_TYPE_SEARCH and SOLUTION_TYPE_RECOMMENDATION type of engines, they
   * can only associate with at most one data store. If solution_type is
   * SOLUTION_TYPE_CHAT, multiple DataStores in the same Collection can be
   * associated here. Note that when used in CreateEngineRequest, one DataStore
   * id must be provided as the system will use it for necessary
   * initializations.
   *
   * @var string[]
   */
  public $dataStoreIds;
  /**
   * Optional. Whether to disable analytics for searches performed on this
   * engine.
   *
   * @var bool
   */
  public $disableAnalytics;
  /**
   * Required. The display name of the engine. Should be human readable. UTF-8
   * encoded string with limit of 1024 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Feature config for the engine to opt in or opt out of features.
   * Supported keys: * `*`: all features, if it's present, all other feature
   * state settings are ignored. * `agent-gallery` * `no-code-agent-builder` *
   * `prompt-gallery` * `model-selector` * `notebook-lm` * `people-search` *
   * `people-search-org-chart` * `bi-directional-audio` * `feedback` * `session-
   * sharing` * `personalization-memory` * `disable-agent-sharing` * `disable-
   * image-generation` * `disable-video-generation` * `disable-onedrive-upload`
   * * `disable-talk-to-content` * `disable-google-drive-upload`
   *
   * @var string[]
   */
  public $features;
  /**
   * Optional. The industry vertical that the engine registers. The restriction
   * of the Engine industry vertical is based on DataStore: Vertical on Engine
   * has to match vertical of the DataStore linked to the engine.
   *
   * @var string
   */
  public $industryVertical;
  protected $mediaRecommendationEngineConfigType = GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfig::class;
  protected $mediaRecommendationEngineConfigDataType = '';
  /**
   * Optional. Maps a model name to its specific configuration for this engine.
   * This allows admin users to turn on/off individual models. This only stores
   * models whose states are overridden by the admin. When the state is
   * unspecified, or model_configs is empty for this model, the system will
   * decide if this model should be available or not based on the default
   * configuration. For example, a preview model should be disabled by default
   * if the admin has not chosen to enable it.
   *
   * @var string[]
   */
  public $modelConfigs;
  /**
   * Immutable. Identifier. The fully qualified resource name of the engine.
   * This field must be a UTF-8 encoded string with a length limit of 1024
   * characters. Format: `projects/{project}/locations/{location}/collections/{c
   * ollection}/engines/{engine}` engine should be 1-63 characters, and valid
   * characters are /a-z0-9. Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $name;
  protected $searchEngineConfigType = GoogleCloudDiscoveryengineV1betaEngineSearchEngineConfig::class;
  protected $searchEngineConfigDataType = '';
  /**
   * Required. The solutions of the engine.
   *
   * @var string
   */
  public $solutionType;
  /**
   * Output only. Timestamp the Recommendation Engine was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Immutable. This the application type which this engine resource
   * represents. NOTE: this is a new concept independ of existing industry
   * vertical or solution type.
   *
   * Accepted values: APP_TYPE_UNSPECIFIED, APP_TYPE_INTRANET
   *
   * @param self::APP_TYPE_* $appType
   */
  public function setAppType($appType)
  {
    $this->appType = $appType;
  }
  /**
   * @return self::APP_TYPE_*
   */
  public function getAppType()
  {
    return $this->appType;
  }
  /**
   * Configurations for the Chat Engine. Only applicable if solution_type is
   * SOLUTION_TYPE_CHAT.
   *
   * @param GoogleCloudDiscoveryengineV1betaEngineChatEngineConfig $chatEngineConfig
   */
  public function setChatEngineConfig(GoogleCloudDiscoveryengineV1betaEngineChatEngineConfig $chatEngineConfig)
  {
    $this->chatEngineConfig = $chatEngineConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaEngineChatEngineConfig
   */
  public function getChatEngineConfig()
  {
    return $this->chatEngineConfig;
  }
  /**
   * Output only. Additional information of the Chat Engine. Only applicable if
   * solution_type is SOLUTION_TYPE_CHAT.
   *
   * @param GoogleCloudDiscoveryengineV1betaEngineChatEngineMetadata $chatEngineMetadata
   */
  public function setChatEngineMetadata(GoogleCloudDiscoveryengineV1betaEngineChatEngineMetadata $chatEngineMetadata)
  {
    $this->chatEngineMetadata = $chatEngineMetadata;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaEngineChatEngineMetadata
   */
  public function getChatEngineMetadata()
  {
    return $this->chatEngineMetadata;
  }
  /**
   * Output only. CMEK-related information for the Engine.
   *
   * @param GoogleCloudDiscoveryengineV1betaCmekConfig $cmekConfig
   */
  public function setCmekConfig(GoogleCloudDiscoveryengineV1betaCmekConfig $cmekConfig)
  {
    $this->cmekConfig = $cmekConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCmekConfig
   */
  public function getCmekConfig()
  {
    return $this->cmekConfig;
  }
  /**
   * Common config spec that specifies the metadata of the engine.
   *
   * @param GoogleCloudDiscoveryengineV1betaEngineCommonConfig $commonConfig
   */
  public function setCommonConfig(GoogleCloudDiscoveryengineV1betaEngineCommonConfig $commonConfig)
  {
    $this->commonConfig = $commonConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaEngineCommonConfig
   */
  public function getCommonConfig()
  {
    return $this->commonConfig;
  }
  /**
   * Optional. Configuration for configurable billing approach.
   *
   * Accepted values: CONFIGURABLE_BILLING_APPROACH_UNSPECIFIED,
   * CONFIGURABLE_BILLING_APPROACH_ENABLED
   *
   * @param self::CONFIGURABLE_BILLING_APPROACH_* $configurableBillingApproach
   */
  public function setConfigurableBillingApproach($configurableBillingApproach)
  {
    $this->configurableBillingApproach = $configurableBillingApproach;
  }
  /**
   * @return self::CONFIGURABLE_BILLING_APPROACH_*
   */
  public function getConfigurableBillingApproach()
  {
    return $this->configurableBillingApproach;
  }
  /**
   * Output only. Timestamp the Recommendation Engine was created at.
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
   * Optional. The data stores associated with this engine. For
   * SOLUTION_TYPE_SEARCH and SOLUTION_TYPE_RECOMMENDATION type of engines, they
   * can only associate with at most one data store. If solution_type is
   * SOLUTION_TYPE_CHAT, multiple DataStores in the same Collection can be
   * associated here. Note that when used in CreateEngineRequest, one DataStore
   * id must be provided as the system will use it for necessary
   * initializations.
   *
   * @param string[] $dataStoreIds
   */
  public function setDataStoreIds($dataStoreIds)
  {
    $this->dataStoreIds = $dataStoreIds;
  }
  /**
   * @return string[]
   */
  public function getDataStoreIds()
  {
    return $this->dataStoreIds;
  }
  /**
   * Optional. Whether to disable analytics for searches performed on this
   * engine.
   *
   * @param bool $disableAnalytics
   */
  public function setDisableAnalytics($disableAnalytics)
  {
    $this->disableAnalytics = $disableAnalytics;
  }
  /**
   * @return bool
   */
  public function getDisableAnalytics()
  {
    return $this->disableAnalytics;
  }
  /**
   * Required. The display name of the engine. Should be human readable. UTF-8
   * encoded string with limit of 1024 characters.
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
   * Optional. Feature config for the engine to opt in or opt out of features.
   * Supported keys: * `*`: all features, if it's present, all other feature
   * state settings are ignored. * `agent-gallery` * `no-code-agent-builder` *
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
   * Optional. The industry vertical that the engine registers. The restriction
   * of the Engine industry vertical is based on DataStore: Vertical on Engine
   * has to match vertical of the DataStore linked to the engine.
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
   * Configurations for the Media Engine. Only applicable on the data stores
   * with solution_type SOLUTION_TYPE_RECOMMENDATION and IndustryVertical.MEDIA
   * vertical.
   *
   * @param GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfig $mediaRecommendationEngineConfig
   */
  public function setMediaRecommendationEngineConfig(GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfig $mediaRecommendationEngineConfig)
  {
    $this->mediaRecommendationEngineConfig = $mediaRecommendationEngineConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfig
   */
  public function getMediaRecommendationEngineConfig()
  {
    return $this->mediaRecommendationEngineConfig;
  }
  /**
   * Optional. Maps a model name to its specific configuration for this engine.
   * This allows admin users to turn on/off individual models. This only stores
   * models whose states are overridden by the admin. When the state is
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
   * Immutable. Identifier. The fully qualified resource name of the engine.
   * This field must be a UTF-8 encoded string with a length limit of 1024
   * characters. Format: `projects/{project}/locations/{location}/collections/{c
   * ollection}/engines/{engine}` engine should be 1-63 characters, and valid
   * characters are /a-z0-9. Otherwise, an INVALID_ARGUMENT error is returned.
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
   * Configurations for the Search Engine. Only applicable if solution_type is
   * SOLUTION_TYPE_SEARCH.
   *
   * @param GoogleCloudDiscoveryengineV1betaEngineSearchEngineConfig $searchEngineConfig
   */
  public function setSearchEngineConfig(GoogleCloudDiscoveryengineV1betaEngineSearchEngineConfig $searchEngineConfig)
  {
    $this->searchEngineConfig = $searchEngineConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaEngineSearchEngineConfig
   */
  public function getSearchEngineConfig()
  {
    return $this->searchEngineConfig;
  }
  /**
   * Required. The solutions of the engine.
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
   * Output only. Timestamp the Recommendation Engine was last updated.
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
class_alias(GoogleCloudDiscoveryengineV1betaEngine::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaEngine');
