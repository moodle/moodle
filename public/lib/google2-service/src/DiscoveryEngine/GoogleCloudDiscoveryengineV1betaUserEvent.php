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

class GoogleCloudDiscoveryengineV1betaUserEvent extends \Google\Collection
{
  protected $collection_key = 'tagIds';
  protected $attributesType = GoogleCloudDiscoveryengineV1betaCustomAttribute::class;
  protected $attributesDataType = 'map';
  /**
   * @var string
   */
  public $attributionToken;
  protected $completionInfoType = GoogleCloudDiscoveryengineV1betaCompletionInfo::class;
  protected $completionInfoDataType = '';
  /**
   * @var bool
   */
  public $directUserRequest;
  protected $documentsType = GoogleCloudDiscoveryengineV1betaDocumentInfo::class;
  protected $documentsDataType = 'array';
  /**
   * @var string
   */
  public $eventTime;
  /**
   * @var string
   */
  public $eventType;
  /**
   * @var string
   */
  public $filter;
  protected $mediaInfoType = GoogleCloudDiscoveryengineV1betaMediaInfo::class;
  protected $mediaInfoDataType = '';
  protected $pageInfoType = GoogleCloudDiscoveryengineV1betaPageInfo::class;
  protected $pageInfoDataType = '';
  protected $panelType = GoogleCloudDiscoveryengineV1betaPanelInfo::class;
  protected $panelDataType = '';
  /**
   * @var string[]
   */
  public $promotionIds;
  protected $searchInfoType = GoogleCloudDiscoveryengineV1betaSearchInfo::class;
  protected $searchInfoDataType = '';
  /**
   * @var string
   */
  public $sessionId;
  /**
   * @var string[]
   */
  public $tagIds;
  protected $transactionInfoType = GoogleCloudDiscoveryengineV1betaTransactionInfo::class;
  protected $transactionInfoDataType = '';
  protected $userInfoType = GoogleCloudDiscoveryengineV1betaUserInfo::class;
  protected $userInfoDataType = '';
  /**
   * @var string
   */
  public $userPseudoId;

  /**
   * @param GoogleCloudDiscoveryengineV1betaCustomAttribute[]
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCustomAttribute[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * @param string
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
   * @param GoogleCloudDiscoveryengineV1betaCompletionInfo
   */
  public function setCompletionInfo(GoogleCloudDiscoveryengineV1betaCompletionInfo $completionInfo)
  {
    $this->completionInfo = $completionInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaCompletionInfo
   */
  public function getCompletionInfo()
  {
    return $this->completionInfo;
  }
  /**
   * @param bool
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
   * @param GoogleCloudDiscoveryengineV1betaDocumentInfo[]
   */
  public function setDocuments($documents)
  {
    $this->documents = $documents;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaDocumentInfo[]
   */
  public function getDocuments()
  {
    return $this->documents;
  }
  /**
   * @param string
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
   * @param string
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
   * @param string
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
   * @param GoogleCloudDiscoveryengineV1betaMediaInfo
   */
  public function setMediaInfo(GoogleCloudDiscoveryengineV1betaMediaInfo $mediaInfo)
  {
    $this->mediaInfo = $mediaInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaMediaInfo
   */
  public function getMediaInfo()
  {
    return $this->mediaInfo;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaPageInfo
   */
  public function setPageInfo(GoogleCloudDiscoveryengineV1betaPageInfo $pageInfo)
  {
    $this->pageInfo = $pageInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaPageInfo
   */
  public function getPageInfo()
  {
    return $this->pageInfo;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaPanelInfo
   */
  public function setPanel(GoogleCloudDiscoveryengineV1betaPanelInfo $panel)
  {
    $this->panel = $panel;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaPanelInfo
   */
  public function getPanel()
  {
    return $this->panel;
  }
  /**
   * @param string[]
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
   * @param GoogleCloudDiscoveryengineV1betaSearchInfo
   */
  public function setSearchInfo(GoogleCloudDiscoveryengineV1betaSearchInfo $searchInfo)
  {
    $this->searchInfo = $searchInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchInfo
   */
  public function getSearchInfo()
  {
    return $this->searchInfo;
  }
  /**
   * @param string
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
   * @param string[]
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
   * @param GoogleCloudDiscoveryengineV1betaTransactionInfo
   */
  public function setTransactionInfo(GoogleCloudDiscoveryengineV1betaTransactionInfo $transactionInfo)
  {
    $this->transactionInfo = $transactionInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaTransactionInfo
   */
  public function getTransactionInfo()
  {
    return $this->transactionInfo;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaUserInfo
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
   * @param string
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
class_alias(GoogleCloudDiscoveryengineV1betaUserEvent::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaUserEvent');
