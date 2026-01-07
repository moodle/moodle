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

class GoogleCloudApigeeV1ListSecurityReportsResponse extends \Google\Collection
{
  protected $collection_key = 'securityReports';
  /**
   * If the number of security reports exceeded the page size requested, the
   * token can be used to fetch the next page in a subsequent call. If the
   * response is the last page and there are no more reports to return this
   * field is left empty.
   *
   * @var string
   */
  public $nextPageToken;
  protected $securityReportsType = GoogleCloudApigeeV1SecurityReport::class;
  protected $securityReportsDataType = 'array';

  /**
   * If the number of security reports exceeded the page size requested, the
   * token can be used to fetch the next page in a subsequent call. If the
   * response is the last page and there are no more reports to return this
   * field is left empty.
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
   * The security reports belong to requested resource name.
   *
   * @param GoogleCloudApigeeV1SecurityReport[] $securityReports
   */
  public function setSecurityReports($securityReports)
  {
    $this->securityReports = $securityReports;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityReport[]
   */
  public function getSecurityReports()
  {
    return $this->securityReports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ListSecurityReportsResponse::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ListSecurityReportsResponse');
