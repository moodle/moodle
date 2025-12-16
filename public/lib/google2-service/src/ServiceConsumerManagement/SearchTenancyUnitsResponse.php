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

namespace Google\Service\ServiceConsumerManagement;

class SearchTenancyUnitsResponse extends \Google\Collection
{
  protected $collection_key = 'tenancyUnits';
  /**
   * Pagination token for large results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $tenancyUnitsType = TenancyUnit::class;
  protected $tenancyUnitsDataType = 'array';

  /**
   * Pagination token for large results.
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
   * Tenancy Units matching the request.
   *
   * @param TenancyUnit[] $tenancyUnits
   */
  public function setTenancyUnits($tenancyUnits)
  {
    $this->tenancyUnits = $tenancyUnits;
  }
  /**
   * @return TenancyUnit[]
   */
  public function getTenancyUnits()
  {
    return $this->tenancyUnits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchTenancyUnitsResponse::class, 'Google_Service_ServiceConsumerManagement_SearchTenancyUnitsResponse');
