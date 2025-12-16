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

use Google\Service\Walletobjects\Issuer as IssuerModel;
use Google\Service\Walletobjects\IssuerListResponse;

/**
 * The "issuer" collection of methods.
 * Typical usage is:
 *  <code>
 *   $walletobjectsService = new Google\Service\Walletobjects(...);
 *   $issuer = $walletobjectsService->issuer;
 *  </code>
 */
class Issuer extends \Google\Service\Resource
{
  /**
   * Returns the issuer with the given issuer ID. (issuer.get)
   *
   * @param string $resourceId The unique identifier for an issuer.
   * @param array $optParams Optional parameters.
   * @return IssuerModel
   * @throws \Google\Service\Exception
   */
  public function get($resourceId, $optParams = [])
  {
    $params = ['resourceId' => $resourceId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], IssuerModel::class);
  }
  /**
   * Inserts an issuer with the given ID and properties. (issuer.insert)
   *
   * @param IssuerModel $postBody
   * @param array $optParams Optional parameters.
   * @return IssuerModel
   * @throws \Google\Service\Exception
   */
  public function insert(IssuerModel $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], IssuerModel::class);
  }
  /**
   * Returns a list of all issuers shared to the caller. (issuer.listIssuer)
   *
   * @param array $optParams Optional parameters.
   * @return IssuerListResponse
   * @throws \Google\Service\Exception
   */
  public function listIssuer($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], IssuerListResponse::class);
  }
  /**
   * Updates the issuer referenced by the given issuer ID. This method supports
   * patch semantics. (issuer.patch)
   *
   * @param string $resourceId The unique identifier for an issuer.
   * @param IssuerModel $postBody
   * @param array $optParams Optional parameters.
   * @return IssuerModel
   * @throws \Google\Service\Exception
   */
  public function patch($resourceId, IssuerModel $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], IssuerModel::class);
  }
  /**
   * Updates the issuer referenced by the given issuer ID. (issuer.update)
   *
   * @param string $resourceId The unique identifier for an issuer.
   * @param IssuerModel $postBody
   * @param array $optParams Optional parameters.
   * @return IssuerModel
   * @throws \Google\Service\Exception
   */
  public function update($resourceId, IssuerModel $postBody, $optParams = [])
  {
    $params = ['resourceId' => $resourceId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], IssuerModel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Issuer::class, 'Google_Service_Walletobjects_Resource_Issuer');
