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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1TransferEntitlementsRequest extends \Google\Collection
{
  protected $collection_key = 'entitlements';
  /**
   * The super admin of the resold customer generates this token to authorize a
   * reseller to access their Cloud Identity and purchase entitlements on their
   * behalf. You can omit this token after authorization. See
   * https://support.google.com/a/answer/7643790 for more details.
   *
   * @var string
   */
  public $authToken;
  protected $entitlementsType = GoogleCloudChannelV1Entitlement::class;
  protected $entitlementsDataType = 'array';
  /**
   * Optional. You can specify an optional unique request ID, and if you need to
   * retry your request, the server will know to ignore the request if it's
   * complete. For example, you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if it received the original operation with the same request ID. If it
   * did, it will ignore the second request. The request ID must be a valid
   * [UUID](https://tools.ietf.org/html/rfc4122) with the exception that zero
   * UUID is not supported (`00000000-0000-0000-0000-000000000000`).
   *
   * @var string
   */
  public $requestId;

  /**
   * The super admin of the resold customer generates this token to authorize a
   * reseller to access their Cloud Identity and purchase entitlements on their
   * behalf. You can omit this token after authorization. See
   * https://support.google.com/a/answer/7643790 for more details.
   *
   * @param string $authToken
   */
  public function setAuthToken($authToken)
  {
    $this->authToken = $authToken;
  }
  /**
   * @return string
   */
  public function getAuthToken()
  {
    return $this->authToken;
  }
  /**
   * Required. The new entitlements to create or transfer.
   *
   * @param GoogleCloudChannelV1Entitlement[] $entitlements
   */
  public function setEntitlements($entitlements)
  {
    $this->entitlements = $entitlements;
  }
  /**
   * @return GoogleCloudChannelV1Entitlement[]
   */
  public function getEntitlements()
  {
    return $this->entitlements;
  }
  /**
   * Optional. You can specify an optional unique request ID, and if you need to
   * retry your request, the server will know to ignore the request if it's
   * complete. For example, you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if it received the original operation with the same request ID. If it
   * did, it will ignore the second request. The request ID must be a valid
   * [UUID](https://tools.ietf.org/html/rfc4122) with the exception that zero
   * UUID is not supported (`00000000-0000-0000-0000-000000000000`).
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
class_alias(GoogleCloudChannelV1TransferEntitlementsRequest::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1TransferEntitlementsRequest');
