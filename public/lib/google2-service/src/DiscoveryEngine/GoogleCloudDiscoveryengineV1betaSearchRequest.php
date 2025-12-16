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

class GoogleCloudDiscoveryengineV1betaSearchRequest extends \Google\Collection
{
  /**
   * Default option for unspecified/unknown values.
   */
  public const RANKING_EXPRESSION_BACKEND_RANKING_EXPRESSION_BACKEND_UNSPECIFIED = 'RANKING_EXPRESSION_BACKEND_UNSPECIFIED';
  /**
   * Deprecated: Use `RANK_BY_EMBEDDING` instead. Ranking by custom embedding
   * model, the default way to evaluate the ranking expression. Legacy enum
   * option, `RANK_BY_EMBEDDING` should be used instead.
   *
   * @deprecated
   */
  public const RANKING_EXPRESSION_BACKEND_BYOE = 'BYOE';
  /**
   * Deprecated: Use `RANK_BY_FORMULA` instead. Ranking by custom formula.
   * Legacy enum option, `RANK_BY_FORMULA` should be used instead.
   *
   * @deprecated
   */
  public const RANKING_EXPRESSION_BACKEND_CLEARBOX = 'CLEARBOX';
  /**
   * Ranking by custom embedding model, the default way to evaluate the ranking
   * expression.
   */
  public const RANKING_EXPRESSION_BACKEND_RANK_BY_EMBEDDING = 'RANK_BY_EMBEDDING';
  /**
   * Ranking by custom formula.
   */
  public const RANKING_EXPRESSION_BACKEND_RANK_BY_FORMULA = 'RANK_BY_FORMULA';
  /**
   * Default value. In this case, server behavior defaults to Google defined
   * threshold.
   */
  public const RELEVANCE_THRESHOLD_RELEVANCE_THRESHOLD_UNSPECIFIED = 'RELEVANCE_THRESHOLD_UNSPECIFIED';
  /**
   * Lowest relevance threshold.
   */
  public const RELEVANCE_THRESHOLD_LOWEST = 'LOWEST';
  /**
   * Low relevance threshold.
   */
  public const RELEVANCE_THRESHOLD_LOW = 'LOW';
  /**
   * Medium relevance threshold.
   */
  public const RELEVANCE_THRESHOLD_MEDIUM = 'MEDIUM';
  /**
   * High relevance threshold.
   */
  public const RELEVANCE_THRESHOLD_HIGH = 'HIGH';
  protected $collection_key = 'pageCategories';
  protected $boostSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  /**
   * The branch resource name, such as `projects/locations/global/collections/de
   * fault_collection/dataStores/default_data_store/branches/0`. Use
   * `default_branch` as the branch ID or leave this field empty, to search
   * documents under the default branch.
   *
   * @var string
   */
  public $branch;
  /**
   * The default filter that is applied when a user performs a search without
   * checking any filters on the search page. The filter applied to every search
   * request when quality improvement such as query expansion is needed. In the
   * case a query does not have a sufficient amount of results this filter will
   * be used to determine whether or not to enable the query expansion flow. The
   * original filter will still be used for the query expanded search. This
   * field is strongly recommended to achieve high search quality. For more
   * information about filter syntax, see SearchRequest.filter.
   *
   * @var string
   */
  public $canonicalFilter;
  protected $contentSearchSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpec::class;
  protected $contentSearchSpecDataType = '';
  protected $crowdingSpecsType = GoogleCloudDiscoveryengineV1betaSearchRequestCrowdingSpec::class;
  protected $crowdingSpecsDataType = 'array';
  protected $dataStoreSpecsType = GoogleCloudDiscoveryengineV1betaSearchRequestDataStoreSpec::class;
  protected $dataStoreSpecsDataType = 'array';
  protected $displaySpecType = GoogleCloudDiscoveryengineV1betaSearchRequestDisplaySpec::class;
  protected $displaySpecDataType = '';
  protected $embeddingSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestEmbeddingSpec::class;
  protected $embeddingSpecDataType = '';
  protected $facetSpecsType = GoogleCloudDiscoveryengineV1betaSearchRequestFacetSpec::class;
  protected $facetSpecsDataType = 'array';
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the documents being filtered. Filter
   * expression is case-sensitive. If this field is unrecognizable, an
   * `INVALID_ARGUMENT` is returned. Filtering in Vertex AI Search is done by
   * mapping the LHS filter key to a key property defined in the Vertex AI
   * Search backend -- this mapping is defined by the customer in their schema.
   * For example a media customer might have a field 'name' in their schema. In
   * this case the filter would look like this: filter --> name:'ANY("king
   * kong")' For more information about filtering including syntax and filter
   * operators, see [Filter](https://cloud.google.com/generative-ai-app-
   * builder/docs/filter-search-metadata)
   *
   * @var string
   */
  public $filter;
  protected $imageQueryType = GoogleCloudDiscoveryengineV1betaSearchRequestImageQuery::class;
  protected $imageQueryDataType = '';
  /**
   * The BCP-47 language code, such as "en-US" or "sr-Latn". For more
   * information, see [Standard
   * fields](https://cloud.google.com/apis/design/standard_fields). This field
   * helps to better interpret the query. If a value isn't specified, the query
   * language code is automatically detected, which may not be accurate.
   *
   * @var string
   */
  public $languageCode;
  protected $naturalLanguageQueryUnderstandingSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestNaturalLanguageQueryUnderstandingSpec::class;
  protected $naturalLanguageQueryUnderstandingSpecDataType = '';
  /**
   * A 0-indexed integer that specifies the current offset (that is, starting
   * result location, amongst the Documents deemed by the API as relevant) in
   * search results. This field is only considered if page_token is unset. If
   * this field is negative, an `INVALID_ARGUMENT` is returned. A large offset
   * may be capped to a reasonable threshold.
   *
   * @var int
   */
  public $offset;
  /**
   * The maximum number of results to return for OneBox. This applies to each
   * OneBox type individually. Default number is 10.
   *
   * @var int
   */
  public $oneBoxPageSize;
  /**
   * The order in which documents are returned. Documents can be ordered by a
   * field in an Document object. Leave it unset if ordered by relevance.
   * `order_by` expression is case-sensitive. For more information on ordering
   * the website search results, see [Order web search
   * results](https://cloud.google.com/generative-ai-app-builder/docs/order-web-
   * search-results). For more information on ordering the healthcare search
   * results, see [Order healthcare search
   * results](https://cloud.google.com/generative-ai-app-builder/docs/order-hc-
   * results). If this field is unrecognizable, an `INVALID_ARGUMENT` is
   * returned.
   *
   * @var string
   */
  public $orderBy;
  /**
   * Optional. The categories associated with a category page. Must be set for
   * category navigation queries to achieve good search quality. The format
   * should be the same as UserEvent.PageInfo.page_category. This field is the
   * equivalent of the query for browse (navigation) queries. It's used by the
   * browse model when the query is empty. If the field is empty, it will not be
   * used by the browse model. If the field contains more than one element, only
   * the first element will be used. To represent full path of a category, use
   * '>' character to separate different hierarchies. If '>' is part of the
   * category name, replace it with other character(s). For example, `Graphics
   * Cards > RTX>4090 > Founders Edition` where "RTX > 4090" represents one
   * level, can be rewritten as `Graphics Cards > RTX_4090 > Founders Edition`
   *
   * @var string[]
   */
  public $pageCategories;
  /**
   * Maximum number of Documents to return. The maximum allowed value depends on
   * the data type. Values above the maximum value are coerced to the maximum
   * value. * Websites with basic indexing: Default `10`, Maximum `25`. *
   * Websites with advanced indexing: Default `25`, Maximum `50`. * Other:
   * Default `50`, Maximum `100`. If this field is negative, an
   * `INVALID_ARGUMENT` is returned.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A page token received from a previous SearchService.Search call. Provide
   * this to retrieve the subsequent page. When paginating, all other parameters
   * provided to SearchService.Search must match the call that provided the page
   * token. Otherwise, an `INVALID_ARGUMENT` error is returned.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Additional search parameters. For public website search only, supported
   * values are: * `user_country_code`: string. Default empty. If set to non-
   * empty, results are restricted or boosted based on the location provided.
   * For example, `user_country_code: "au"` For available codes see [Country
   * Codes](https://developers.google.com/custom-
   * search/docs/json_api_reference#countryCodes) * `search_type`: double.
   * Default empty. Enables non-webpage searching depending on the value. The
   * only valid non-default value is 1, which enables image searching. For
   * example, `search_type: 1`
   *
   * @var array[]
   */
  public $params;
  protected $personalizationSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestPersonalizationSpec::class;
  protected $personalizationSpecDataType = '';
  /**
   * Raw search query.
   *
   * @var string
   */
  public $query;
  protected $queryExpansionSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestQueryExpansionSpec::class;
  protected $queryExpansionSpecDataType = '';
  /**
   * Optional. The ranking expression controls the customized ranking on
   * retrieval documents. This overrides ServingConfig.ranking_expression. The
   * syntax and supported features depend on the `ranking_expression_backend`
   * value. If `ranking_expression_backend` is not provided, it defaults to
   * `RANK_BY_EMBEDDING`. If ranking_expression_backend is not provided or set
   * to `RANK_BY_EMBEDDING`, it should be a single function or multiple
   * functions that are joined by "+". * ranking_expression = function, { " + ",
   * function }; Supported functions: * double * relevance_score * double *
   * dotProduct(embedding_field_path) Function variables: * `relevance_score`:
   * pre-defined keywords, used for measure relevance between query and
   * document. * `embedding_field_path`: the document embedding field used with
   * query embedding vector. * `dotProduct`: embedding function between
   * `embedding_field_path` and query embedding vector. Example ranking
   * expression: If document has an embedding field doc_embedding, the ranking
   * expression could be `0.5 * relevance_score + 0.3 *
   * dotProduct(doc_embedding)`. If ranking_expression_backend is set to
   * `RANK_BY_FORMULA`, the following expression types (and combinations of
   * those chained using + or * operators) are supported: * `double` * `signal`
   * * `log(signal)` * `exp(signal)` * `rr(signal, double > 0)` -- reciprocal
   * rank transformation with second argument being a denominator constant. *
   * `is_nan(signal)` -- returns 0 if signal is NaN, 1 otherwise. *
   * `fill_nan(signal1, signal2 | double)` -- if signal1 is NaN, returns signal2
   * | double, else returns signal1. Here are a few examples of ranking formulas
   * that use the supported ranking expression types: - `0.2 *
   * semantic_similarity_score + 0.8 * log(keyword_similarity_score)` -- mostly
   * rank by the logarithm of `keyword_similarity_score` with slight
   * `semantic_smilarity_score` adjustment. - `0.2 *
   * exp(fill_nan(semantic_similarity_score, 0)) + 0.3 *
   * is_nan(keyword_similarity_score)` -- rank by the exponent of
   * `semantic_similarity_score` filling the value with 0 if it's NaN, also add
   * constant 0.3 adjustment to the final score if `semantic_similarity_score`
   * is NaN. - `0.2 * rr(semantic_similarity_score, 16) + 0.8 *
   * rr(keyword_similarity_score, 16)` -- mostly rank by the reciprocal rank of
   * `keyword_similarity_score` with slight adjustment of reciprocal rank of
   * `semantic_smilarity_score`. The following signals are supported: *
   * `semantic_similarity_score`: semantic similarity adjustment that is
   * calculated using the embeddings generated by a proprietary Google model.
   * This score determines how semantically similar a search query is to a
   * document. * `keyword_similarity_score`: keyword match adjustment uses the
   * Best Match 25 (BM25) ranking function. This score is calculated using a
   * probabilistic model to estimate the probability that a document is relevant
   * to a given query. * `relevance_score`: semantic relevance adjustment that
   * uses a proprietary Google model to determine the meaning and intent behind
   * a user's query in context with the content in the documents. * `pctr_rank`:
   * predicted conversion rate adjustment as a rank use predicted Click-through
   * rate (pCTR) to gauge the relevance and attractiveness of a search result
   * from a user's perspective. A higher pCTR suggests that the result is more
   * likely to satisfy the user's query and intent, making it a valuable signal
   * for ranking. * `freshness_rank`: freshness adjustment as a rank *
   * `document_age`: The time in hours elapsed since the document was last
   * updated, a floating-point number (e.g., 0.25 means 15 minutes). *
   * `topicality_rank`: topicality adjustment as a rank. Uses proprietary Google
   * model to determine the keyword-based overlap between the query and the
   * document. * `base_rank`: the default rank of the result
   *
   * @var string
   */
  public $rankingExpression;
  /**
   * Optional. The backend to use for the ranking expression evaluation.
   *
   * @var string
   */
  public $rankingExpressionBackend;
  /**
   * The Unicode country/region code (CLDR) of a location, such as "US" and
   * "419". For more information, see [Standard
   * fields](https://cloud.google.com/apis/design/standard_fields). If set, then
   * results will be boosted based on the region_code provided.
   *
   * @var string
   */
  public $regionCode;
  protected $relevanceScoreSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestRelevanceScoreSpec::class;
  protected $relevanceScoreSpecDataType = '';
  /**
   * The relevance threshold of the search results. Default to Google defined
   * threshold, leveraging a balance of precision and recall to deliver both
   * highly accurate results and comprehensive coverage of relevant information.
   * This feature is not supported for healthcare search.
   *
   * @var string
   */
  public $relevanceThreshold;
  /**
   * Whether to turn on safe search. This is only supported for website search.
   *
   * @var bool
   */
  public $safeSearch;
  protected $searchAddonSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestSearchAddonSpec::class;
  protected $searchAddonSpecDataType = '';
  protected $searchAsYouTypeSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestSearchAsYouTypeSpec::class;
  protected $searchAsYouTypeSpecDataType = '';
  /**
   * Required. The resource name of the Search serving config, such as `projects
   * /locations/global/collections/default_collection/engines/servingConfigs/def
   * ault_serving_config`, or `projects/locations/global/collections/default_col
   * lection/dataStores/default_data_store/servingConfigs/default_serving_config
   * `. This field is used to identify the serving configuration name, set of
   * models used to make the search.
   *
   * @var string
   */
  public $servingConfig;
  /**
   * The session resource name. Optional. Session allows users to do multi-turn
   * /search API calls or coordination between /search API calls and /answer API
   * calls. Example #1 (multi-turn /search API calls): Call /search API with the
   * session ID generated in the first call. Here, the previous search query
   * gets considered in query standing. I.e., if the first query is "How did
   * Alphabet do in 2022?" and the current query is "How about 2023?", the
   * current query will be interpreted as "How did Alphabet do in 2023?".
   * Example #2 (coordination between /search API calls and /answer API calls):
   * Call /answer API with the session ID generated in the first call. Here, the
   * answer generation happens in the context of the search results from the
   * first search call. Multi-turn Search feature is currently at private GA
   * stage. Please use v1alpha or v1beta version instead before we launch this
   * feature to public GA. Or ask for allowlisting through Google Support team.
   *
   * @var string
   */
  public $session;
  protected $sessionSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestSessionSpec::class;
  protected $sessionSpecDataType = '';
  protected $spellCorrectionSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestSpellCorrectionSpec::class;
  protected $spellCorrectionSpecDataType = '';
  protected $userInfoType = GoogleCloudDiscoveryengineV1betaUserInfo::class;
  protected $userInfoDataType = '';
  /**
   * The user labels applied to a resource must meet the following requirements:
   * * Each resource can have multiple labels, up to a maximum of 64. * Each
   * label must be a key-value pair. * Keys have a minimum length of 1 character
   * and a maximum length of 63 characters and cannot be empty. Values can be
   * empty and have a maximum length of 63 characters. * Keys and values can
   * contain only lowercase letters, numeric characters, underscores, and
   * dashes. All characters must use UTF-8 encoding, and international
   * characters are allowed. * The key portion of a label must be unique.
   * However, you can use the same key with multiple resources. * Keys must
   * start with a lowercase letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @var string[]
   */
  public $userLabels;
  /**
   * Optional. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor logs in or out of the website. This field should NOT
   * have a fixed value such as `unknown_visitor`. This should be the same
   * identifier as UserEvent.user_pseudo_id and
   * CompleteQueryRequest.user_pseudo_id The field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @var string
   */
  public $userPseudoId;

  /**
   * Boost specification to boost certain documents. For more information on
   * boosting, see [Boosting](https://cloud.google.com/generative-ai-app-
   * builder/docs/boost-search-results)
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec $boostSpec
   */
  public function setBoostSpec(GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * The branch resource name, such as `projects/locations/global/collections/de
   * fault_collection/dataStores/default_data_store/branches/0`. Use
   * `default_branch` as the branch ID or leave this field empty, to search
   * documents under the default branch.
   *
   * @param string $branch
   */
  public function setBranch($branch)
  {
    $this->branch = $branch;
  }
  /**
   * @return string
   */
  public function getBranch()
  {
    return $this->branch;
  }
  /**
   * The default filter that is applied when a user performs a search without
   * checking any filters on the search page. The filter applied to every search
   * request when quality improvement such as query expansion is needed. In the
   * case a query does not have a sufficient amount of results this filter will
   * be used to determine whether or not to enable the query expansion flow. The
   * original filter will still be used for the query expanded search. This
   * field is strongly recommended to achieve high search quality. For more
   * information about filter syntax, see SearchRequest.filter.
   *
   * @param string $canonicalFilter
   */
  public function setCanonicalFilter($canonicalFilter)
  {
    $this->canonicalFilter = $canonicalFilter;
  }
  /**
   * @return string
   */
  public function getCanonicalFilter()
  {
    return $this->canonicalFilter;
  }
  /**
   * A specification for configuring the behavior of content search.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpec $contentSearchSpec
   */
  public function setContentSearchSpec(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpec $contentSearchSpec)
  {
    $this->contentSearchSpec = $contentSearchSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpec
   */
  public function getContentSearchSpec()
  {
    return $this->contentSearchSpec;
  }
  /**
   * Optional. Crowding specifications for improving result diversity. If
   * multiple CrowdingSpecs are specified, crowding will be evaluated on each
   * unique combination of the `field` values, and max_count will be the maximum
   * value of `max_count` across all CrowdingSpecs. For example, if the first
   * CrowdingSpec has `field` = "color" and `max_count` = 3, and the second
   * CrowdingSpec has `field` = "size" and `max_count` = 2, then after 3
   * documents that share the same color AND size have been returned, subsequent
   * ones should be removed or demoted.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestCrowdingSpec[] $crowdingSpecs
   */
  public function setCrowdingSpecs($crowdingSpecs)
  {
    $this->crowdingSpecs = $crowdingSpecs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestCrowdingSpec[]
   */
  public function getCrowdingSpecs()
  {
    return $this->crowdingSpecs;
  }
  /**
   * Specifications that define the specific DataStores to be searched, along
   * with configurations for those data stores. This is only considered for
   * Engines with multiple data stores. For engines with a single data store,
   * the specs directly under SearchRequest should be used.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestDataStoreSpec[] $dataStoreSpecs
   */
  public function setDataStoreSpecs($dataStoreSpecs)
  {
    $this->dataStoreSpecs = $dataStoreSpecs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestDataStoreSpec[]
   */
  public function getDataStoreSpecs()
  {
    return $this->dataStoreSpecs;
  }
  /**
   * Optional. Config for display feature, like match highlighting on search
   * results.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestDisplaySpec $displaySpec
   */
  public function setDisplaySpec(GoogleCloudDiscoveryengineV1betaSearchRequestDisplaySpec $displaySpec)
  {
    $this->displaySpec = $displaySpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestDisplaySpec
   */
  public function getDisplaySpec()
  {
    return $this->displaySpec;
  }
  /**
   * Uses the provided embedding to do additional semantic document retrieval.
   * The retrieval is based on the dot product of
   * SearchRequest.EmbeddingSpec.EmbeddingVector.vector and the document
   * embedding that is provided in
   * SearchRequest.EmbeddingSpec.EmbeddingVector.field_path. If
   * SearchRequest.EmbeddingSpec.EmbeddingVector.field_path is not provided, it
   * will use ServingConfig.EmbeddingConfig.field_path.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestEmbeddingSpec $embeddingSpec
   */
  public function setEmbeddingSpec(GoogleCloudDiscoveryengineV1betaSearchRequestEmbeddingSpec $embeddingSpec)
  {
    $this->embeddingSpec = $embeddingSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestEmbeddingSpec
   */
  public function getEmbeddingSpec()
  {
    return $this->embeddingSpec;
  }
  /**
   * Facet specifications for faceted search. If empty, no facets are returned.
   * A maximum of 100 values are allowed. Otherwise, an `INVALID_ARGUMENT` error
   * is returned.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestFacetSpec[] $facetSpecs
   */
  public function setFacetSpecs($facetSpecs)
  {
    $this->facetSpecs = $facetSpecs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestFacetSpec[]
   */
  public function getFacetSpecs()
  {
    return $this->facetSpecs;
  }
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the documents being filtered. Filter
   * expression is case-sensitive. If this field is unrecognizable, an
   * `INVALID_ARGUMENT` is returned. Filtering in Vertex AI Search is done by
   * mapping the LHS filter key to a key property defined in the Vertex AI
   * Search backend -- this mapping is defined by the customer in their schema.
   * For example a media customer might have a field 'name' in their schema. In
   * this case the filter would look like this: filter --> name:'ANY("king
   * kong")' For more information about filtering including syntax and filter
   * operators, see [Filter](https://cloud.google.com/generative-ai-app-
   * builder/docs/filter-search-metadata)
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Raw image query.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestImageQuery $imageQuery
   */
  public function setImageQuery(GoogleCloudDiscoveryengineV1betaSearchRequestImageQuery $imageQuery)
  {
    $this->imageQuery = $imageQuery;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestImageQuery
   */
  public function getImageQuery()
  {
    return $this->imageQuery;
  }
  /**
   * The BCP-47 language code, such as "en-US" or "sr-Latn". For more
   * information, see [Standard
   * fields](https://cloud.google.com/apis/design/standard_fields). This field
   * helps to better interpret the query. If a value isn't specified, the query
   * language code is automatically detected, which may not be accurate.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Optional. Config for natural language query understanding capabilities,
   * such as extracting structured field filters from the query. Refer to [this
   * documentation](https://cloud.google.com/generative-ai-app-
   * builder/docs/natural-language-queries) for more information. If
   * `naturalLanguageQueryUnderstandingSpec` is not specified, no additional
   * natural language query understanding will be done.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestNaturalLanguageQueryUnderstandingSpec $naturalLanguageQueryUnderstandingSpec
   */
  public function setNaturalLanguageQueryUnderstandingSpec(GoogleCloudDiscoveryengineV1betaSearchRequestNaturalLanguageQueryUnderstandingSpec $naturalLanguageQueryUnderstandingSpec)
  {
    $this->naturalLanguageQueryUnderstandingSpec = $naturalLanguageQueryUnderstandingSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestNaturalLanguageQueryUnderstandingSpec
   */
  public function getNaturalLanguageQueryUnderstandingSpec()
  {
    return $this->naturalLanguageQueryUnderstandingSpec;
  }
  /**
   * A 0-indexed integer that specifies the current offset (that is, starting
   * result location, amongst the Documents deemed by the API as relevant) in
   * search results. This field is only considered if page_token is unset. If
   * this field is negative, an `INVALID_ARGUMENT` is returned. A large offset
   * may be capped to a reasonable threshold.
   *
   * @param int $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * The maximum number of results to return for OneBox. This applies to each
   * OneBox type individually. Default number is 10.
   *
   * @param int $oneBoxPageSize
   */
  public function setOneBoxPageSize($oneBoxPageSize)
  {
    $this->oneBoxPageSize = $oneBoxPageSize;
  }
  /**
   * @return int
   */
  public function getOneBoxPageSize()
  {
    return $this->oneBoxPageSize;
  }
  /**
   * The order in which documents are returned. Documents can be ordered by a
   * field in an Document object. Leave it unset if ordered by relevance.
   * `order_by` expression is case-sensitive. For more information on ordering
   * the website search results, see [Order web search
   * results](https://cloud.google.com/generative-ai-app-builder/docs/order-web-
   * search-results). For more information on ordering the healthcare search
   * results, see [Order healthcare search
   * results](https://cloud.google.com/generative-ai-app-builder/docs/order-hc-
   * results). If this field is unrecognizable, an `INVALID_ARGUMENT` is
   * returned.
   *
   * @param string $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * Optional. The categories associated with a category page. Must be set for
   * category navigation queries to achieve good search quality. The format
   * should be the same as UserEvent.PageInfo.page_category. This field is the
   * equivalent of the query for browse (navigation) queries. It's used by the
   * browse model when the query is empty. If the field is empty, it will not be
   * used by the browse model. If the field contains more than one element, only
   * the first element will be used. To represent full path of a category, use
   * '>' character to separate different hierarchies. If '>' is part of the
   * category name, replace it with other character(s). For example, `Graphics
   * Cards > RTX>4090 > Founders Edition` where "RTX > 4090" represents one
   * level, can be rewritten as `Graphics Cards > RTX_4090 > Founders Edition`
   *
   * @param string[] $pageCategories
   */
  public function setPageCategories($pageCategories)
  {
    $this->pageCategories = $pageCategories;
  }
  /**
   * @return string[]
   */
  public function getPageCategories()
  {
    return $this->pageCategories;
  }
  /**
   * Maximum number of Documents to return. The maximum allowed value depends on
   * the data type. Values above the maximum value are coerced to the maximum
   * value. * Websites with basic indexing: Default `10`, Maximum `25`. *
   * Websites with advanced indexing: Default `25`, Maximum `50`. * Other:
   * Default `50`, Maximum `100`. If this field is negative, an
   * `INVALID_ARGUMENT` is returned.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * A page token received from a previous SearchService.Search call. Provide
   * this to retrieve the subsequent page. When paginating, all other parameters
   * provided to SearchService.Search must match the call that provided the page
   * token. Otherwise, an `INVALID_ARGUMENT` error is returned.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Additional search parameters. For public website search only, supported
   * values are: * `user_country_code`: string. Default empty. If set to non-
   * empty, results are restricted or boosted based on the location provided.
   * For example, `user_country_code: "au"` For available codes see [Country
   * Codes](https://developers.google.com/custom-
   * search/docs/json_api_reference#countryCodes) * `search_type`: double.
   * Default empty. Enables non-webpage searching depending on the value. The
   * only valid non-default value is 1, which enables image searching. For
   * example, `search_type: 1`
   *
   * @param array[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * The specification for personalization. Notice that if both
   * ServingConfig.personalization_spec and SearchRequest.personalization_spec
   * are set, SearchRequest.personalization_spec overrides
   * ServingConfig.personalization_spec.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestPersonalizationSpec $personalizationSpec
   */
  public function setPersonalizationSpec(GoogleCloudDiscoveryengineV1betaSearchRequestPersonalizationSpec $personalizationSpec)
  {
    $this->personalizationSpec = $personalizationSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestPersonalizationSpec
   */
  public function getPersonalizationSpec()
  {
    return $this->personalizationSpec;
  }
  /**
   * Raw search query.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The query expansion specification that specifies the conditions under which
   * query expansion occurs.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestQueryExpansionSpec $queryExpansionSpec
   */
  public function setQueryExpansionSpec(GoogleCloudDiscoveryengineV1betaSearchRequestQueryExpansionSpec $queryExpansionSpec)
  {
    $this->queryExpansionSpec = $queryExpansionSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestQueryExpansionSpec
   */
  public function getQueryExpansionSpec()
  {
    return $this->queryExpansionSpec;
  }
  /**
   * Optional. The ranking expression controls the customized ranking on
   * retrieval documents. This overrides ServingConfig.ranking_expression. The
   * syntax and supported features depend on the `ranking_expression_backend`
   * value. If `ranking_expression_backend` is not provided, it defaults to
   * `RANK_BY_EMBEDDING`. If ranking_expression_backend is not provided or set
   * to `RANK_BY_EMBEDDING`, it should be a single function or multiple
   * functions that are joined by "+". * ranking_expression = function, { " + ",
   * function }; Supported functions: * double * relevance_score * double *
   * dotProduct(embedding_field_path) Function variables: * `relevance_score`:
   * pre-defined keywords, used for measure relevance between query and
   * document. * `embedding_field_path`: the document embedding field used with
   * query embedding vector. * `dotProduct`: embedding function between
   * `embedding_field_path` and query embedding vector. Example ranking
   * expression: If document has an embedding field doc_embedding, the ranking
   * expression could be `0.5 * relevance_score + 0.3 *
   * dotProduct(doc_embedding)`. If ranking_expression_backend is set to
   * `RANK_BY_FORMULA`, the following expression types (and combinations of
   * those chained using + or * operators) are supported: * `double` * `signal`
   * * `log(signal)` * `exp(signal)` * `rr(signal, double > 0)` -- reciprocal
   * rank transformation with second argument being a denominator constant. *
   * `is_nan(signal)` -- returns 0 if signal is NaN, 1 otherwise. *
   * `fill_nan(signal1, signal2 | double)` -- if signal1 is NaN, returns signal2
   * | double, else returns signal1. Here are a few examples of ranking formulas
   * that use the supported ranking expression types: - `0.2 *
   * semantic_similarity_score + 0.8 * log(keyword_similarity_score)` -- mostly
   * rank by the logarithm of `keyword_similarity_score` with slight
   * `semantic_smilarity_score` adjustment. - `0.2 *
   * exp(fill_nan(semantic_similarity_score, 0)) + 0.3 *
   * is_nan(keyword_similarity_score)` -- rank by the exponent of
   * `semantic_similarity_score` filling the value with 0 if it's NaN, also add
   * constant 0.3 adjustment to the final score if `semantic_similarity_score`
   * is NaN. - `0.2 * rr(semantic_similarity_score, 16) + 0.8 *
   * rr(keyword_similarity_score, 16)` -- mostly rank by the reciprocal rank of
   * `keyword_similarity_score` with slight adjustment of reciprocal rank of
   * `semantic_smilarity_score`. The following signals are supported: *
   * `semantic_similarity_score`: semantic similarity adjustment that is
   * calculated using the embeddings generated by a proprietary Google model.
   * This score determines how semantically similar a search query is to a
   * document. * `keyword_similarity_score`: keyword match adjustment uses the
   * Best Match 25 (BM25) ranking function. This score is calculated using a
   * probabilistic model to estimate the probability that a document is relevant
   * to a given query. * `relevance_score`: semantic relevance adjustment that
   * uses a proprietary Google model to determine the meaning and intent behind
   * a user's query in context with the content in the documents. * `pctr_rank`:
   * predicted conversion rate adjustment as a rank use predicted Click-through
   * rate (pCTR) to gauge the relevance and attractiveness of a search result
   * from a user's perspective. A higher pCTR suggests that the result is more
   * likely to satisfy the user's query and intent, making it a valuable signal
   * for ranking. * `freshness_rank`: freshness adjustment as a rank *
   * `document_age`: The time in hours elapsed since the document was last
   * updated, a floating-point number (e.g., 0.25 means 15 minutes). *
   * `topicality_rank`: topicality adjustment as a rank. Uses proprietary Google
   * model to determine the keyword-based overlap between the query and the
   * document. * `base_rank`: the default rank of the result
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
   * Optional. The backend to use for the ranking expression evaluation.
   *
   * Accepted values: RANKING_EXPRESSION_BACKEND_UNSPECIFIED, BYOE, CLEARBOX,
   * RANK_BY_EMBEDDING, RANK_BY_FORMULA
   *
   * @param self::RANKING_EXPRESSION_BACKEND_* $rankingExpressionBackend
   */
  public function setRankingExpressionBackend($rankingExpressionBackend)
  {
    $this->rankingExpressionBackend = $rankingExpressionBackend;
  }
  /**
   * @return self::RANKING_EXPRESSION_BACKEND_*
   */
  public function getRankingExpressionBackend()
  {
    return $this->rankingExpressionBackend;
  }
  /**
   * The Unicode country/region code (CLDR) of a location, such as "US" and
   * "419". For more information, see [Standard
   * fields](https://cloud.google.com/apis/design/standard_fields). If set, then
   * results will be boosted based on the region_code provided.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * Optional. The specification for returning the relevance score.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestRelevanceScoreSpec $relevanceScoreSpec
   */
  public function setRelevanceScoreSpec(GoogleCloudDiscoveryengineV1betaSearchRequestRelevanceScoreSpec $relevanceScoreSpec)
  {
    $this->relevanceScoreSpec = $relevanceScoreSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestRelevanceScoreSpec
   */
  public function getRelevanceScoreSpec()
  {
    return $this->relevanceScoreSpec;
  }
  /**
   * The relevance threshold of the search results. Default to Google defined
   * threshold, leveraging a balance of precision and recall to deliver both
   * highly accurate results and comprehensive coverage of relevant information.
   * This feature is not supported for healthcare search.
   *
   * Accepted values: RELEVANCE_THRESHOLD_UNSPECIFIED, LOWEST, LOW, MEDIUM, HIGH
   *
   * @param self::RELEVANCE_THRESHOLD_* $relevanceThreshold
   */
  public function setRelevanceThreshold($relevanceThreshold)
  {
    $this->relevanceThreshold = $relevanceThreshold;
  }
  /**
   * @return self::RELEVANCE_THRESHOLD_*
   */
  public function getRelevanceThreshold()
  {
    return $this->relevanceThreshold;
  }
  /**
   * Whether to turn on safe search. This is only supported for website search.
   *
   * @param bool $safeSearch
   */
  public function setSafeSearch($safeSearch)
  {
    $this->safeSearch = $safeSearch;
  }
  /**
   * @return bool
   */
  public function getSafeSearch()
  {
    return $this->safeSearch;
  }
  /**
   * Optional. SearchAddonSpec is used to disable add-ons for search as per new
   * repricing model. This field is only supported for search requests.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestSearchAddonSpec $searchAddonSpec
   */
  public function setSearchAddonSpec(GoogleCloudDiscoveryengineV1betaSearchRequestSearchAddonSpec $searchAddonSpec)
  {
    $this->searchAddonSpec = $searchAddonSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestSearchAddonSpec
   */
  public function getSearchAddonSpec()
  {
    return $this->searchAddonSpec;
  }
  /**
   * Search as you type configuration. Only supported for the
   * IndustryVertical.MEDIA vertical.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestSearchAsYouTypeSpec $searchAsYouTypeSpec
   */
  public function setSearchAsYouTypeSpec(GoogleCloudDiscoveryengineV1betaSearchRequestSearchAsYouTypeSpec $searchAsYouTypeSpec)
  {
    $this->searchAsYouTypeSpec = $searchAsYouTypeSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestSearchAsYouTypeSpec
   */
  public function getSearchAsYouTypeSpec()
  {
    return $this->searchAsYouTypeSpec;
  }
  /**
   * Required. The resource name of the Search serving config, such as `projects
   * /locations/global/collections/default_collection/engines/servingConfigs/def
   * ault_serving_config`, or `projects/locations/global/collections/default_col
   * lection/dataStores/default_data_store/servingConfigs/default_serving_config
   * `. This field is used to identify the serving configuration name, set of
   * models used to make the search.
   *
   * @param string $servingConfig
   */
  public function setServingConfig($servingConfig)
  {
    $this->servingConfig = $servingConfig;
  }
  /**
   * @return string
   */
  public function getServingConfig()
  {
    return $this->servingConfig;
  }
  /**
   * The session resource name. Optional. Session allows users to do multi-turn
   * /search API calls or coordination between /search API calls and /answer API
   * calls. Example #1 (multi-turn /search API calls): Call /search API with the
   * session ID generated in the first call. Here, the previous search query
   * gets considered in query standing. I.e., if the first query is "How did
   * Alphabet do in 2022?" and the current query is "How about 2023?", the
   * current query will be interpreted as "How did Alphabet do in 2023?".
   * Example #2 (coordination between /search API calls and /answer API calls):
   * Call /answer API with the session ID generated in the first call. Here, the
   * answer generation happens in the context of the search results from the
   * first search call. Multi-turn Search feature is currently at private GA
   * stage. Please use v1alpha or v1beta version instead before we launch this
   * feature to public GA. Or ask for allowlisting through Google Support team.
   *
   * @param string $session
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return string
   */
  public function getSession()
  {
    return $this->session;
  }
  /**
   * Session specification. Can be used only when `session` is set.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestSessionSpec $sessionSpec
   */
  public function setSessionSpec(GoogleCloudDiscoveryengineV1betaSearchRequestSessionSpec $sessionSpec)
  {
    $this->sessionSpec = $sessionSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestSessionSpec
   */
  public function getSessionSpec()
  {
    return $this->sessionSpec;
  }
  /**
   * The spell correction specification that specifies the mode under which
   * spell correction takes effect.
   *
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestSpellCorrectionSpec $spellCorrectionSpec
   */
  public function setSpellCorrectionSpec(GoogleCloudDiscoveryengineV1betaSearchRequestSpellCorrectionSpec $spellCorrectionSpec)
  {
    $this->spellCorrectionSpec = $spellCorrectionSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestSpellCorrectionSpec
   */
  public function getSpellCorrectionSpec()
  {
    return $this->spellCorrectionSpec;
  }
  /**
   * Information about the end user. Highly recommended for analytics and
   * personalization. UserInfo.user_agent is used to deduce `device_type` for
   * analytics.
   *
   * @param GoogleCloudDiscoveryengineV1betaUserInfo $userInfo
   */
  public function setUserInfo(GoogleCloudDiscoveryengineV1betaUserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaUserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
  /**
   * The user labels applied to a resource must meet the following requirements:
   * * Each resource can have multiple labels, up to a maximum of 64. * Each
   * label must be a key-value pair. * Keys have a minimum length of 1 character
   * and a maximum length of 63 characters and cannot be empty. Values can be
   * empty and have a maximum length of 63 characters. * Keys and values can
   * contain only lowercase letters, numeric characters, underscores, and
   * dashes. All characters must use UTF-8 encoding, and international
   * characters are allowed. * The key portion of a label must be unique.
   * However, you can use the same key with multiple resources. * Keys must
   * start with a lowercase letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
  /**
   * Optional. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor logs in or out of the website. This field should NOT
   * have a fixed value such as `unknown_visitor`. This should be the same
   * identifier as UserEvent.user_pseudo_id and
   * CompleteQueryRequest.user_pseudo_id The field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @param string $userPseudoId
   */
  public function setUserPseudoId($userPseudoId)
  {
    $this->userPseudoId = $userPseudoId;
  }
  /**
   * @return string
   */
  public function getUserPseudoId()
  {
    return $this->userPseudoId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchRequest');
