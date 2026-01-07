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
use Google\Service\Walletobjects\TransitObject as TransitObjectModel;
use Google\Service\Walletobjects\TransitObjectAddMessageResponse;
use Google\Service\Walletobjects\TransitObjectListResponse;

/**
 * The "transitobject" collection of methods.
 * Typical usage is:
 *  <code>
 *   $walletobjectsService = new Google\Service\Walletobjects(...);
 *   $transitobject = $walletobjectsService->transitobject;
 *  </code>
 */
class Transitobject extends \Google\Service\Resource
{
  /**
   * Adds a message to the transit object referenced by the given object ID.
   * (transitobject.addmessage)
   *
   * @param string $resourceId The unique identifier for an object. This ID must
   * be unique across all objects from an issuer. This value should follow the
   * format issuer ID. identifier where the former is issued by Google and latter
   * is chosen by you. Your unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   * @param AddMessageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TransitObjectAddMessageResponse
   * @throws \Google\Service\Exception
   */
  public function addmessage($resourceId, AddMessageRequest $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('addmessage', [$params], TransitObjectAddMessageResponse::class);
  }
  /**
   * Returns the transit object with the given object ID. (transitobject.get)
   *
   * @param string $resourceId The unique identifier for an object. This ID must
   * be unique across all objects from an issuer. This value should follow the
   * format issuer ID. identifier where the former is issued by Google and latter
   * is chosen by you. Your unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   * @param array $optParams Optional parameters.
   * @return TransitObjectModel
   * @throws \Google\Service\Exception
   */
  public function get($resourceId, $optParams = [])
  {
    $params = ['resourceId' => $resourceId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], TransitObjectModel::class);
  }
  /**
   * Inserts an transit object with the given ID and properties.
   * (transitobject.insert)
   *
   * @param TransitObjectModel $postBody
   * @param array $optParams Optional parameters.
   * @return TransitObjectModel
   * @throws \Google\Service\Exception
   */
  public function insert(TransitObjectModel $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], TransitObjectModel::class);
  }
  /**
   * Returns a list of all transit objects for a given issuer ID.
   * (transitobject.listTransitobject)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string classId The ID of the class whose objects will be listed.
   * @opt_param int maxResults Identifies the max number of results returned by a
   * list. All results are returned if `maxResults` isn't defined.
   * @opt_param string token Used to get the next set of results if `maxResults`
   * is specified, but more than `maxResults` objects are available in a list. For
   * example, if you have a list of 200 objects and you call list with
   * `maxResults` set to 20, list will return the first 20 objects and a token.
   * Call list again with `maxResults` set to 20 and the token to get the next 20
   * objects.
   * @return TransitObjectListResponse
   * @throws \Google\Service\Exception
   */
  public function listTransitobject($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], TransitObjectListResponse::class);
  }
  /**
   * Updates the transit object referenced by the given object ID. This method
   * supports patch semantics. (transitobject.patch)
   *
   * @param string $resourceId The unique identifier for an object. This ID must
   * be unique across all objects from an issuer. This value should follow the
   * format issuer ID. identifier where the former is issued by Google and latter
   * is chosen by you. Your unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   * @param TransitObjectModel $postBody
   * @param array $optParams Optional parameters.
   * @return TransitObjectModel
   * @throws \Google\Service\Exception
   */
  public function patch($resourceId, TransitObjectModel $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], TransitObjectModel::class);
  }
  /**
   * Updates the transit object referenced by the given object ID.
   * (transitobject.update)
   *
   * @param string $resourceId The unique identifier for an object. This ID must
   * be unique across all objects from an issuer. This value should follow the
   * format issuer ID. identifier where the former is issued by Google and latter
   * is chosen by you. Your unique identifier should only include alphanumeric
   * characters, '.', '_', or '-'.
   * @param TransitObjectModel $postBody
   * @param array $optParams Optional parameters.
   * @return TransitObjectModel
   * @throws \Google\Service\Exception
   */
  public function update($resourceId, TransitObjectModel $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], TransitObjectModel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Transitobject::class, 'Google_Service_Walletobjects_Resource_Transitobject');
