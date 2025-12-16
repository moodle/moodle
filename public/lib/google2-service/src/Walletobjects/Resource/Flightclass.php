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

namespace Google\Service\Walletobjects\Resource;

use Google\Service\Walletobjects\AddMessageRequest;
use Google\Service\Walletobjects\FlightClass as FlightClassModel;
use Google\Service\Walletobjects\FlightClassAddMessageResponse;
use Google\Service\Walletobjects\FlightClassListResponse;

/**
 * The "flightclass" collection of methods.
 * Typical usage is:
 *  <code>
 *   $walletobjectsService = new Google\Service\Walletobjects(...);
 *   $flightclass = $walletobjectsService->flightclass;
 *  </code>
 */
class Flightclass extends \Google\Service\Resource
{
  /**
   * Adds a message to the flight class referenced by the given class ID.
   * (flightclass.addmessage)
   *
   * @param string $resourceId The unique identifier for a class. This ID must be
   * unique across all classes from an issuer. This value should follow the format
   * issuer ID. identifier where the former is issued by Google and latter is
   * chosen by you. Your unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   * @param AddMessageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FlightClassAddMessageResponse
   * @throws \Google\Service\Exception
   */
  public function addmessage($resourceId, AddMessageRequest $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('addmessage', [$params], FlightClassAddMessageResponse::class);
  }
  /**
   * Returns the flight class with the given class ID. (flightclass.get)
   *
   * @param string $resourceId The unique identifier for a class. This ID must be
   * unique across all classes from an issuer. This value should follow the format
   * issuer ID. identifier where the former is issued by Google and latter is
   * chosen by you. Your unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   * @param array $optParams Optional parameters.
   * @return FlightClassModel
   * @throws \Google\Service\Exception
   */
  public function get($resourceId, $optParams = [])
  {
    $params = ['resourceId' => $resourceId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], FlightClassModel::class);
  }
  /**
   * Inserts an flight class with the given ID and properties.
   * (flightclass.insert)
   *
   * @param FlightClassModel $postBody
   * @param array $optParams Optional parameters.
   * @return FlightClassModel
   * @throws \Google\Service\Exception
   */
  public function insert(FlightClassModel $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], FlightClassModel::class);
  }
  /**
   * Returns a list of all flight classes for a given issuer ID.
   * (flightclass.listFlightclass)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string issuerId The ID of the issuer authorized to list classes.
   * @opt_param int maxResults Identifies the max number of results returned by a
   * list. All results are returned if `maxResults` isn't defined.
   * @opt_param string token Used to get the next set of results if `maxResults`
   * is specified, but more than `maxResults` classes are available in a list. For
   * example, if you have a list of 200 classes and you call list with
   * `maxResults` set to 20, list will return the first 20 classes and a token.
   * Call list again with `maxResults` set to 20 and the token to get the next 20
   * classes.
   * @return FlightClassListResponse
   * @throws \Google\Service\Exception
   */
  public function listFlightclass($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], FlightClassListResponse::class);
  }
  /**
   * Updates the flight class referenced by the given class ID. This method
   * supports patch semantics. (flightclass.patch)
   *
   * @param string $resourceId The unique identifier for a class. This ID must be
   * unique across all classes from an issuer. This value should follow the format
   * issuer ID. identifier where the former is issued by Google and latter is
   * chosen by you. Your unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   * @param FlightClassModel $postBody
   * @param array $optParams Optional parameters.
   * @return FlightClassModel
   * @throws \Google\Service\Exception
   */
  public function patch($resourceId, FlightClassModel $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], FlightClassModel::class);
  }
  /**
   * Updates the flight class referenced by the given class ID.
   * (flightclass.update)
   *
   * @param string $resourceId The unique identifier for a class. This ID must be
   * unique across all classes from an issuer. This value should follow the format
   * issuer ID. identifier where the former is issued by Google and latter is
   * chosen by you. Your unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   * @param FlightClassModel $postBody
   * @param array $optParams Optional parameters.
   * @return FlightClassModel
   * @throws \Google\Service\Exception
   */
  public function update($resourceId, FlightClassModel $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], FlightClassModel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Flightclass::class, 'Google_Service_Walletobjects_Resource_Flightclass');
