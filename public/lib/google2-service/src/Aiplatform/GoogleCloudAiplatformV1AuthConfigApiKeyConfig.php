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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1AuthConfigApiKeyConfig extends \Google\Model
{
  public const HTTP_ELEMENT_LOCATION_HTTP_IN_UNSPECIFIED = 'HTTP_IN_UNSPECIFIED';
  /**
   * Element is in the HTTP request query.
   */
  public const HTTP_ELEMENT_LOCATION_HTTP_IN_QUERY = 'HTTP_IN_QUERY';
  /**
   * Element is in the HTTP request header.
   */
  public const HTTP_ELEMENT_LOCATION_HTTP_IN_HEADER = 'HTTP_IN_HEADER';
  /**
   * Element is in the HTTP request path.
   */
  public const HTTP_ELEMENT_LOCATION_HTTP_IN_PATH = 'HTTP_IN_PATH';
  /**
   * Element is in the HTTP request body.
   */
  public const HTTP_ELEMENT_LOCATION_HTTP_IN_BODY = 'HTTP_IN_BODY';
  /**
   * Element is in the HTTP request cookie.
   */
  public const HTTP_ELEMENT_LOCATION_HTTP_IN_COOKIE = 'HTTP_IN_COOKIE';
  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * API key. Format: `projects/{project}/secrets/{secrete}/versions/{version}`
   * - If both `api_key_secret` and `api_key_string` are specified, this field
   * takes precedence over `api_key_string`. - If specified, the
   * `secretmanager.versions.access` permission should be granted to Vertex AI
   * Extension Service Agent (https://cloud.google.com/vertex-
   * ai/docs/general/access-control#service-agents) on the specified resource.
   *
   * @var string
   */
  public $apiKeySecret;
  /**
   * Optional. The API key to be used in the request directly.
   *
   * @var string
   */
  public $apiKeyString;
  /**
   * Optional. The location of the API key.
   *
   * @var string
   */
  public $httpElementLocation;
  /**
   * Optional. The parameter name of the API key. E.g. If the API request is
   * "https://example.com/act?api_key=", "api_key" would be the parameter name.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * API key. Format: `projects/{project}/secrets/{secrete}/versions/{version}`
   * - If both `api_key_secret` and `api_key_string` are specified, this field
   * takes precedence over `api_key_string`. - If specified, the
   * `secretmanager.versions.access` permission should be granted to Vertex AI
   * Extension Service Agent (https://cloud.google.com/vertex-
   * ai/docs/general/access-control#service-agents) on the specified resource.
   *
   * @param string $apiKeySecret
   */
  public function setApiKeySecret($apiKeySecret)
  {
    $this->apiKeySecret = $apiKeySecret;
  }
  /**
   * @return string
   */
  public function getApiKeySecret()
  {
    return $this->apiKeySecret;
  }
  /**
   * Optional. The API key to be used in the request directly.
   *
   * @param string $apiKeyString
   */
  public function setApiKeyString($apiKeyString)
  {
    $this->apiKeyString = $apiKeyString;
  }
  /**
   * @return string
   */
  public function getApiKeyString()
  {
    return $this->apiKeyString;
  }
  /**
   * Optional. The location of the API key.
   *
   * Accepted values: HTTP_IN_UNSPECIFIED, HTTP_IN_QUERY, HTTP_IN_HEADER,
   * HTTP_IN_PATH, HTTP_IN_BODY, HTTP_IN_COOKIE
   *
   * @param self::HTTP_ELEMENT_LOCATION_* $httpElementLocation
   */
  public function setHttpElementLocation($httpElementLocation)
  {
    $this->httpElementLocation = $httpElementLocation;
  }
  /**
   * @return self::HTTP_ELEMENT_LOCATION_*
   */
  public function getHttpElementLocation()
  {
    return $this->httpElementLocation;
  }
  /**
   * Optional. The parameter name of the API key. E.g. If the API request is
   * "https://example.com/act?api_key=", "api_key" would be the parameter name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1AuthConfigApiKeyConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1AuthConfigApiKeyConfig');
