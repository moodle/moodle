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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ToolAuthenticationApiKeyConfig extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const REQUEST_LOCATION_REQUEST_LOCATION_UNSPECIFIED = 'REQUEST_LOCATION_UNSPECIFIED';
  /**
   * Represents the key in http header.
   */
  public const REQUEST_LOCATION_HEADER = 'HEADER';
  /**
   * Represents the key in query string.
   */
  public const REQUEST_LOCATION_QUERY_STRING = 'QUERY_STRING';
  /**
   * Optional. The API key. If the `secret_version_for_api_key` field is set,
   * this field will be ignored.
   *
   * @var string
   */
  public $apiKey;
  /**
   * Required. The parameter name or the header name of the API key. E.g., If
   * the API request is "https://example.com/act?X-Api-Key=", "X-Api-Key" would
   * be the parameter name.
   *
   * @var string
   */
  public $keyName;
  /**
   * Required. Key location in the request.
   *
   * @var string
   */
  public $requestLocation;
  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * API key. If this field is set, the `api_key` field will be ignored. Format:
   * `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @var string
   */
  public $secretVersionForApiKey;

  /**
   * Optional. The API key. If the `secret_version_for_api_key` field is set,
   * this field will be ignored.
   *
   * @param string $apiKey
   */
  public function setApiKey($apiKey)
  {
    $this->apiKey = $apiKey;
  }
  /**
   * @return string
   */
  public function getApiKey()
  {
    return $this->apiKey;
  }
  /**
   * Required. The parameter name or the header name of the API key. E.g., If
   * the API request is "https://example.com/act?X-Api-Key=", "X-Api-Key" would
   * be the parameter name.
   *
   * @param string $keyName
   */
  public function setKeyName($keyName)
  {
    $this->keyName = $keyName;
  }
  /**
   * @return string
   */
  public function getKeyName()
  {
    return $this->keyName;
  }
  /**
   * Required. Key location in the request.
   *
   * Accepted values: REQUEST_LOCATION_UNSPECIFIED, HEADER, QUERY_STRING
   *
   * @param self::REQUEST_LOCATION_* $requestLocation
   */
  public function setRequestLocation($requestLocation)
  {
    $this->requestLocation = $requestLocation;
  }
  /**
   * @return self::REQUEST_LOCATION_*
   */
  public function getRequestLocation()
  {
    return $this->requestLocation;
  }
  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * API key. If this field is set, the `api_key` field will be ignored. Format:
   * `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @param string $secretVersionForApiKey
   */
  public function setSecretVersionForApiKey($secretVersionForApiKey)
  {
    $this->secretVersionForApiKey = $secretVersionForApiKey;
  }
  /**
   * @return string
   */
  public function getSecretVersionForApiKey()
  {
    return $this->secretVersionForApiKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ToolAuthenticationApiKeyConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolAuthenticationApiKeyConfig');
