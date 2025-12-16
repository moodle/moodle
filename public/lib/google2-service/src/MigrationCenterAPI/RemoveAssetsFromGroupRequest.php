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

namespace Google\Service\MigrationCenterAPI;

class RemoveAssetsFromGroupRequest extends \Google\Model
{
  /**
   * Optional. When this value is set to `false` and one of the given assets is
   * not an existing member of the group, the operation fails with a `Not Found`
   * error. When set to `true` this situation is silently ignored by the server.
   * Default value is `false`.
   *
   * @var bool
   */
  public $allowMissing;
  protected $assetsType = AssetList::class;
  protected $assetsDataType = '';
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;

  /**
   * Optional. When this value is set to `false` and one of the given assets is
   * not an existing member of the group, the operation fails with a `Not Found`
   * error. When set to `true` this situation is silently ignored by the server.
   * Default value is `false`.
   *
   * @param bool $allowMissing
   */
  public function setAllowMissing($allowMissing)
  {
    $this->allowMissing = $allowMissing;
  }
  /**
   * @return bool
   */
  public function getAllowMissing()
  {
    return $this->allowMissing;
  }
  /**
   * Required. List of assets to be removed. The maximum number of assets that
   * can be removed in a single request is 1000.
   *
   * @param AssetList $assets
   */
  public function setAssets(AssetList $assets)
  {
    $this->assets = $assets;
  }
  /**
   * @return AssetList
   */
  public function getAssets()
  {
    return $this->assets;
  }
  /**
   * Optional. An optional request ID to identify requests. Specify a unique
   * request ID so that if you must retry your request, the server will know to
   * ignore the request if it has already been completed. The server will
   * guarantee that for at least 60 minutes after the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoveAssetsFromGroupRequest::class, 'Google_Service_MigrationCenterAPI_RemoveAssetsFromGroupRequest');
