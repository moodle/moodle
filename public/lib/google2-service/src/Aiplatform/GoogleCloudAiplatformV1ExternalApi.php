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

class GoogleCloudAiplatformV1ExternalApi extends \Google\Model
{
  /**
   * Unspecified API spec. This value should not be used.
   */
  public const API_SPEC_API_SPEC_UNSPECIFIED = 'API_SPEC_UNSPECIFIED';
  /**
   * Simple search API spec.
   */
  public const API_SPEC_SIMPLE_SEARCH = 'SIMPLE_SEARCH';
  /**
   * Elastic search API spec.
   */
  public const API_SPEC_ELASTIC_SEARCH = 'ELASTIC_SEARCH';
  protected $apiAuthType = GoogleCloudAiplatformV1ApiAuth::class;
  protected $apiAuthDataType = '';
  /**
   * The API spec that the external API implements.
   *
   * @var string
   */
  public $apiSpec;
  protected $authConfigType = GoogleCloudAiplatformV1AuthConfig::class;
  protected $authConfigDataType = '';
  protected $elasticSearchParamsType = GoogleCloudAiplatformV1ExternalApiElasticSearchParams::class;
  protected $elasticSearchParamsDataType = '';
  /**
   * The endpoint of the external API. The system will call the API at this
   * endpoint to retrieve the data for grounding. Example:
   * https://acme.com:443/search
   *
   * @var string
   */
  public $endpoint;
  protected $simpleSearchParamsType = GoogleCloudAiplatformV1ExternalApiSimpleSearchParams::class;
  protected $simpleSearchParamsDataType = '';

  /**
   * The authentication config to access the API. Deprecated. Please use
   * auth_config instead.
   *
   * @deprecated
   * @param GoogleCloudAiplatformV1ApiAuth $apiAuth
   */
  public function setApiAuth(GoogleCloudAiplatformV1ApiAuth $apiAuth)
  {
    $this->apiAuth = $apiAuth;
  }
  /**
   * @deprecated
   * @return GoogleCloudAiplatformV1ApiAuth
   */
  public function getApiAuth()
  {
    return $this->apiAuth;
  }
  /**
   * The API spec that the external API implements.
   *
   * Accepted values: API_SPEC_UNSPECIFIED, SIMPLE_SEARCH, ELASTIC_SEARCH
   *
   * @param self::API_SPEC_* $apiSpec
   */
  public function setApiSpec($apiSpec)
  {
    $this->apiSpec = $apiSpec;
  }
  /**
   * @return self::API_SPEC_*
   */
  public function getApiSpec()
  {
    return $this->apiSpec;
  }
  /**
   * The authentication config to access the API.
   *
   * @param GoogleCloudAiplatformV1AuthConfig $authConfig
   */
  public function setAuthConfig(GoogleCloudAiplatformV1AuthConfig $authConfig)
  {
    $this->authConfig = $authConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1AuthConfig
   */
  public function getAuthConfig()
  {
    return $this->authConfig;
  }
  /**
   * Parameters for the elastic search API.
   *
   * @param GoogleCloudAiplatformV1ExternalApiElasticSearchParams $elasticSearchParams
   */
  public function setElasticSearchParams(GoogleCloudAiplatformV1ExternalApiElasticSearchParams $elasticSearchParams)
  {
    $this->elasticSearchParams = $elasticSearchParams;
  }
  /**
   * @return GoogleCloudAiplatformV1ExternalApiElasticSearchParams
   */
  public function getElasticSearchParams()
  {
    return $this->elasticSearchParams;
  }
  /**
   * The endpoint of the external API. The system will call the API at this
   * endpoint to retrieve the data for grounding. Example:
   * https://acme.com:443/search
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Parameters for the simple search API.
   *
   * @param GoogleCloudAiplatformV1ExternalApiSimpleSearchParams $simpleSearchParams
   */
  public function setSimpleSearchParams(GoogleCloudAiplatformV1ExternalApiSimpleSearchParams $simpleSearchParams)
  {
    $this->simpleSearchParams = $simpleSearchParams;
  }
  /**
   * @return GoogleCloudAiplatformV1ExternalApiSimpleSearchParams
   */
  public function getSimpleSearchParams()
  {
    return $this->simpleSearchParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExternalApi::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExternalApi');
