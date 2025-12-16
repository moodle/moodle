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

namespace Google\Service\Storagetransfer;

class S3CompatibleMetadata extends \Google\Model
{
  /**
   * AuthMethod is not specified.
   */
  public const AUTH_METHOD_AUTH_METHOD_UNSPECIFIED = 'AUTH_METHOD_UNSPECIFIED';
  /**
   * Auth requests with AWS SigV4.
   */
  public const AUTH_METHOD_AUTH_METHOD_AWS_SIGNATURE_V4 = 'AUTH_METHOD_AWS_SIGNATURE_V4';
  /**
   * Auth requests with AWS SigV2.
   */
  public const AUTH_METHOD_AUTH_METHOD_AWS_SIGNATURE_V2 = 'AUTH_METHOD_AWS_SIGNATURE_V2';
  /**
   * ListApi is not specified.
   */
  public const LIST_API_LIST_API_UNSPECIFIED = 'LIST_API_UNSPECIFIED';
  /**
   * Perform listing using ListObjectsV2 API.
   */
  public const LIST_API_LIST_OBJECTS_V2 = 'LIST_OBJECTS_V2';
  /**
   * Legacy ListObjects API.
   */
  public const LIST_API_LIST_OBJECTS = 'LIST_OBJECTS';
  /**
   * NetworkProtocol is not specified.
   */
  public const PROTOCOL_NETWORK_PROTOCOL_UNSPECIFIED = 'NETWORK_PROTOCOL_UNSPECIFIED';
  /**
   * Perform requests using HTTPS.
   */
  public const PROTOCOL_NETWORK_PROTOCOL_HTTPS = 'NETWORK_PROTOCOL_HTTPS';
  /**
   * Not recommended: This sends data in clear-text. This is only appropriate
   * within a closed network or for publicly available data. Perform requests
   * using HTTP.
   */
  public const PROTOCOL_NETWORK_PROTOCOL_HTTP = 'NETWORK_PROTOCOL_HTTP';
  /**
   * RequestModel is not specified.
   */
  public const REQUEST_MODEL_REQUEST_MODEL_UNSPECIFIED = 'REQUEST_MODEL_UNSPECIFIED';
  /**
   * Perform requests using Virtual Hosted Style. Example: https://bucket-
   * name.s3.region.amazonaws.com/key-name
   */
  public const REQUEST_MODEL_REQUEST_MODEL_VIRTUAL_HOSTED_STYLE = 'REQUEST_MODEL_VIRTUAL_HOSTED_STYLE';
  /**
   * Perform requests using Path Style. Example:
   * https://s3.region.amazonaws.com/bucket-name/key-name
   */
  public const REQUEST_MODEL_REQUEST_MODEL_PATH_STYLE = 'REQUEST_MODEL_PATH_STYLE';
  /**
   * Specifies the authentication and authorization method used by the storage
   * service. When not specified, Transfer Service will attempt to determine
   * right auth method to use.
   *
   * @var string
   */
  public $authMethod;
  /**
   * The Listing API to use for discovering objects. When not specified,
   * Transfer Service will attempt to determine the right API to use.
   *
   * @var string
   */
  public $listApi;
  /**
   * Specifies the network protocol of the agent. When not specified, the
   * default value of NetworkProtocol NETWORK_PROTOCOL_HTTPS is used.
   *
   * @var string
   */
  public $protocol;
  /**
   * Specifies the API request model used to call the storage service. When not
   * specified, the default value of RequestModel
   * REQUEST_MODEL_VIRTUAL_HOSTED_STYLE is used.
   *
   * @var string
   */
  public $requestModel;

  /**
   * Specifies the authentication and authorization method used by the storage
   * service. When not specified, Transfer Service will attempt to determine
   * right auth method to use.
   *
   * Accepted values: AUTH_METHOD_UNSPECIFIED, AUTH_METHOD_AWS_SIGNATURE_V4,
   * AUTH_METHOD_AWS_SIGNATURE_V2
   *
   * @param self::AUTH_METHOD_* $authMethod
   */
  public function setAuthMethod($authMethod)
  {
    $this->authMethod = $authMethod;
  }
  /**
   * @return self::AUTH_METHOD_*
   */
  public function getAuthMethod()
  {
    return $this->authMethod;
  }
  /**
   * The Listing API to use for discovering objects. When not specified,
   * Transfer Service will attempt to determine the right API to use.
   *
   * Accepted values: LIST_API_UNSPECIFIED, LIST_OBJECTS_V2, LIST_OBJECTS
   *
   * @param self::LIST_API_* $listApi
   */
  public function setListApi($listApi)
  {
    $this->listApi = $listApi;
  }
  /**
   * @return self::LIST_API_*
   */
  public function getListApi()
  {
    return $this->listApi;
  }
  /**
   * Specifies the network protocol of the agent. When not specified, the
   * default value of NetworkProtocol NETWORK_PROTOCOL_HTTPS is used.
   *
   * Accepted values: NETWORK_PROTOCOL_UNSPECIFIED, NETWORK_PROTOCOL_HTTPS,
   * NETWORK_PROTOCOL_HTTP
   *
   * @param self::PROTOCOL_* $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return self::PROTOCOL_*
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Specifies the API request model used to call the storage service. When not
   * specified, the default value of RequestModel
   * REQUEST_MODEL_VIRTUAL_HOSTED_STYLE is used.
   *
   * Accepted values: REQUEST_MODEL_UNSPECIFIED,
   * REQUEST_MODEL_VIRTUAL_HOSTED_STYLE, REQUEST_MODEL_PATH_STYLE
   *
   * @param self::REQUEST_MODEL_* $requestModel
   */
  public function setRequestModel($requestModel)
  {
    $this->requestModel = $requestModel;
  }
  /**
   * @return self::REQUEST_MODEL_*
   */
  public function getRequestModel()
  {
    return $this->requestModel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(S3CompatibleMetadata::class, 'Google_Service_Storagetransfer_S3CompatibleMetadata');
