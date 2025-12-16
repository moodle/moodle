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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsRequest extends \Google\Collection
{
  protected $collection_key = 'resourceType';
  /**
   * Optional. If set, only return changes that match one or more of these types
   * of actions.
   *
   * @var string[]
   */
  public $action;
  /**
   * Optional. If set, only return changes if they are made by a user in this
   * list.
   *
   * @var string[]
   */
  public $actorEmail;
  /**
   * Optional. If set, only return changes made after this time (inclusive).
   *
   * @var string
   */
  public $earliestChangeTime;
  /**
   * Optional. If set, only return changes made before this time (inclusive).
   *
   * @var string
   */
  public $latestChangeTime;
  /**
   * Optional. The maximum number of ChangeHistoryEvent items to return. If
   * unspecified, at most 50 items will be returned. The maximum value is 200
   * (higher values will be coerced to the maximum). Note that the service may
   * return a page with fewer items than this value specifies (potentially even
   * zero), and that there still may be additional pages. If you want a
   * particular number of items, you'll need to continue requesting additional
   * pages using `page_token` until you get the needed number.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token, received from a previous
   * `SearchChangeHistoryEvents` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `SearchChangeHistoryEvents` must match the call that provided the page
   * token.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Optional. Resource name for a child property. If set, only return changes
   * made to this property or its child resources. Format:
   * properties/{propertyId} Example: `properties/100`
   *
   * @var string
   */
  public $property;
  /**
   * Optional. If set, only return changes if they are for a resource that
   * matches at least one of these types.
   *
   * @var string[]
   */
  public $resourceType;

  /**
   * Optional. If set, only return changes that match one or more of these types
   * of actions.
   *
   * @param string[] $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string[]
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Optional. If set, only return changes if they are made by a user in this
   * list.
   *
   * @param string[] $actorEmail
   */
  public function setActorEmail($actorEmail)
  {
    $this->actorEmail = $actorEmail;
  }
  /**
   * @return string[]
   */
  public function getActorEmail()
  {
    return $this->actorEmail;
  }
  /**
   * Optional. If set, only return changes made after this time (inclusive).
   *
   * @param string $earliestChangeTime
   */
  public function setEarliestChangeTime($earliestChangeTime)
  {
    $this->earliestChangeTime = $earliestChangeTime;
  }
  /**
   * @return string
   */
  public function getEarliestChangeTime()
  {
    return $this->earliestChangeTime;
  }
  /**
   * Optional. If set, only return changes made before this time (inclusive).
   *
   * @param string $latestChangeTime
   */
  public function setLatestChangeTime($latestChangeTime)
  {
    $this->latestChangeTime = $latestChangeTime;
  }
  /**
   * @return string
   */
  public function getLatestChangeTime()
  {
    return $this->latestChangeTime;
  }
  /**
   * Optional. The maximum number of ChangeHistoryEvent items to return. If
   * unspecified, at most 50 items will be returned. The maximum value is 200
   * (higher values will be coerced to the maximum). Note that the service may
   * return a page with fewer items than this value specifies (potentially even
   * zero), and that there still may be additional pages. If you want a
   * particular number of items, you'll need to continue requesting additional
   * pages using `page_token` until you get the needed number.
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
   * Optional. A page token, received from a previous
   * `SearchChangeHistoryEvents` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `SearchChangeHistoryEvents` must match the call that provided the page
   * token.
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
   * Optional. Resource name for a child property. If set, only return changes
   * made to this property or its child resources. Format:
   * properties/{propertyId} Example: `properties/100`
   *
   * @param string $property
   */
  public function setProperty($property)
  {
    $this->property = $property;
  }
  /**
   * @return string
   */
  public function getProperty()
  {
    return $this->property;
  }
  /**
   * Optional. If set, only return changes if they are for a resource that
   * matches at least one of these types.
   *
   * @param string[] $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string[]
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsRequest::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaSearchChangeHistoryEventsRequest');
