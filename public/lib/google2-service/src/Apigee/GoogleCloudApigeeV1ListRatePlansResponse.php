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

class GoogleCloudApigeeV1ListRatePlansResponse extends \Google\Collection
{
  protected $collection_key = 'ratePlans';
  /**
   * Value that can be sent as `startKey` to retrieve the next page of content.
   * If this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextStartKey;
  protected $ratePlansType = GoogleCloudApigeeV1RatePlan::class;
  protected $ratePlansDataType = 'array';

  /**
   * Value that can be sent as `startKey` to retrieve the next page of content.
   * If this field is omitted, there are no subsequent pages.
   *
   * @param string $nextStartKey
   */
  public function setNextStartKey($nextStartKey)
  {
    $this->nextStartKey = $nextStartKey;
  }
  /**
   * @return string
   */
  public function getNextStartKey()
  {
    return $this->nextStartKey;
  }
  /**
   * List of rate plans in an organization.
   *
   * @param GoogleCloudApigeeV1RatePlan[] $ratePlans
   */
  public function setRatePlans($ratePlans)
  {
    $this->ratePlans = $ratePlans;
  }
  /**
   * @return GoogleCloudApigeeV1RatePlan[]
   */
  public function getRatePlans()
  {
    return $this->ratePlans;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ListRatePlansResponse::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ListRatePlansResponse');
