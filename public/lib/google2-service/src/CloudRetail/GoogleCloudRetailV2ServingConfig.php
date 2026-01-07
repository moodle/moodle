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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2ServingConfig extends \Google\Collection
{
  /**
   * Default value.
   */
  public const DIVERSITY_TYPE_DIVERSITY_TYPE_UNSPECIFIED = 'DIVERSITY_TYPE_UNSPECIFIED';
  /**
   * Rule based diversity.
   */
  public const DIVERSITY_TYPE_RULE_BASED_DIVERSITY = 'RULE_BASED_DIVERSITY';
  /**
   * Data driven diversity.
   */
  public const DIVERSITY_TYPE_DATA_DRIVEN_DIVERSITY = 'DATA_DRIVEN_DIVERSITY';
  protected $collection_key = 'twowaySynonymsControlIds';
  /**
   * Condition boost specifications. If a product matches multiple conditions in
   * the specifications, boost scores from these specifications are all applied
   * and combined in a non-linear way. Maximum number of specifications is 100.
   * Notice that if both ServingConfig.boost_control_ids and
   * SearchRequest.boost_spec are set, the boost conditions from both places are
   * evaluated. If a search request matches multiple boost conditions, the final
   * boost score is equal to the sum of the boost scores from all matched boost
   * conditions. Can only be set if solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $boostControlIds;
  /**
   * Required. The human readable serving config display name. Used in Retail
   * UI. This field must be a UTF-8 encoded string with a length limit of 128
   * characters. Otherwise, an INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $displayName;
  /**
   * How much diversity to use in recommendation model results e.g. `medium-
   * diversity` or `high-diversity`. Currently supported values: * `no-
   * diversity` * `low-diversity` * `medium-diversity` * `high-diversity` *
   * `auto-diversity` If not specified, we choose default based on
   * recommendation model type. Default value: `no-diversity`. Can only be set
   * if solution_types is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @var string
   */
  public $diversityLevel;
  /**
   * What kind of diversity to use - data driven or rule based. If unset, the
   * server behavior defaults to RULE_BASED_DIVERSITY.
   *
   * @var string
   */
  public $diversityType;
  /**
   * Condition do not associate specifications. If multiple do not associate
   * conditions match, all matching do not associate controls in the list will
   * execute. - Order does not matter. - Maximum number of specifications is
   * 100. Can only be set if solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $doNotAssociateControlIds;
  protected $dynamicFacetSpecType = GoogleCloudRetailV2SearchRequestDynamicFacetSpec::class;
  protected $dynamicFacetSpecDataType = '';
  /**
   * Whether to add additional category filters on the `similar-items` model. If
   * not specified, we enable it by default. Allowed values are: * `no-category-
   * match`: No additional filtering of original results from the model and the
   * customer's filters. * `relaxed-category-match`: Only keep results with
   * categories that match at least one item categories in the PredictRequests's
   * context item. * If customer also sends filters in the PredictRequest, then
   * the results will satisfy both conditions (user given and category match).
   * Can only be set if solution_types is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @var string
   */
  public $enableCategoryFilterLevel;
  /**
   * Facet specifications for faceted search. If empty, no facets are returned.
   * The ids refer to the ids of Control resources with only the Facet control
   * set. These controls are assumed to be in the same Catalog as the
   * ServingConfig. A maximum of 100 values are allowed. Otherwise, an
   * INVALID_ARGUMENT error is returned. Can only be set if solution_types is
   * SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $facetControlIds;
  /**
   * Condition filter specifications. If a product matches multiple conditions
   * in the specifications, filters from these specifications are all applied
   * and combined via the AND operator. Maximum number of specifications is 100.
   * Can only be set if solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $filterControlIds;
  /**
   * Condition ignore specifications. If multiple ignore conditions match, all
   * matching ignore controls in the list will execute. - Order does not matter.
   * - Maximum number of specifications is 100. Can only be set if
   * solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $ignoreControlIds;
  /**
   * When the flag is enabled, the products in the denylist will not be filtered
   * out in the recommendation filtering results.
   *
   * @var bool
   */
  public $ignoreRecsDenylist;
  /**
   * The id of the model in the same Catalog to use at serving time. Currently
   * only RecommendationModels are supported:
   * https://cloud.google.com/retail/recommendations-ai/docs/create-models Can
   * be changed but only to a compatible model (e.g. others-you-may-like CTR to
   * others-you-may-like CVR). Required when solution_types is
   * SOLUTION_TYPE_RECOMMENDATION.
   *
   * @var string
   */
  public $modelId;
  /**
   * Immutable. Fully qualified name
   * `projects/locations/global/catalogs/servingConfig`
   *
   * @var string
   */
  public $name;
  /**
   * Condition oneway synonyms specifications. If multiple oneway synonyms
   * conditions match, all matching oneway synonyms controls in the list will
   * execute. Order of controls in the list will not matter. Maximum number of
   * specifications is 100. Can only be set if solution_types is
   * SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $onewaySynonymsControlIds;
  protected $personalizationSpecType = GoogleCloudRetailV2SearchRequestPersonalizationSpec::class;
  protected $personalizationSpecDataType = '';
  /**
   * How much price ranking we want in serving results. Price reranking causes
   * product items with a similar recommendation probability to be ordered by
   * price, with the highest-priced items first. This setting could result in a
   * decrease in click-through and conversion rates. Allowed values are: * `no-
   * price-reranking` * `low-price-reranking` * `medium-price-reranking` *
   * `high-price-reranking` If not specified, we choose default based on model
   * type. Default value: `no-price-reranking`. Can only be set if
   * solution_types is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @var string
   */
  public $priceRerankingLevel;
  /**
   * Condition redirect specifications. Only the first triggered redirect action
   * is applied, even if multiple apply. Maximum number of specifications is
   * 1000. Can only be set if solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $redirectControlIds;
  /**
   * Condition replacement specifications. - Applied according to the order in
   * the list. - A previously replaced term can not be re-replaced. - Maximum
   * number of specifications is 100. Can only be set if solution_types is
   * SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $replacementControlIds;
  /**
   * Required. Immutable. Specifies the solution types that a serving config can
   * be associated with. Currently we support setting only one type of solution.
   *
   * @var string[]
   */
  public $solutionTypes;
  /**
   * Condition synonyms specifications. If multiple syonyms conditions match,
   * all matching synonyms control in the list will execute. Order of controls
   * in the list will not matter. Maximum number of specifications is 100. Can
   * only be set if solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @var string[]
   */
  public $twowaySynonymsControlIds;

  /**
   * Condition boost specifications. If a product matches multiple conditions in
   * the specifications, boost scores from these specifications are all applied
   * and combined in a non-linear way. Maximum number of specifications is 100.
   * Notice that if both ServingConfig.boost_control_ids and
   * SearchRequest.boost_spec are set, the boost conditions from both places are
   * evaluated. If a search request matches multiple boost conditions, the final
   * boost score is equal to the sum of the boost scores from all matched boost
   * conditions. Can only be set if solution_types is SOLUTION_TYPE_SEARCH.
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
   * Required. The human readable serving config display name. Used in Retail
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
   * How much diversity to use in recommendation model results e.g. `medium-
   * diversity` or `high-diversity`. Currently supported values: * `no-
   * diversity` * `low-diversity` * `medium-diversity` * `high-diversity` *
   * `auto-diversity` If not specified, we choose default based on
   * recommendation model type. Default value: `no-diversity`. Can only be set
   * if solution_types is SOLUTION_TYPE_RECOMMENDATION.
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
   * What kind of diversity to use - data driven or rule based. If unset, the
   * server behavior defaults to RULE_BASED_DIVERSITY.
   *
   * Accepted values: DIVERSITY_TYPE_UNSPECIFIED, RULE_BASED_DIVERSITY,
   * DATA_DRIVEN_DIVERSITY
   *
   * @param self::DIVERSITY_TYPE_* $diversityType
   */
  public function setDiversityType($diversityType)
  {
    $this->diversityType = $diversityType;
  }
  /**
   * @return self::DIVERSITY_TYPE_*
   */
  public function getDiversityType()
  {
    return $this->diversityType;
  }
  /**
   * Condition do not associate specifications. If multiple do not associate
   * conditions match, all matching do not associate controls in the list will
   * execute. - Order does not matter. - Maximum number of specifications is
   * 100. Can only be set if solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $doNotAssociateControlIds
   */
  public function setDoNotAssociateControlIds($doNotAssociateControlIds)
  {
    $this->doNotAssociateControlIds = $doNotAssociateControlIds;
  }
  /**
   * @return string[]
   */
  public function getDoNotAssociateControlIds()
  {
    return $this->doNotAssociateControlIds;
  }
  /**
   * The specification for dynamically generated facets. Notice that only
   * textual facets can be dynamically generated. Can only be set if
   * solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @param GoogleCloudRetailV2SearchRequestDynamicFacetSpec $dynamicFacetSpec
   */
  public function setDynamicFacetSpec(GoogleCloudRetailV2SearchRequestDynamicFacetSpec $dynamicFacetSpec)
  {
    $this->dynamicFacetSpec = $dynamicFacetSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestDynamicFacetSpec
   */
  public function getDynamicFacetSpec()
  {
    return $this->dynamicFacetSpec;
  }
  /**
   * Whether to add additional category filters on the `similar-items` model. If
   * not specified, we enable it by default. Allowed values are: * `no-category-
   * match`: No additional filtering of original results from the model and the
   * customer's filters. * `relaxed-category-match`: Only keep results with
   * categories that match at least one item categories in the PredictRequests's
   * context item. * If customer also sends filters in the PredictRequest, then
   * the results will satisfy both conditions (user given and category match).
   * Can only be set if solution_types is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @param string $enableCategoryFilterLevel
   */
  public function setEnableCategoryFilterLevel($enableCategoryFilterLevel)
  {
    $this->enableCategoryFilterLevel = $enableCategoryFilterLevel;
  }
  /**
   * @return string
   */
  public function getEnableCategoryFilterLevel()
  {
    return $this->enableCategoryFilterLevel;
  }
  /**
   * Facet specifications for faceted search. If empty, no facets are returned.
   * The ids refer to the ids of Control resources with only the Facet control
   * set. These controls are assumed to be in the same Catalog as the
   * ServingConfig. A maximum of 100 values are allowed. Otherwise, an
   * INVALID_ARGUMENT error is returned. Can only be set if solution_types is
   * SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $facetControlIds
   */
  public function setFacetControlIds($facetControlIds)
  {
    $this->facetControlIds = $facetControlIds;
  }
  /**
   * @return string[]
   */
  public function getFacetControlIds()
  {
    return $this->facetControlIds;
  }
  /**
   * Condition filter specifications. If a product matches multiple conditions
   * in the specifications, filters from these specifications are all applied
   * and combined via the AND operator. Maximum number of specifications is 100.
   * Can only be set if solution_types is SOLUTION_TYPE_SEARCH.
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
   * Condition ignore specifications. If multiple ignore conditions match, all
   * matching ignore controls in the list will execute. - Order does not matter.
   * - Maximum number of specifications is 100. Can only be set if
   * solution_types is SOLUTION_TYPE_SEARCH.
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
   * When the flag is enabled, the products in the denylist will not be filtered
   * out in the recommendation filtering results.
   *
   * @param bool $ignoreRecsDenylist
   */
  public function setIgnoreRecsDenylist($ignoreRecsDenylist)
  {
    $this->ignoreRecsDenylist = $ignoreRecsDenylist;
  }
  /**
   * @return bool
   */
  public function getIgnoreRecsDenylist()
  {
    return $this->ignoreRecsDenylist;
  }
  /**
   * The id of the model in the same Catalog to use at serving time. Currently
   * only RecommendationModels are supported:
   * https://cloud.google.com/retail/recommendations-ai/docs/create-models Can
   * be changed but only to a compatible model (e.g. others-you-may-like CTR to
   * others-you-may-like CVR). Required when solution_types is
   * SOLUTION_TYPE_RECOMMENDATION.
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
   * Immutable. Fully qualified name
   * `projects/locations/global/catalogs/servingConfig`
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
   * execute. Order of controls in the list will not matter. Maximum number of
   * specifications is 100. Can only be set if solution_types is
   * SOLUTION_TYPE_SEARCH.
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
   * The specification for personalization spec. Can only be set if
   * solution_types is SOLUTION_TYPE_SEARCH. Notice that if both
   * ServingConfig.personalization_spec and SearchRequest.personalization_spec
   * are set. SearchRequest.personalization_spec will override
   * ServingConfig.personalization_spec.
   *
   * @param GoogleCloudRetailV2SearchRequestPersonalizationSpec $personalizationSpec
   */
  public function setPersonalizationSpec(GoogleCloudRetailV2SearchRequestPersonalizationSpec $personalizationSpec)
  {
    $this->personalizationSpec = $personalizationSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestPersonalizationSpec
   */
  public function getPersonalizationSpec()
  {
    return $this->personalizationSpec;
  }
  /**
   * How much price ranking we want in serving results. Price reranking causes
   * product items with a similar recommendation probability to be ordered by
   * price, with the highest-priced items first. This setting could result in a
   * decrease in click-through and conversion rates. Allowed values are: * `no-
   * price-reranking` * `low-price-reranking` * `medium-price-reranking` *
   * `high-price-reranking` If not specified, we choose default based on model
   * type. Default value: `no-price-reranking`. Can only be set if
   * solution_types is SOLUTION_TYPE_RECOMMENDATION.
   *
   * @param string $priceRerankingLevel
   */
  public function setPriceRerankingLevel($priceRerankingLevel)
  {
    $this->priceRerankingLevel = $priceRerankingLevel;
  }
  /**
   * @return string
   */
  public function getPriceRerankingLevel()
  {
    return $this->priceRerankingLevel;
  }
  /**
   * Condition redirect specifications. Only the first triggered redirect action
   * is applied, even if multiple apply. Maximum number of specifications is
   * 1000. Can only be set if solution_types is SOLUTION_TYPE_SEARCH.
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
   * Condition replacement specifications. - Applied according to the order in
   * the list. - A previously replaced term can not be re-replaced. - Maximum
   * number of specifications is 100. Can only be set if solution_types is
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
   * Required. Immutable. Specifies the solution types that a serving config can
   * be associated with. Currently we support setting only one type of solution.
   *
   * @param string[] $solutionTypes
   */
  public function setSolutionTypes($solutionTypes)
  {
    $this->solutionTypes = $solutionTypes;
  }
  /**
   * @return string[]
   */
  public function getSolutionTypes()
  {
    return $this->solutionTypes;
  }
  /**
   * Condition synonyms specifications. If multiple syonyms conditions match,
   * all matching synonyms control in the list will execute. Order of controls
   * in the list will not matter. Maximum number of specifications is 100. Can
   * only be set if solution_types is SOLUTION_TYPE_SEARCH.
   *
   * @param string[] $twowaySynonymsControlIds
   */
  public function setTwowaySynonymsControlIds($twowaySynonymsControlIds)
  {
    $this->twowaySynonymsControlIds = $twowaySynonymsControlIds;
  }
  /**
   * @return string[]
   */
  public function getTwowaySynonymsControlIds()
  {
    return $this->twowaySynonymsControlIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ServingConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ServingConfig');
