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

namespace Google\Service\Monitoring;

class ListAlertPoliciesResponse extends \Google\Collection
{
  protected $collection_key = 'alertPolicies';
  protected $alertPoliciesType = AlertPolicy::class;
  protected $alertPoliciesDataType = 'array';
  /**
   * If there might be more results than were returned, then this field is set
   * to a non-empty value. To see the additional results, use that value as
   * page_token in the next call to this method.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The total number of alert policies in all pages. This number is only an
   * estimate, and may change in subsequent pages. https://aip.dev/158
   *
   * @var int
   */
  public $totalSize;

  /**
   * The returned alert policies.
   *
   * @param AlertPolicy[] $alertPolicies
   */
  public function setAlertPolicies($alertPolicies)
  {
    $this->alertPolicies = $alertPolicies;
  }
  /**
   * @return AlertPolicy[]
   */
  public function getAlertPolicies()
  {
    return $this->alertPolicies;
  }
  /**
   * If there might be more results than were returned, then this field is set
   * to a non-empty value. To see the additional results, use that value as
   * page_token in the next call to this method.
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
   * The total number of alert policies in all pages. This number is only an
   * estimate, and may change in subsequent pages. https://aip.dev/158
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAlertPoliciesResponse::class, 'Google_Service_Monitoring_ListAlertPoliciesResponse');
