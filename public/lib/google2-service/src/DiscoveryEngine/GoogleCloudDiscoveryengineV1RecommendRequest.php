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

class GoogleCloudDiscoveryengineV1RecommendRequest extends \Google\Model
{
  /**
   * Filter for restricting recommendation results with a length limit of 5,000
   * characters. Currently, only filter expressions on the `filter_tags`
   * attribute is supported. Examples: * `(filter_tags: ANY("Red", "Blue") OR
   * filter_tags: ANY("Hot", "Cold"))` * `(filter_tags: ANY("Red", "Blue")) AND
   * NOT (filter_tags: ANY("Green"))` If `attributeFilteringSyntax` is set to
   * true under the `params` field, then attribute-based expressions are
   * expected instead of the above described tag-based syntax. Examples: *
   * (language: ANY("en", "es")) AND NOT (categories: ANY("Movie")) *
   * (available: true) AND (language: ANY("en", "es")) OR (categories:
   * ANY("Movie")) If your filter blocks all results, the API returns generic
   * (unfiltered) popular Documents. If you only want results strictly matching
   * the filters, set `strictFiltering` to `true` in RecommendRequest.params to
   * receive empty results instead. Note that the API never returns Documents
   * with `storageStatus` as `EXPIRED` or `DELETED` regardless of filter
   * choices.
   *
   * @var string
   */
  public $filter;
  /**
   * Maximum number of results to return. Set this property to the number of
   * recommendation results needed. If zero, the service chooses a reasonable
   * default. The maximum allowed value is 100. Values above 100 are set to 100.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Additional domain specific parameters for the recommendations. Allowed
   * values: * `returnDocument`: Boolean. If set to `true`, the associated
   * Document object is returned in
   * RecommendResponse.RecommendationResult.document. * `returnScore`: Boolean.
   * If set to true, the recommendation score corresponding to each returned
   * Document is set in RecommendResponse.RecommendationResult.metadata. The
   * given score indicates the probability of a Document conversion given the
   * user's context and history. * `strictFiltering`: Boolean. True by default.
   * If set to `false`, the service returns generic (unfiltered) popular
   * Documents instead of empty if your filter blocks all recommendation
   * results. * `diversityLevel`: String. Default empty. If set to be non-empty,
   * then it needs to be one of: * `no-diversity` * `low-diversity` * `medium-
   * diversity` * `high-diversity` * `auto-diversity` This gives request-level
   * control and adjusts recommendation results based on Document category. *
   * `attributeFilteringSyntax`: Boolean. False by default. If set to true, the
   * `filter` field is interpreted according to the new, attribute-based syntax.
   *
   * @var array[]
   */
  public $params;
  protected $userEventType = GoogleCloudDiscoveryengineV1UserEvent::class;
  protected $userEventDataType = '';
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
   * start with a lowercase letter or international character. See [Requirements
   * for labels](https://cloud.google.com/resource-manager/docs/creating-
   * managing-labels#requirements) for more details.
   *
   * @var string[]
   */
  public $userLabels;
  /**
   * Use validate only mode for this recommendation query. If set to `true`, a
   * fake model is used that returns arbitrary Document IDs. Note that the
   * validate only mode should only be used for testing the API, or if the model
   * is not ready.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Filter for restricting recommendation results with a length limit of 5,000
   * characters. Currently, only filter expressions on the `filter_tags`
   * attribute is supported. Examples: * `(filter_tags: ANY("Red", "Blue") OR
   * filter_tags: ANY("Hot", "Cold"))` * `(filter_tags: ANY("Red", "Blue")) AND
   * NOT (filter_tags: ANY("Green"))` If `attributeFilteringSyntax` is set to
   * true under the `params` field, then attribute-based expressions are
   * expected instead of the above described tag-based syntax. Examples: *
   * (language: ANY("en", "es")) AND NOT (categories: ANY("Movie")) *
   * (available: true) AND (language: ANY("en", "es")) OR (categories:
   * ANY("Movie")) If your filter blocks all results, the API returns generic
   * (unfiltered) popular Documents. If you only want results strictly matching
   * the filters, set `strictFiltering` to `true` in RecommendRequest.params to
   * receive empty results instead. Note that the API never returns Documents
   * with `storageStatus` as `EXPIRED` or `DELETED` regardless of filter
   * choices.
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
   * Maximum number of results to return. Set this property to the number of
   * recommendation results needed. If zero, the service chooses a reasonable
   * default. The maximum allowed value is 100. Values above 100 are set to 100.
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
   * Additional domain specific parameters for the recommendations. Allowed
   * values: * `returnDocument`: Boolean. If set to `true`, the associated
   * Document object is returned in
   * RecommendResponse.RecommendationResult.document. * `returnScore`: Boolean.
   * If set to true, the recommendation score corresponding to each returned
   * Document is set in RecommendResponse.RecommendationResult.metadata. The
   * given score indicates the probability of a Document conversion given the
   * user's context and history. * `strictFiltering`: Boolean. True by default.
   * If set to `false`, the service returns generic (unfiltered) popular
   * Documents instead of empty if your filter blocks all recommendation
   * results. * `diversityLevel`: String. Default empty. If set to be non-empty,
   * then it needs to be one of: * `no-diversity` * `low-diversity` * `medium-
   * diversity` * `high-diversity` * `auto-diversity` This gives request-level
   * control and adjusts recommendation results based on Document category. *
   * `attributeFilteringSyntax`: Boolean. False by default. If set to true, the
   * `filter` field is interpreted according to the new, attribute-based syntax.
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
   * Required. Context about the user, what they are looking at and what action
   * they took to trigger the Recommend request. Note that this user event
   * detail won't be ingested to userEvent logs. Thus, a separate userEvent
   * write request is required for event logging. Don't set
   * UserEvent.user_pseudo_id or UserEvent.user_info.user_id to the same fixed
   * ID for different users. If you are trying to receive non-personalized
   * recommendations (not recommended; this can negatively impact model
   * performance), instead set UserEvent.user_pseudo_id to a random unique ID
   * and leave UserEvent.user_info.user_id unset.
   *
   * @param GoogleCloudDiscoveryengineV1UserEvent $userEvent
   */
  public function setUserEvent(GoogleCloudDiscoveryengineV1UserEvent $userEvent)
  {
    $this->userEvent = $userEvent;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1UserEvent
   */
  public function getUserEvent()
  {
    return $this->userEvent;
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
   * start with a lowercase letter or international character. See [Requirements
   * for labels](https://cloud.google.com/resource-manager/docs/creating-
   * managing-labels#requirements) for more details.
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
   * Use validate only mode for this recommendation query. If set to `true`, a
   * fake model is used that returns arbitrary Document IDs. Note that the
   * validate only mode should only be used for testing the API, or if the model
   * is not ready.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1RecommendRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1RecommendRequest');
