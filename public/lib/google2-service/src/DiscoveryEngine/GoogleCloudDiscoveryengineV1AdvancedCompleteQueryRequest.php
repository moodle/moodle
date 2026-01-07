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

class GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequest extends \Google\Collection
{
  protected $collection_key = 'suggestionTypes';
  protected $boostSpecType = GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  /**
   * Optional. Experiment ids for this request.
   *
   * @var string[]
   */
  public $experimentIds;
  /**
   * Indicates if tail suggestions should be returned if there are no
   * suggestions that match the full query. Even if set to true, if there are
   * suggestions that match the full query, those are returned and no tail
   * suggestions are returned.
   *
   * @var bool
   */
  public $includeTailSuggestions;
  /**
   * Required. The typeahead input used to fetch suggestions. Maximum length is
   * 128 characters. The query can not be empty for most of the suggestion
   * types. If it is empty, an `INVALID_ARGUMENT` error is returned. The
   * exception is when the suggestion_types contains only the type
   * `RECENT_SEARCH`, the query can be an empty string. The is called "zero
   * prefix" feature, which returns user's recently searched queries given the
   * empty query.
   *
   * @var string
   */
  public $query;
  /**
   * Specifies the autocomplete query model, which only applies to the QUERY
   * SuggestionType. This overrides any model specified in the Configuration >
   * Autocomplete section of the Cloud console. Currently supported values: *
   * `document` - Using suggestions generated from user-imported documents. *
   * `search-history` - Using suggestions generated from the past history of
   * SearchService.Search API calls. Do not use it when there is no traffic for
   * Search API. * `user-event` - Using suggestions generated from user-imported
   * search events. * `document-completable` - Using suggestions taken directly
   * from user-imported document fields marked as completable. Default values: *
   * `document` is the default model for regular dataStores. * `search-history`
   * is the default model for site search dataStores.
   *
   * @var string
   */
  public $queryModel;
  protected $suggestionTypeSpecsType = GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestSuggestionTypeSpec::class;
  protected $suggestionTypeSpecsDataType = 'array';
  /**
   * Optional. Suggestion types to return. If empty or unspecified, query
   * suggestions are returned. Only one suggestion type is supported at the
   * moment.
   *
   * @var string[]
   */
  public $suggestionTypes;
  protected $userInfoType = GoogleCloudDiscoveryengineV1UserInfo::class;
  protected $userInfoDataType = '';
  /**
   * Optional. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor logs in or out of the website. This field should NOT
   * have a fixed value such as `unknown_visitor`. This should be the same
   * identifier as UserEvent.user_pseudo_id and SearchRequest.user_pseudo_id.
   * The field must be a UTF-8 encoded string with a length limit of 128
   *
   * @var string
   */
  public $userPseudoId;

  /**
   * Optional. Specification to boost suggestions matching the condition.
   *
   * @param GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestBoostSpec $boostSpec
   */
  public function setBoostSpec(GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * Optional. Experiment ids for this request.
   *
   * @param string[] $experimentIds
   */
  public function setExperimentIds($experimentIds)
  {
    $this->experimentIds = $experimentIds;
  }
  /**
   * @return string[]
   */
  public function getExperimentIds()
  {
    return $this->experimentIds;
  }
  /**
   * Indicates if tail suggestions should be returned if there are no
   * suggestions that match the full query. Even if set to true, if there are
   * suggestions that match the full query, those are returned and no tail
   * suggestions are returned.
   *
   * @param bool $includeTailSuggestions
   */
  public function setIncludeTailSuggestions($includeTailSuggestions)
  {
    $this->includeTailSuggestions = $includeTailSuggestions;
  }
  /**
   * @return bool
   */
  public function getIncludeTailSuggestions()
  {
    return $this->includeTailSuggestions;
  }
  /**
   * Required. The typeahead input used to fetch suggestions. Maximum length is
   * 128 characters. The query can not be empty for most of the suggestion
   * types. If it is empty, an `INVALID_ARGUMENT` error is returned. The
   * exception is when the suggestion_types contains only the type
   * `RECENT_SEARCH`, the query can be an empty string. The is called "zero
   * prefix" feature, which returns user's recently searched queries given the
   * empty query.
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
   * Specifies the autocomplete query model, which only applies to the QUERY
   * SuggestionType. This overrides any model specified in the Configuration >
   * Autocomplete section of the Cloud console. Currently supported values: *
   * `document` - Using suggestions generated from user-imported documents. *
   * `search-history` - Using suggestions generated from the past history of
   * SearchService.Search API calls. Do not use it when there is no traffic for
   * Search API. * `user-event` - Using suggestions generated from user-imported
   * search events. * `document-completable` - Using suggestions taken directly
   * from user-imported document fields marked as completable. Default values: *
   * `document` is the default model for regular dataStores. * `search-history`
   * is the default model for site search dataStores.
   *
   * @param string $queryModel
   */
  public function setQueryModel($queryModel)
  {
    $this->queryModel = $queryModel;
  }
  /**
   * @return string
   */
  public function getQueryModel()
  {
    return $this->queryModel;
  }
  /**
   * Optional. Specification of each suggestion type.
   *
   * @param GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestSuggestionTypeSpec[] $suggestionTypeSpecs
   */
  public function setSuggestionTypeSpecs($suggestionTypeSpecs)
  {
    $this->suggestionTypeSpecs = $suggestionTypeSpecs;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestSuggestionTypeSpec[]
   */
  public function getSuggestionTypeSpecs()
  {
    return $this->suggestionTypeSpecs;
  }
  /**
   * Optional. Suggestion types to return. If empty or unspecified, query
   * suggestions are returned. Only one suggestion type is supported at the
   * moment.
   *
   * @param string[] $suggestionTypes
   */
  public function setSuggestionTypes($suggestionTypes)
  {
    $this->suggestionTypes = $suggestionTypes;
  }
  /**
   * @return string[]
   */
  public function getSuggestionTypes()
  {
    return $this->suggestionTypes;
  }
  /**
   * Optional. Information about the end user. This should be the same
   * identifier information as UserEvent.user_info and SearchRequest.user_info.
   *
   * @param GoogleCloudDiscoveryengineV1UserInfo $userInfo
   */
  public function setUserInfo(GoogleCloudDiscoveryengineV1UserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1UserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
  /**
   * Optional. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor logs in or out of the website. This field should NOT
   * have a fixed value such as `unknown_visitor`. This should be the same
   * identifier as UserEvent.user_pseudo_id and SearchRequest.user_pseudo_id.
   * The field must be a UTF-8 encoded string with a length limit of 128
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
class_alias(GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequest');
