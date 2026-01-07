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

namespace Google\Service\CivicInfo\Resource;

use Google\Service\CivicInfo\CivicinfoApiprotosV2DivisionByAddressResponse;
use Google\Service\CivicInfo\CivicinfoApiprotosV2DivisionSearchResponse;

/**
 * The "divisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $civicinfoService = new Google\Service\CivicInfo(...);
 *   $divisions = $civicinfoService->divisions;
 *  </code>
 */
class Divisions extends \Google\Service\Resource
{
  /**
   * Lookup OCDIDs and names for divisions related to an address.
   * (divisions.queryDivisionByAddress)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string address
   * @return CivicinfoApiprotosV2DivisionByAddressResponse
   * @throws \Google\Service\Exception
   */
  public function queryDivisionByAddress($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('queryDivisionByAddress', [$params], CivicinfoApiprotosV2DivisionByAddressResponse::class);
  }
  /**
   * Searches for political divisions by their natural name or OCD ID.
   * (divisions.search)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string query The search query. Queries can cover any parts of a
   * OCD ID or a human readable division name. All words given in the query are
   * treated as required patterns. In addition to that, most query operators of
   * the Apache Lucene library are supported. See
   * http://lucene.apache.org/core/2_9_4/queryparsersyntax.html
   * @return CivicinfoApiprotosV2DivisionSearchResponse
   * @throws \Google\Service\Exception
   */
  public function search($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], CivicinfoApiprotosV2DivisionSearchResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Divisions::class, 'Google_Service_CivicInfo_Resource_Divisions');
