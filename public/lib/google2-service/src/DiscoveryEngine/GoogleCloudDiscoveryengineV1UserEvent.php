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

class GoogleCloudDiscoveryengineV1UserEvent extends \Google\Collection
{
  protected $collection_key = 'tagIds';
  protected $attributesType = GoogleCloudDiscoveryengineV1CustomAttribute::class;
  protected $attributesDataType = 'map';
  /**
   * Token to attribute an API response to user action(s) to trigger the event.
   * Highly recommended for user events that are the result of
   * RecommendationService.Recommend. This field enables accurate attribution of
   * recommendation model performance. The value must be one of: *
   * RecommendResponse.attribution_token for events that are the result of
   * RecommendationService.Recommend. * SearchResponse.attribution_token for
   * events that are the result of SearchService.Search. This token enables us
   * to accurately attribute page view or conversion completion back to the
   * event and the particular predict response containing this clicked/purchased
   * product. If user clicks on product K in the recommendation results, pass
   * RecommendResponse.attribution_token as a URL parameter to product K's page.
   * When recording events on product K's page, log the
   * RecommendResponse.attribution_token to this field.
   *
   * @var string
   */
  public $attributionToken;
  protected $completionInfoType = GoogleCloudDiscoveryengineV1CompletionInfo::class;
  protected $completionInfoDataType = '';
  /**
   * Optional. Conversion type. Required if UserEvent.event_type is
   * `conversion`. This is a customer-defined conversion name in lowercase
   * letters or numbers separated by "-", such as "watch", "good-visit" etc. Do
   * not set the field if UserEvent.event_type is not `conversion`. This mixes
   * the custom conversion event with predefined events like `search`, `view-
   * item` etc.
   *
   * @var string
   */
  public $conversionType;
  /**
   * The DataStore resource full name, of the form `projects/{project}/locations
   * /{location}/collections/{collection_id}/dataStores/{data_store_id}`.
   * Optional. Only required for user events whose data store can't by
   * determined by UserEvent.engine or UserEvent.documents. If data store is set
   * in the parent of write/import/collect user event requests, this field can
   * be omitted.
   *
   * @var string
   */
  public $dataStore;
  /**
   * Should set to true if the request is made directly from the end user, in
   * which case the UserEvent.user_info.user_agent can be populated from the
   * HTTP request. This flag should be set only if the API request is made
   * directly from the end user such as a mobile app (and not if a gateway or a
   * server is processing and pushing the user events). This should not be set
   * when using the JavaScript tag in UserEventService.CollectUserEvent.
   *
   * @var bool
   */
  public $directUserRequest;
  protected $documentsType = GoogleCloudDiscoveryengineV1DocumentInfo::class;
  protected $documentsDataType = 'array';
  /**
   * The Engine resource name, in the form of `projects/{project}/locations/{loc
   * ation}/collections/{collection_id}/engines/{engine_id}`. Optional. Only
   * required for Engine produced user events. For example, user events from
   * blended search.
   *
   * @var string
   */
  public $engine;
  /**
   * Only required for UserEventService.ImportUserEvents method. Timestamp of
   * when the user event happened.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Required. User event type. Allowed values are: Generic values: * `search`:
   * Search for Documents. * `view-item`: Detailed page view of a Document. *
   * `view-item-list`: View of a panel or ordered list of Documents. * `view-
   * home-page`: View of the home page. * `view-category-page`: View of a
   * category page, e.g. Home > Men > Jeans Retail-related values: * `add-to-
   * cart`: Add an item(s) to cart, e.g. in Retail online shopping * `purchase`:
   * Purchase an item(s) Media-related values: * `media-play`: Start/resume
   * watching a video, playing a song, etc. * `media-complete`: Finished or
   * stopped midway through a video, song, etc. Custom conversion value: *
   * `conversion`: Customer defined conversion event.
   *
   * @var string
   */
  public $eventType;
  /**
   * Optional. The filter syntax consists of an expression language for
   * constructing a predicate from one or more fields of the documents being
   * filtered. One example is for `search` events, the associated SearchRequest
   * may contain a filter expression in SearchRequest.filter conforming to
   * https://google.aip.dev/160#filtering. Similarly, for `view-item-list`
   * events that are generated from a RecommendRequest, this field may be
   * populated directly from RecommendRequest.filter conforming to
   * https://google.aip.dev/160#filtering. The value must be a UTF-8 encoded
   * string with a length limit of 1,000 characters. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
   *
   * @var string
   */
  public $filter;
  protected $mediaInfoType = GoogleCloudDiscoveryengineV1MediaInfo::class;
  protected $mediaInfoDataType = '';
  protected $pageInfoType = GoogleCloudDiscoveryengineV1PageInfo::class;
  protected $pageInfoDataType = '';
  protected $panelType = GoogleCloudDiscoveryengineV1PanelInfo::class;
  protected $panelDataType = '';
  protected $panelsType = GoogleCloudDiscoveryengineV1PanelInfo::class;
  protected $panelsDataType = 'array';
  /**
   * The promotion IDs if this is an event associated with promotions.
   * Currently, this field is restricted to at most one ID.
   *
   * @var string[]
   */
  public $promotionIds;
  protected $searchInfoType = GoogleCloudDiscoveryengineV1SearchInfo::class;
  protected $searchInfoDataType = '';
  /**
   * A unique identifier for tracking a visitor session with a length limit of
   * 128 bytes. A session is an aggregation of an end user behavior in a time
   * span. A general guideline to populate the session_id: 1. If user has no
   * activity for 30 min, a new session_id should be assigned. 2. The session_id
   * should be unique across users, suggest use uuid or add
   * UserEvent.user_pseudo_id as prefix.
   *
   * @var string
   */
  public $sessionId;
  /**
   * A list of identifiers for the independent experiment groups this user event
   * belongs to. This is used to distinguish between user events associated with
   * different experiment setups.
   *
   * @var string[]
   */
  public $tagIds;
  protected $transactionInfoType = GoogleCloudDiscoveryengineV1TransactionInfo::class;
  protected $transactionInfoDataType = '';
  protected $userInfoType = GoogleCloudDiscoveryengineV1UserInfo::class;
  protected $userInfoDataType = '';
  /**
   * Required. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor log in/out of the website. Do not set the field to
   * the same fixed ID for different users. This mixes the event history of
   * those users together, which results in degraded model quality. The field
   * must be a UTF-8 encoded string with a length limit of 128 characters.
   * Otherwise, an `INVALID_ARGUMENT` error is returned. The field should not
   * contain PII or user-data. We recommend to use Google Analytics [Client ID](
   * https://developers.google.com/analytics/devguides/collection/analyticsjs/fi
   * eld-reference#clientId) for this field.
   *
   * @var string
   */
  public $userPseudoId;

  /**
   * Extra user event features to include in the recommendation model. These
   * attributes must NOT contain data that needs to be parsed or processed
   * further, e.g. JSON or other encodings. If you provide custom attributes for
   * ingested user events, also include them in the user events that you
   * associate with prediction requests. Custom attribute formatting must be
   * consistent between imported events and events provided with prediction
   * requests. This lets the Discovery Engine API use those custom attributes
   * when training models and serving predictions, which helps improve
   * recommendation quality. This field needs to pass all below criteria,
   * otherwise an `INVALID_ARGUMENT` error is returned: * The key must be a
   * UTF-8 encoded string with a length limit of 5,000 characters. * For text
   * attributes, at most 400 values are allowed. Empty values are not allowed.
   * Each value must be a UTF-8 encoded string with a length limit of 256
   * characters. * For number attributes, at most 400 values are allowed. For
   * product recommendations, an example of extra user information is
   * `traffic_channel`, which is how a user arrives at the site. Users can
   * arrive at the site by coming to the site directly, coming through Google
   * search, or in other ways.
   *
   * @param GoogleCloudDiscoveryengineV1CustomAttribute[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1CustomAttribute[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Token to attribute an API response to user action(s) to trigger the event.
   * Highly recommended for user events that are the result of
   * RecommendationService.Recommend. This field enables accurate attribution of
   * recommendation model performance. The value must be one of: *
   * RecommendResponse.attribution_token for events that are the result of
   * RecommendationService.Recommend. * SearchResponse.attribution_token for
   * events that are the result of SearchService.Search. This token enables us
   * to accurately attribute page view or conversion completion back to the
   * event and the particular predict response containing this clicked/purchased
   * product. If user clicks on product K in the recommendation results, pass
   * RecommendResponse.attribution_token as a URL parameter to product K's page.
   * When recording events on product K's page, log the
   * RecommendResponse.attribution_token to this field.
   *
   * @param string $attributionToken
   */
  public function setAttributionToken($attributionToken)
  {
    $this->attributionToken = $attributionToken;
  }
  /**
   * @return string
   */
  public function getAttributionToken()
  {
    return $this->attributionToken;
  }
  /**
   * CompletionService.CompleteQuery details related to the event. This field
   * should be set for `search` event when autocomplete function is enabled and
   * the user clicks a suggestion for search.
   *
   * @param GoogleCloudDiscoveryengineV1CompletionInfo $completionInfo
   */
  public function setCompletionInfo(GoogleCloudDiscoveryengineV1CompletionInfo $completionInfo)
  {
    $this->completionInfo = $completionInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1CompletionInfo
   */
  public function getCompletionInfo()
  {
    return $this->completionInfo;
  }
  /**
   * Optional. Conversion type. Required if UserEvent.event_type is
   * `conversion`. This is a customer-defined conversion name in lowercase
   * letters or numbers separated by "-", such as "watch", "good-visit" etc. Do
   * not set the field if UserEvent.event_type is not `conversion`. This mixes
   * the custom conversion event with predefined events like `search`, `view-
   * item` etc.
   *
   * @param string $conversionType
   */
  public function setConversionType($conversionType)
  {
    $this->conversionType = $conversionType;
  }
  /**
   * @return string
   */
  public function getConversionType()
  {
    return $this->conversionType;
  }
  /**
   * The DataStore resource full name, of the form `projects/{project}/locations
   * /{location}/collections/{collection_id}/dataStores/{data_store_id}`.
   * Optional. Only required for user events whose data store can't by
   * determined by UserEvent.engine or UserEvent.documents. If data store is set
   * in the parent of write/import/collect user event requests, this field can
   * be omitted.
   *
   * @param string $dataStore
   */
  public function setDataStore($dataStore)
  {
    $this->dataStore = $dataStore;
  }
  /**
   * @return string
   */
  public function getDataStore()
  {
    return $this->dataStore;
  }
  /**
   * Should set to true if the request is made directly from the end user, in
   * which case the UserEvent.user_info.user_agent can be populated from the
   * HTTP request. This flag should be set only if the API request is made
   * directly from the end user such as a mobile app (and not if a gateway or a
   * server is processing and pushing the user events). This should not be set
   * when using the JavaScript tag in UserEventService.CollectUserEvent.
   *
   * @param bool $directUserRequest
   */
  public function setDirectUserRequest($directUserRequest)
  {
    $this->directUserRequest = $directUserRequest;
  }
  /**
   * @return bool
   */
  public function getDirectUserRequest()
  {
    return $this->directUserRequest;
  }
  /**
   * List of Documents associated with this user event. This field is optional
   * except for the following event types: * `view-item` * `add-to-cart` *
   * `purchase` * `media-play` * `media-complete` In a `search` event, this
   * field represents the documents returned to the end user on the current page
   * (the end user may have not finished browsing the whole page yet). When a
   * new page is returned to the end user, after pagination/filtering/ordering
   * even for the same query, a new `search` event with different
   * UserEvent.documents is desired.
   *
   * @param GoogleCloudDiscoveryengineV1DocumentInfo[] $documents
   */
  public function setDocuments($documents)
  {
    $this->documents = $documents;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1DocumentInfo[]
   */
  public function getDocuments()
  {
    return $this->documents;
  }
  /**
   * The Engine resource name, in the form of `projects/{project}/locations/{loc
   * ation}/collections/{collection_id}/engines/{engine_id}`. Optional. Only
   * required for Engine produced user events. For example, user events from
   * blended search.
   *
   * @param string $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return string
   */
  public function getEngine()
  {
    return $this->engine;
  }
  /**
   * Only required for UserEventService.ImportUserEvents method. Timestamp of
   * when the user event happened.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Required. User event type. Allowed values are: Generic values: * `search`:
   * Search for Documents. * `view-item`: Detailed page view of a Document. *
   * `view-item-list`: View of a panel or ordered list of Documents. * `view-
   * home-page`: View of the home page. * `view-category-page`: View of a
   * category page, e.g. Home > Men > Jeans Retail-related values: * `add-to-
   * cart`: Add an item(s) to cart, e.g. in Retail online shopping * `purchase`:
   * Purchase an item(s) Media-related values: * `media-play`: Start/resume
   * watching a video, playing a song, etc. * `media-complete`: Finished or
   * stopped midway through a video, song, etc. Custom conversion value: *
   * `conversion`: Customer defined conversion event.
   *
   * @param string $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return string
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * Optional. The filter syntax consists of an expression language for
   * constructing a predicate from one or more fields of the documents being
   * filtered. One example is for `search` events, the associated SearchRequest
   * may contain a filter expression in SearchRequest.filter conforming to
   * https://google.aip.dev/160#filtering. Similarly, for `view-item-list`
   * events that are generated from a RecommendRequest, this field may be
   * populated directly from RecommendRequest.filter conforming to
   * https://google.aip.dev/160#filtering. The value must be a UTF-8 encoded
   * string with a length limit of 1,000 characters. Otherwise, an
   * `INVALID_ARGUMENT` error is returned.
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
   * Media-specific info.
   *
   * @param GoogleCloudDiscoveryengineV1MediaInfo $mediaInfo
   */
  public function setMediaInfo(GoogleCloudDiscoveryengineV1MediaInfo $mediaInfo)
  {
    $this->mediaInfo = $mediaInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1MediaInfo
   */
  public function getMediaInfo()
  {
    return $this->mediaInfo;
  }
  /**
   * Page metadata such as categories and other critical information for certain
   * event types such as `view-category-page`.
   *
   * @param GoogleCloudDiscoveryengineV1PageInfo $pageInfo
   */
  public function setPageInfo(GoogleCloudDiscoveryengineV1PageInfo $pageInfo)
  {
    $this->pageInfo = $pageInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1PageInfo
   */
  public function getPageInfo()
  {
    return $this->pageInfo;
  }
  /**
   * Panel metadata associated with this user event.
   *
   * @param GoogleCloudDiscoveryengineV1PanelInfo $panel
   */
  public function setPanel(GoogleCloudDiscoveryengineV1PanelInfo $panel)
  {
    $this->panel = $panel;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1PanelInfo
   */
  public function getPanel()
  {
    return $this->panel;
  }
  /**
   * Optional. List of panels associated with this event. Used for page-level
   * impression data.
   *
   * @param GoogleCloudDiscoveryengineV1PanelInfo[] $panels
   */
  public function setPanels($panels)
  {
    $this->panels = $panels;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1PanelInfo[]
   */
  public function getPanels()
  {
    return $this->panels;
  }
  /**
   * The promotion IDs if this is an event associated with promotions.
   * Currently, this field is restricted to at most one ID.
   *
   * @param string[] $promotionIds
   */
  public function setPromotionIds($promotionIds)
  {
    $this->promotionIds = $promotionIds;
  }
  /**
   * @return string[]
   */
  public function getPromotionIds()
  {
    return $this->promotionIds;
  }
  /**
   * SearchService.Search details related to the event. This field should be set
   * for `search` event.
   *
   * @param GoogleCloudDiscoveryengineV1SearchInfo $searchInfo
   */
  public function setSearchInfo(GoogleCloudDiscoveryengineV1SearchInfo $searchInfo)
  {
    $this->searchInfo = $searchInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SearchInfo
   */
  public function getSearchInfo()
  {
    return $this->searchInfo;
  }
  /**
   * A unique identifier for tracking a visitor session with a length limit of
   * 128 bytes. A session is an aggregation of an end user behavior in a time
   * span. A general guideline to populate the session_id: 1. If user has no
   * activity for 30 min, a new session_id should be assigned. 2. The session_id
   * should be unique across users, suggest use uuid or add
   * UserEvent.user_pseudo_id as prefix.
   *
   * @param string $sessionId
   */
  public function setSessionId($sessionId)
  {
    $this->sessionId = $sessionId;
  }
  /**
   * @return string
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }
  /**
   * A list of identifiers for the independent experiment groups this user event
   * belongs to. This is used to distinguish between user events associated with
   * different experiment setups.
   *
   * @param string[] $tagIds
   */
  public function setTagIds($tagIds)
  {
    $this->tagIds = $tagIds;
  }
  /**
   * @return string[]
   */
  public function getTagIds()
  {
    return $this->tagIds;
  }
  /**
   * The transaction metadata (if any) associated with this user event.
   *
   * @param GoogleCloudDiscoveryengineV1TransactionInfo $transactionInfo
   */
  public function setTransactionInfo(GoogleCloudDiscoveryengineV1TransactionInfo $transactionInfo)
  {
    $this->transactionInfo = $transactionInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1TransactionInfo
   */
  public function getTransactionInfo()
  {
    return $this->transactionInfo;
  }
  /**
   * Information about the end user.
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
   * Required. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor log in/out of the website. Do not set the field to
   * the same fixed ID for different users. This mixes the event history of
   * those users together, which results in degraded model quality. The field
   * must be a UTF-8 encoded string with a length limit of 128 characters.
   * Otherwise, an `INVALID_ARGUMENT` error is returned. The field should not
   * contain PII or user-data. We recommend to use Google Analytics [Client ID](
   * https://developers.google.com/analytics/devguides/collection/analyticsjs/fi
   * eld-reference#clientId) for this field.
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
class_alias(GoogleCloudDiscoveryengineV1UserEvent::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1UserEvent');
