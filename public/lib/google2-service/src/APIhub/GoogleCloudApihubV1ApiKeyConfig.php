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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ApiKeyConfig extends \Google\Model
{
  /**
   * HTTP element location not specified.
   */
  public const HTTP_ELEMENT_LOCATION_HTTP_ELEMENT_LOCATION_UNSPECIFIED = 'HTTP_ELEMENT_LOCATION_UNSPECIFIED';
  /**
   * Element is in the HTTP request query.
   */
  public const HTTP_ELEMENT_LOCATION_QUERY = 'QUERY';
  /**
   * Element is in the HTTP request header.
   */
  public const HTTP_ELEMENT_LOCATION_HEADER = 'HEADER';
  /**
   * Element is in the HTTP request path.
   */
  public const HTTP_ELEMENT_LOCATION_PATH = 'PATH';
  /**
   * Element is in the HTTP request body.
   */
  public const HTTP_ELEMENT_LOCATION_BODY = 'BODY';
  /**
   * Element is in the HTTP request cookie.
   */
  public const HTTP_ELEMENT_LOCATION_COOKIE = 'COOKIE';
  protected $apiKeyType = GoogleCloudApihubV1Secret::class;
  protected $apiKeyDataType = '';
  /**
   * Required. The location of the API key. The default value is QUERY.
   *
   * @var string
   */
  public $httpElementLocation;
  /**
   * Required. The parameter name of the API key. E.g. If the API request is
   * "https://example.com/act?api_key=", "api_key" would be the parameter name.
   *
   * @var string
   */
  public $name;

  /**
   * Required. The name of the SecretManager secret version resource storing the
   * API key. Format: `projects/{project}/secrets/{secrete}/versions/{version}`.
   * The `secretmanager.versions.access` permission should be granted to the
   * service account accessing the secret.
   *
   * @param GoogleCloudApihubV1Secret $apiKey
   */
  public function setApiKey(GoogleCloudApihubV1Secret $apiKey)
  {
    $this->apiKey = $apiKey;
  }
  /**
   * @return GoogleCloudApihubV1Secret
   */
  public function getApiKey()
  {
    return $this->apiKey;
  }
  /**
   * Required. The location of the API key. The default value is QUERY.
   *
   * Accepted values: HTTP_ELEMENT_LOCATION_UNSPECIFIED, QUERY, HEADER, PATH,
   * BODY, COOKIE
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
   * Required. The parameter name of the API key. E.g. If the API request is
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
class_alias(GoogleCloudApihubV1ApiKeyConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1ApiKeyConfig');
