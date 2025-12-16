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

class GoogleCloudDiscoveryengineV1ConverseConversationRequest extends \Google\Model
{
  protected $boostSpecType = GoogleCloudDiscoveryengineV1SearchRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  protected $conversationType = GoogleCloudDiscoveryengineV1Conversation::class;
  protected $conversationDataType = '';
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the documents being filtered. Filter
   * expression is case-sensitive. This will be used to filter search results
   * which may affect the summary response. If this field is unrecognizable, an
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
  protected $queryType = GoogleCloudDiscoveryengineV1TextInput::class;
  protected $queryDataType = '';
  /**
   * Whether to turn on safe search.
   *
   * @var bool
   */
  public $safeSearch;
  /**
   * The resource name of the Serving Config to use. Format: `projects/{project}
   * /locations/{location}/collections/{collection}/dataStores/{data_store_id}/s
   * ervingConfigs/{serving_config_id}` If this is not set, the default serving
   * config will be used.
   *
   * @var string
   */
  public $servingConfig;
  protected $summarySpecType = GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecSummarySpec::class;
  protected $summarySpecDataType = '';
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
   * Boost specification to boost certain documents in search results which may
   * affect the converse response. For more information on boosting, see
   * [Boosting](https://cloud.google.com/retail/docs/boosting#boost)
   *
   * @param GoogleCloudDiscoveryengineV1SearchRequestBoostSpec $boostSpec
   */
  public function setBoostSpec(GoogleCloudDiscoveryengineV1SearchRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * The conversation to be used by auto session only. The name field will be
   * ignored as we automatically assign new name for the conversation in auto
   * session.
   *
   * @param GoogleCloudDiscoveryengineV1Conversation $conversation
   */
  public function setConversation(GoogleCloudDiscoveryengineV1Conversation $conversation)
  {
    $this->conversation = $conversation;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Conversation
   */
  public function getConversation()
  {
    return $this->conversation;
  }
  /**
   * The filter syntax consists of an expression language for constructing a
   * predicate from one or more fields of the documents being filtered. Filter
   * expression is case-sensitive. This will be used to filter search results
   * which may affect the summary response. If this field is unrecognizable, an
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
   * Required. Current user input.
   *
   * @param GoogleCloudDiscoveryengineV1TextInput $query
   */
  public function setQuery(GoogleCloudDiscoveryengineV1TextInput $query)
  {
    $this->query = $query;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1TextInput
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Whether to turn on safe search.
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
   * The resource name of the Serving Config to use. Format: `projects/{project}
   * /locations/{location}/collections/{collection}/dataStores/{data_store_id}/s
   * ervingConfigs/{serving_config_id}` If this is not set, the default serving
   * config will be used.
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
   * A specification for configuring the summary returned in the response.
   *
   * @param GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecSummarySpec $summarySpec
   */
  public function setSummarySpec(GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecSummarySpec $summarySpec)
  {
    $this->summarySpec = $summarySpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecSummarySpec
   */
  public function getSummarySpec()
  {
    return $this->summarySpec;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ConverseConversationRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ConverseConversationRequest');
