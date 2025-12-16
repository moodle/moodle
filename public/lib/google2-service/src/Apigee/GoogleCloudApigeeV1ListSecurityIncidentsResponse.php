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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ListSecurityIncidentsResponse extends \Google\Collection
{
  protected $collection_key = 'securityIncidents';
  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $securityIncidentsType = GoogleCloudApigeeV1SecurityIncident::class;
  protected $securityIncidentsDataType = 'array';

  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted, there are no subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * List of security incidents in the organization
   *
   * @param GoogleCloudApigeeV1SecurityIncident[] $securityIncidents
   */
  public function setSecurityIncidents($securityIncidents)
  {
    $this->securityIncidents = $securityIncidents;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityIncident[]
   */
  public function getSecurityIncidents()
  {
    return $this->securityIncidents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ListSecurityIncidentsResponse::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ListSecurityIncidentsResponse');
