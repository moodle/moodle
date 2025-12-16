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

class GoogleCloudDiscoveryengineV1ServingConfig extends \Google\Collection
{
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
  protected $collection_key = 'synonymsControlIds';
  protected $answerGenerationSpecType = GoogleCloudDiscoveryengineV1AnswerGenerationSpec::class;
  protected $answerGenerationSpecDataType = '';
  /**
   * Boost controls to use in serving path. All triggered boost controls will be
   * applied. Boost controls must be in the same data store as the serving
   * config. Maximum of 20 boost controls.
   *
   * @var string[]
   */
  public $boostControlIds;
  /**
   * Output only. ServingConfig created timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The human readable serving config display name. Used in Discovery
   * UI. This field must be a UTF-8 encoded string with a length limit of 128
   * characters. Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $displayName;
  /**
   * Condition do not associate specifications. If multiple do not associate
   * conditions match, all matching do not associate controls in the list will
   * execute. Order does not matter. Maximum number of specifications is 100.
   * Can only be set if SolutionType is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $dissociateControlIds;
  /**
   * How much diversity to use in recommendation model results e.g. `medium-
   * diversity` or `high-diversity`. Currently supported values: * `no-
   * diversity` * `low-diversity` * `medium-diversity` * `high-diversity` *
   * `auto-diversity` If not specified, we choose default based on
   * recommendation model type. Default value: `no-diversity`. Can only be set
   * if SolutionType is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @var string
   */
  public $diversityLevel;
  /**
   * Filter controls to use in serving path. All triggered filter controls will
   * be applied. Filter controls must be in the same data store as the serving
   * config. Maximum of 20 filter controls.
   *
   * @var string[]
   */
  public $filterControlIds;
  protected $genericConfigType = GoogleCloudDiscoveryengineV1ServingConfigGenericConfig::class;
  protected $genericConfigDataType = '';
  /**
   * Condition ignore specifications. If multiple ignore conditions match, all
   * matching ignore controls in the list will execute. Order does not matter.
   * Maximum number of specifications is 100.
   *
   * @var string[]
   */
  public $ignoreControlIds;
  protected $mediaConfigType = GoogleCloudDiscoveryengineV1ServingConfigMediaConfig::class;
  protected $mediaConfigDataType = '';
  /**
   * The id of the model to use at serving time. Currently only
   * RecommendationModels are supported. Can be changed but only to a compatible
   * model (e.g. others-you-may-like CTR to others-you-may-like CVR). Required
   * when SolutionType is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @var string
   */
  public $modelId;
  /**
   * Immutable. Fully qualified name `projects/{project}/locations/{location}/co
   * llections/{collection_id}/engines/{engine_id}/servingConfigs/{serving_confi
   * g_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Condition oneway synonyms specifications. If multiple oneway synonyms
   * conditions match, all matching oneway synonyms controls in the list will
   * execute. Maximum number of specifications is 100. Can only be set if
   * SolutionType is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $onewaySynonymsControlIds;
  /**
   * Condition promote specifications. Maximum number of specifications is 100.
   *
   * @var string[]
   */
  public $promoteControlIds;
  /**
   * The ranking expression controls the customized ranking on retrieval
   * documents. To leverage this, document embedding is required. The ranking
   * expression setting in ServingConfig applies to all search requests served
   * by the serving config. However, if `SearchRequest.ranking_expression` is
   * specified, it overrides the ServingConfig ranking expression. The ranking
   * expression is a single function or multiple functions that are joined by
   * "+". * ranking_expression = function, { " + ", function }; Supported
   * functions: * double * relevance_score * double *
   * dotProduct(embedding_field_path) Function variables: * `relevance_score`:
   * pre-defined keywords, used for measure relevance between query and
   * document. * `embedding_field_path`: the document embedding field used with
   * query embedding vector. * `dotProduct`: embedding function between
   * embedding_field_path and query embedding vector. Example ranking
   * expression: If document has an embedding field doc_embedding, the ranking
   * expression could be `0.5 * relevance_score + 0.3 *
   * dotProduct(doc_embedding)`.
   *
   * @var string
   */
  public $rankingExpression;
  /**
   * IDs of the redirect controls. Only the first triggered redirect action is
   * applied, even if multiple apply. Maximum number of specifications is 100.
   * Can only be set if SolutionType is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $redirectControlIds;
  /**
   * Condition replacement specifications. Applied according to the order in the
   * list. A previously replaced term can not be re-replaced. Maximum number of
   * specifications is 100. Can only be set if SolutionType is
   * SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $replacementControlIds;
  /**
   * Required. Immutable. Specifies the solution type that a serving config can
   * be associated with.
   *
   * @var string
   */
  public $solutionType;
  /**
   * Condition synonyms specifications. If multiple synonyms conditions match,
   * all matching synonyms controls in the list will execute. Maximum number of
   * specifications is 100. Can only be set if SolutionType is
   * SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $synonymsControlIds;
  /**
   * Output only. ServingConfig updated timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The specification for answer generation.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerGenerationSpec $answerGenerationSpec
   */
  public function setAnswerGenerationSpec(GoogleCloudDiscoveryengineV1AnswerGenerationSpec $answerGenerationSpec)
  {
    $this->answerGenerationSpec = $answerGenerationSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerGenerationSpec
   */
  public function getAnswerGenerationSpec()
  {
    return $this->answerGenerationSpec;
  }
  /**
   * Boost controls to use in serving path. All triggered boost controls will be
   * applied. Boost controls must be in the same data store as the serving
   * config. Maximum of 20 boost controls.
   *
   * @param string[] $boostControlIds
   */
  public function setBoostControlIds($boostControlIds)
  {
    $this->boostControlIds = $boostControlIds;
  }
  /**
   * @return string[]
   */
  public function getBoostControlIds()
  {
    return $this->boostControlIds;
  }
  /**
   * Output only. ServingConfig created timestamp.
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
   * Required. The human readable serving config display name. Used in Discovery
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
   * Condition do not associate specifications. If multiple do not associate
   * conditions match, all matching do not associate controls in the list will
   * execute. Order does not matter. Maximum number of specifications is 100.
   * Can only be set if SolutionType is SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $dissociateControlIds
   */
  public function setDissociateControlIds($dissociateControlIds)
  {
    $this->dissociateControlIds = $dissociateControlIds;
  }
  /**
   * @return string[]
   */
  public function getDissociateControlIds()
  {
    return $this->dissociateControlIds;
  }
  /**
   * How much diversity to use in recommendation model results e.g. `medium-
   * diversity` or `high-diversity`. Currently supported values: * `no-
   * diversity` * `low-diversity` * `medium-diversity` * `high-diversity` *
   * `auto-diversity` If not specified, we choose default based on
   * recommendation model type. Default value: `no-diversity`. Can only be set
   * if SolutionType is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @param string $diversityLevel
   */
  public function setDiversityLevel($diversityLevel)
  {
    $this->diversityLevel = $diversityLevel;
  }
  /**
   * @return string
   */
  public function getDiversityLevel()
  {
    return $this->diversityLevel;
  }
  /**
   * Filter controls to use in serving path. All triggered filter controls will
   * be applied. Filter controls must be in the same data store as the serving
   * config. Maximum of 20 filter controls.
   *
   * @param string[] $filterControlIds
   */
  public function setFilterControlIds($filterControlIds)
  {
    $this->filterControlIds = $filterControlIds;
  }
  /**
   * @return string[]
   */
  public function getFilterControlIds()
  {
    return $this->filterControlIds;
  }
  /**
   * The GenericConfig of the serving configuration.
   *
   * @param GoogleCloudDiscoveryengineV1ServingConfigGenericConfig $genericConfig
   */
  public function setGenericConfig(GoogleCloudDiscoveryengineV1ServingConfigGenericConfig $genericConfig)
  {
    $this->genericConfig = $genericConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ServingConfigGenericConfig
   */
  public function getGenericConfig()
  {
    return $this->genericConfig;
  }
  /**
   * Condition ignore specifications. If multiple ignore conditions match, all
   * matching ignore controls in the list will execute. Order does not matter.
   * Maximum number of specifications is 100.
   *
   * @param string[] $ignoreControlIds
   */
  public function setIgnoreControlIds($ignoreControlIds)
  {
    $this->ignoreControlIds = $ignoreControlIds;
  }
  /**
   * @return string[]
   */
  public function getIgnoreControlIds()
  {
    return $this->ignoreControlIds;
  }
  /**
   * The MediaConfig of the serving configuration.
   *
   * @param GoogleCloudDiscoveryengineV1ServingConfigMediaConfig $mediaConfig
   */
  public function setMediaConfig(GoogleCloudDiscoveryengineV1ServingConfigMediaConfig $mediaConfig)
  {
    $this->mediaConfig = $mediaConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ServingConfigMediaConfig
   */
  public function getMediaConfig()
  {
    return $this->mediaConfig;
  }
  /**
   * The id of the model to use at serving time. Currently only
   * RecommendationModels are supported. Can be changed but only to a compatible
   * model (e.g. others-you-may-like CTR to others-you-may-like CVR). Required
   * when SolutionType is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @param string $modelId
   */
  public function setModelId($modelId)
  {
    $this->modelId = $modelId;
  }
  /**
   * @return string
   */
  public function getModelId()
  {
    return $this->modelId;
  }
  /**
   * Immutable. Fully qualified name `projects/{project}/locations/{location}/co
   * llections/{collection_id}/engines/{engine_id}/servingConfigs/{serving_confi
   * g_id}`
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
   * Condition oneway synonyms specifications. If multiple oneway synonyms
   * conditions match, all matching oneway synonyms controls in the list will
   * execute. Maximum number of specifications is 100. Can only be set if
   * SolutionType is SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $onewaySynonymsControlIds
   */
  public function setOnewaySynonymsControlIds($onewaySynonymsControlIds)
  {
    $this->onewaySynonymsControlIds = $onewaySynonymsControlIds;
  }
  /**
   * @return string[]
   */
  public function getOnewaySynonymsControlIds()
  {
    return $this->onewaySynonymsControlIds;
  }
  /**
   * Condition promote specifications. Maximum number of specifications is 100.
   *
   * @param string[] $promoteControlIds
   */
  public function setPromoteControlIds($promoteControlIds)
  {
    $this->promoteControlIds = $promoteControlIds;
  }
  /**
   * @return string[]
   */
  public function getPromoteControlIds()
  {
    return $this->promoteControlIds;
  }
  /**
   * The ranking expression controls the customized ranking on retrieval
   * documents. To leverage this, document embedding is required. The ranking
   * expression setting in ServingConfig applies to all search requests served
   * by the serving config. However, if `SearchRequest.ranking_expression` is
   * specified, it overrides the ServingConfig ranking expression. The ranking
   * expression is a single function or multiple functions that are joined by
   * "+". * ranking_expression = function, { " + ", function }; Supported
   * functions: * double * relevance_score * double *
   * dotProduct(embedding_field_path) Function variables: * `relevance_score`:
   * pre-defined keywords, used for measure relevance between query and
   * document. * `embedding_field_path`: the document embedding field used with
   * query embedding vector. * `dotProduct`: embedding function between
   * embedding_field_path and query embedding vector. Example ranking
   * expression: If document has an embedding field doc_embedding, the ranking
   * expression could be `0.5 * relevance_score + 0.3 *
   * dotProduct(doc_embedding)`.
   *
   * @param string $rankingExpression
   */
  public function setRankingExpression($rankingExpression)
  {
    $this->rankingExpression = $rankingExpression;
  }
  /**
   * @return string
   */
  public function getRankingExpression()
  {
    return $this->rankingExpression;
  }
  /**
   * IDs of the redirect controls. Only the first triggered redirect action is
   * applied, even if multiple apply. Maximum number of specifications is 100.
   * Can only be set if SolutionType is SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $redirectControlIds
   */
  public function setRedirectControlIds($redirectControlIds)
  {
    $this->redirectControlIds = $redirectControlIds;
  }
  /**
   * @return string[]
   */
  public function getRedirectControlIds()
  {
    return $this->redirectControlIds;
  }
  /**
   * Condition replacement specifications. Applied according to the order in the
   * list. A previously replaced term can not be re-replaced. Maximum number of
   * specifications is 100. Can only be set if SolutionType is
   * SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $replacementControlIds
   */
  public function setReplacementControlIds($replacementControlIds)
  {
    $this->replacementControlIds = $replacementControlIds;
  }
  /**
   * @return string[]
   */
  public function getReplacementControlIds()
  {
    return $this->replacementControlIds;
  }
  /**
   * Required. Immutable. Specifies the solution type that a serving config can
   * be associated with.
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
   * Condition synonyms specifications. If multiple synonyms conditions match,
   * all matching synonyms controls in the list will execute. Maximum number of
   * specifications is 100. Can only be set if SolutionType is
   * SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $synonymsControlIds
   */
  public function setSynonymsControlIds($synonymsControlIds)
  {
    $this->synonymsControlIds = $synonymsControlIds;
  }
  /**
   * @return string[]
   */
  public function getSynonymsControlIds()
  {
    return $this->synonymsControlIds;
  }
  /**
   * Output only. ServingConfig updated timestamp.
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
class_alias(GoogleCloudDiscoveryengineV1ServingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ServingConfig');
