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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1PurgeUserEventsRequest extends \Google\Model
{
  /**
   * Required. The filter string to specify the events to be deleted. Empty
   * string filter is not allowed. The eligible fields for filtering are: *
   * `eventType`: UserEvent.eventType field of type string. * `eventTime`: in
   * ISO 8601 "zulu" format. * `visitorId`: field of type string. Specifying
   * this will delete all events associated with a visitor. * `userId`: field of
   * type string. Specifying this will delete all events associated with a user.
   * Examples: * Deleting all events in a time range: `eventTime >
   * "2012-04-23T18:25:43.511Z" eventTime < "2012-04-23T18:30:43.511Z"` *
   * Deleting specific eventType in time range: `eventTime >
   * "2012-04-23T18:25:43.511Z" eventType = "detail-page-view"` * Deleting all
   * events for a specific visitor: `visitorId = "visitor1024"` The filtering
   * fields are assumed to have an implicit AND.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. The default value is false. Override this flag to true to
   * actually perform the purge. If the field is not set to true, a sampling of
   * events to be deleted will be returned.
   *
   * @var bool
   */
  public $force;

  /**
   * Required. The filter string to specify the events to be deleted. Empty
   * string filter is not allowed. The eligible fields for filtering are: *
   * `eventType`: UserEvent.eventType field of type string. * `eventTime`: in
   * ISO 8601 "zulu" format. * `visitorId`: field of type string. Specifying
   * this will delete all events associated with a visitor. * `userId`: field of
   * type string. Specifying this will delete all events associated with a user.
   * Examples: * Deleting all events in a time range: `eventTime >
   * "2012-04-23T18:25:43.511Z" eventTime < "2012-04-23T18:30:43.511Z"` *
   * Deleting specific eventType in time range: `eventTime >
   * "2012-04-23T18:25:43.511Z" eventType = "detail-page-view"` * Deleting all
   * events for a specific visitor: `visitorId = "visitor1024"` The filtering
   * fields are assumed to have an implicit AND.
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
   * Optional. The default value is false. Override this flag to true to
   * actually perform the purge. If the field is not set to true, a sampling of
   * events to be deleted will be returned.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1PurgeUserEventsRequest::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1PurgeUserEventsRequest');
