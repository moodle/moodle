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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaOAuth2ClientCredentials extends \Google\Model
{
  /**
   * Unspecified request type
   */
  public const REQUEST_TYPE_REQUEST_TYPE_UNSPECIFIED = 'REQUEST_TYPE_UNSPECIFIED';
  /**
   * To pass all the parameters in post body.
   */
  public const REQUEST_TYPE_REQUEST_BODY = 'REQUEST_BODY';
  /**
   * To pass all the parameters as a part of query parameter.
   */
  public const REQUEST_TYPE_QUERY_PARAMETERS = 'QUERY_PARAMETERS';
  /**
   * To pass client id and client secret as base 64 encoding of
   * client_id:client_password and rest parameters in post body.
   */
  public const REQUEST_TYPE_ENCODED_HEADER = 'ENCODED_HEADER';
  protected $accessTokenType = GoogleCloudIntegrationsV1alphaAccessToken::class;
  protected $accessTokenDataType = '';
  /**
   * The client's ID.
   *
   * @var string
   */
  public $clientId;
  /**
   * The client's secret.
   *
   * @var string
   */
  public $clientSecret;
  /**
   * Represent how to pass parameters to fetch access token
   *
   * @var string
   */
  public $requestType;
  /**
   * A space-delimited list of requested scope permissions.
   *
   * @var string
   */
  public $scope;
  /**
   * The token endpoint is used by the client to obtain an access token by
   * presenting its authorization grant or refresh token.
   *
   * @var string
   */
  public $tokenEndpoint;
  protected $tokenParamsType = GoogleCloudIntegrationsV1alphaParameterMap::class;
  protected $tokenParamsDataType = '';

  /**
   * Access token fetched from the authorization server.
   *
   * @param GoogleCloudIntegrationsV1alphaAccessToken $accessToken
   */
  public function setAccessToken(GoogleCloudIntegrationsV1alphaAccessToken $accessToken)
  {
    $this->accessToken = $accessToken;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaAccessToken
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }
  /**
   * The client's ID.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * The client's secret.
   *
   * @param string $clientSecret
   */
  public function setClientSecret($clientSecret)
  {
    $this->clientSecret = $clientSecret;
  }
  /**
   * @return string
   */
  public function getClientSecret()
  {
    return $this->clientSecret;
  }
  /**
   * Represent how to pass parameters to fetch access token
   *
   * Accepted values: REQUEST_TYPE_UNSPECIFIED, REQUEST_BODY, QUERY_PARAMETERS,
   * ENCODED_HEADER
   *
   * @param self::REQUEST_TYPE_* $requestType
   */
  public function setRequestType($requestType)
  {
    $this->requestType = $requestType;
  }
  /**
   * @return self::REQUEST_TYPE_*
   */
  public function getRequestType()
  {
    return $this->requestType;
  }
  /**
   * A space-delimited list of requested scope permissions.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * The token endpoint is used by the client to obtain an access token by
   * presenting its authorization grant or refresh token.
   *
   * @param string $tokenEndpoint
   */
  public function setTokenEndpoint($tokenEndpoint)
  {
    $this->tokenEndpoint = $tokenEndpoint;
  }
  /**
   * @return string
   */
  public function getTokenEndpoint()
  {
    return $this->tokenEndpoint;
  }
  /**
   * Token parameters for the auth request.
   *
   * @param GoogleCloudIntegrationsV1alphaParameterMap $tokenParams
   */
  public function setTokenParams(GoogleCloudIntegrationsV1alphaParameterMap $tokenParams)
  {
    $this->tokenParams = $tokenParams;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaParameterMap
   */
  public function getTokenParams()
  {
    return $this->tokenParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaOAuth2ClientCredentials::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaOAuth2ClientCredentials');
