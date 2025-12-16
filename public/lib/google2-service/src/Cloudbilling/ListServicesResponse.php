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

namespace Google\Service\Cloudbilling;

class ListServicesResponse extends \Google\Collection
{
  protected $collection_key = 'services';
  /**
   * A token to retrieve the next page of results. To retrieve the next page,
   * call `ListServices` again with the `page_token` field set to this value.
   * This field is empty if there are no more results to retrieve.
   *
   * @var string
   */
  public $nextPageToken;
  protected $servicesType = Service::class;
  protected $servicesDataType = 'array';

  /**
   * A token to retrieve the next page of results. To retrieve the next page,
   * call `ListServices` again with the `page_token` field set to this value.
   * This field is empty if there are no more results to retrieve.
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
   * A list of services.
   *
   * @param Service[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return Service[]
   */
  public function getServices()
  {
    return $this->services;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListServicesResponse::class, 'Google_Service_Cloudbilling_ListServicesResponse');
