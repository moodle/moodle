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

class GoogleCloudRetailV2ConversationalSearchRequest extends \Google\Collection
{
  protected $collection_key = 'safetySettings';
  /**
   * Required. The branch resource name, such as
   * `projects/locations/global/catalogs/default_catalog/branches/0`. Use
   * "default_branch" as the branch ID or leave this field empty, to search
   * products under the default branch.
   *
   * @var string
   */
  public $branch;
  /**
   * Optional. This field specifies the conversation id, which maintains the
   * state of the conversation between client side and server side. Use the
   * value from the previous ConversationalSearchResponse.conversation_id. For
   * the initial request, this should be empty.
   *
   * @var string
   */
  public $conversationId;
  protected $conversationalFilteringSpecType = GoogleCloudRetailV2ConversationalSearchRequestConversationalFilteringSpec::class;
  protected $conversationalFilteringSpecDataType = '';
  /**
   * Optional. The categories associated with a category page. Must be set for
   * category navigation queries to achieve good search quality. The format
   * should be the same as UserEvent.page_categories; To represent full path of
   * category, use '>' sign to separate different hierarchies. If '>' is part of
   * the category name, replace it with other character(s). Category pages
   * include special pages such as sales or promotions. For instance, a special
   * sale page may have the category hierarchy: "pageCategories" : ["Sales >
   * 2017 Black Friday Deals"].
   *
   * @var string[]
   */
  public $pageCategories;
  /**
   * Optional. Raw search query to be searched for. If this field is empty, the
   * request is considered a category browsing request.
   *
   * @var string
   */
  public $query;
  protected $safetySettingsType = GoogleCloudRetailV2SafetySetting::class;
  protected $safetySettingsDataType = 'array';
  protected $searchParamsType = GoogleCloudRetailV2ConversationalSearchRequestSearchParams::class;
  protected $searchParamsDataType = '';
  protected $userInfoType = GoogleCloudRetailV2UserInfo::class;
  protected $userInfoDataType = '';
  /**
   * Optional. The user labels applied to a resource must meet the following
   * requirements: * Each resource can have multiple labels, up to a maximum of
   * 64. * Each label must be a key-value pair. * Keys have a minimum length of
   * 1 character and a maximum length of 63 characters and cannot be empty.
   * Values can be empty and have a maximum length of 63 characters. * Keys and
   * values can contain only lowercase letters, numeric characters, underscores,
   * and dashes. All characters must use UTF-8 encoding, and international
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
   * Required. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor logs in or out of the website. This should be the
   * same identifier as UserEvent.visitor_id. The field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $visitorId;

  /**
   * Required. The branch resource name, such as
   * `projects/locations/global/catalogs/default_catalog/branches/0`. Use
   * "default_branch" as the branch ID or leave this field empty, to search
   * products under the default branch.
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
   * Optional. This field specifies the conversation id, which maintains the
   * state of the conversation between client side and server side. Use the
   * value from the previous ConversationalSearchResponse.conversation_id. For
   * the initial request, this should be empty.
   *
   * @param string $conversationId
   */
  public function setConversationId($conversationId)
  {
    $this->conversationId = $conversationId;
  }
  /**
   * @return string
   */
  public function getConversationId()
  {
    return $this->conversationId;
  }
  /**
   * Optional. This field specifies all conversational filtering related
   * parameters.
   *
   * @param GoogleCloudRetailV2ConversationalSearchRequestConversationalFilteringSpec $conversationalFilteringSpec
   */
  public function setConversationalFilteringSpec(GoogleCloudRetailV2ConversationalSearchRequestConversationalFilteringSpec $conversationalFilteringSpec)
  {
    $this->conversationalFilteringSpec = $conversationalFilteringSpec;
  }
  /**
   * @return GoogleCloudRetailV2ConversationalSearchRequestConversationalFilteringSpec
   */
  public function getConversationalFilteringSpec()
  {
    return $this->conversationalFilteringSpec;
  }
  /**
   * Optional. The categories associated with a category page. Must be set for
   * category navigation queries to achieve good search quality. The format
   * should be the same as UserEvent.page_categories; To represent full path of
   * category, use '>' sign to separate different hierarchies. If '>' is part of
   * the category name, replace it with other character(s). Category pages
   * include special pages such as sales or promotions. For instance, a special
   * sale page may have the category hierarchy: "pageCategories" : ["Sales >
   * 2017 Black Friday Deals"].
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
   * Optional. Raw search query to be searched for. If this field is empty, the
   * request is considered a category browsing request.
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
   * Optional. The safety settings to be applied to the generated content.
   *
   * @param GoogleCloudRetailV2SafetySetting[] $safetySettings
   */
  public function setSafetySettings($safetySettings)
  {
    $this->safetySettings = $safetySettings;
  }
  /**
   * @return GoogleCloudRetailV2SafetySetting[]
   */
  public function getSafetySettings()
  {
    return $this->safetySettings;
  }
  /**
   * Optional. Search parameters.
   *
   * @param GoogleCloudRetailV2ConversationalSearchRequestSearchParams $searchParams
   */
  public function setSearchParams(GoogleCloudRetailV2ConversationalSearchRequestSearchParams $searchParams)
  {
    $this->searchParams = $searchParams;
  }
  /**
   * @return GoogleCloudRetailV2ConversationalSearchRequestSearchParams
   */
  public function getSearchParams()
  {
    return $this->searchParams;
  }
  /**
   * Optional. User information.
   *
   * @param GoogleCloudRetailV2UserInfo $userInfo
   */
  public function setUserInfo(GoogleCloudRetailV2UserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return GoogleCloudRetailV2UserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
  /**
   * Optional. The user labels applied to a resource must meet the following
   * requirements: * Each resource can have multiple labels, up to a maximum of
   * 64. * Each label must be a key-value pair. * Keys have a minimum length of
   * 1 character and a maximum length of 63 characters and cannot be empty.
   * Values can be empty and have a maximum length of 63 characters. * Keys and
   * values can contain only lowercase letters, numeric characters, underscores,
   * and dashes. All characters must use UTF-8 encoding, and international
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
   * Required. A unique identifier for tracking visitors. For example, this
   * could be implemented with an HTTP cookie, which should be able to uniquely
   * identify a visitor on a single device. This unique identifier should not
   * change if the visitor logs in or out of the website. This should be the
   * same identifier as UserEvent.visitor_id. The field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @param string $visitorId
   */
  public function setVisitorId($visitorId)
  {
    $this->visitorId = $visitorId;
  }
  /**
   * @return string
   */
  public function getVisitorId()
  {
    return $this->visitorId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ConversationalSearchRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ConversationalSearchRequest');
