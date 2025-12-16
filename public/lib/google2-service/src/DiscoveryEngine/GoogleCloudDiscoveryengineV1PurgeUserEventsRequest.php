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

class GoogleCloudDiscoveryengineV1PurgeUserEventsRequest extends \Google\Model
{
  /**
   * Required. The filter string to specify the events to be deleted with a
   * length limit of 5,000 characters. The eligible fields for filtering are: *
   * `eventType`: Double quoted UserEvent.event_type string. * `eventTime`: in
   * ISO 8601 "zulu" format. * `userPseudoId`: Double quoted string. Specifying
   * this will delete all events associated with a visitor. * `userId`: Double
   * quoted string. Specifying this will delete all events associated with a
   * user. Note: This API only supports purging a max range of 30 days.
   * Examples: * Deleting all events in a time range: `eventTime >
   * "2012-04-23T18:25:43.511Z" eventTime < "2012-04-23T18:30:43.511Z"` *
   * Deleting specific eventType in a time range: `eventTime >
   * "2012-04-23T18:25:43.511Z" eventTime < "2012-04-23T18:30:43.511Z" eventType
   * = "search"` * Deleting all events for a specific visitor in a time range:
   * `eventTime > "2012-04-23T18:25:43.511Z" eventTime <
   * "2012-04-23T18:30:43.511Z" userPseudoId = "visitor1024"` * Deleting the
   * past 30 days of events inside a DataStore: `*` The filtering fields are
   * assumed to have an implicit AND.
   *
   * @var string
   */
  public $filter;
  /**
   * The `force` field is currently not supported. Purge user event requests
   * will permanently delete all purgeable events. Once the development is
   * complete: If `force` is set to false, the method will return the expected
   * purge count without deleting any user events. This field will default to
   * false if not included in the request.
   *
   * @var bool
   */
  public $force;

  /**
   * Required. The filter string to specify the events to be deleted with a
   * length limit of 5,000 characters. The eligible fields for filtering are: *
   * `eventType`: Double quoted UserEvent.event_type string. * `eventTime`: in
   * ISO 8601 "zulu" format. * `userPseudoId`: Double quoted string. Specifying
   * this will delete all events associated with a visitor. * `userId`: Double
   * quoted string. Specifying this will delete all events associated with a
   * user. Note: This API only supports purging a max range of 30 days.
   * Examples: * Deleting all events in a time range: `eventTime >
   * "2012-04-23T18:25:43.511Z" eventTime < "2012-04-23T18:30:43.511Z"` *
   * Deleting specific eventType in a time range: `eventTime >
   * "2012-04-23T18:25:43.511Z" eventTime < "2012-04-23T18:30:43.511Z" eventType
   * = "search"` * Deleting all events for a specific visitor in a time range:
   * `eventTime > "2012-04-23T18:25:43.511Z" eventTime <
   * "2012-04-23T18:30:43.511Z" userPseudoId = "visitor1024"` * Deleting the
   * past 30 days of events inside a DataStore: `*` The filtering fields are
   * assumed to have an implicit AND.
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
   * The `force` field is currently not supported. Purge user event requests
   * will permanently delete all purgeable events. Once the development is
   * complete: If `force` is set to false, the method will return the expected
   * purge count without deleting any user events. This field will default to
   * false if not included in the request.
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
class_alias(GoogleCloudDiscoveryengineV1PurgeUserEventsRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1PurgeUserEventsRequest');
