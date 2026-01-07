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

class GoogleCloudApigeeV1UpdateAppGroupAppKeyRequest extends \Google\Collection
{
  protected $collection_key = 'apiProducts';
  /**
   * Approve or revoke the consumer key by setting this value to `approve` or
   * `revoke` respectively. The `Content-Type` header, if set, must be set to
   * `application/octet-stream`, with empty body.
   *
   * @var string
   */
  public $action;
  /**
   * The list of API products that will be associated with the credential. This
   * list will be appended to the existing list of associated API Products for
   * this App Key. Duplicates will be ignored.
   *
   * @var string[]
   */
  public $apiProducts;
  protected $appGroupAppKeyType = GoogleCloudApigeeV1AppGroupAppKey::class;
  protected $appGroupAppKeyDataType = '';

  /**
   * Approve or revoke the consumer key by setting this value to `approve` or
   * `revoke` respectively. The `Content-Type` header, if set, must be set to
   * `application/octet-stream`, with empty body.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * The list of API products that will be associated with the credential. This
   * list will be appended to the existing list of associated API Products for
   * this App Key. Duplicates will be ignored.
   *
   * @param string[] $apiProducts
   */
  public function setApiProducts($apiProducts)
  {
    $this->apiProducts = $apiProducts;
  }
  /**
   * @return string[]
   */
  public function getApiProducts()
  {
    return $this->apiProducts;
  }
  /**
   * Note that only Scopes and Attributes of the AppGroupAppKey can be amended.
   * Scopes and Attributes mentioned in the request will be inserted and the
   * existing scopes and attributes will be removed.
   *
   * @param GoogleCloudApigeeV1AppGroupAppKey $appGroupAppKey
   */
  public function setAppGroupAppKey(GoogleCloudApigeeV1AppGroupAppKey $appGroupAppKey)
  {
    $this->appGroupAppKey = $appGroupAppKey;
  }
  /**
   * @return GoogleCloudApigeeV1AppGroupAppKey
   */
  public function getAppGroupAppKey()
  {
    return $this->appGroupAppKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1UpdateAppGroupAppKeyRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1UpdateAppGroupAppKeyRequest');
