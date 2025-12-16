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

namespace Google\Service\WorkspaceEvents;

class SecurityScheme extends \Google\Model
{
  protected $apiKeySecuritySchemeType = APIKeySecurityScheme::class;
  protected $apiKeySecuritySchemeDataType = '';
  protected $httpAuthSecuritySchemeType = HTTPAuthSecurityScheme::class;
  protected $httpAuthSecuritySchemeDataType = '';
  protected $mtlsSecuritySchemeType = MutualTlsSecurityScheme::class;
  protected $mtlsSecuritySchemeDataType = '';
  protected $oauth2SecuritySchemeType = OAuth2SecurityScheme::class;
  protected $oauth2SecuritySchemeDataType = '';
  protected $openIdConnectSecuritySchemeType = OpenIdConnectSecurityScheme::class;
  protected $openIdConnectSecuritySchemeDataType = '';

  /**
   * @param APIKeySecurityScheme $apiKeySecurityScheme
   */
  public function setApiKeySecurityScheme(APIKeySecurityScheme $apiKeySecurityScheme)
  {
    $this->apiKeySecurityScheme = $apiKeySecurityScheme;
  }
  /**
   * @return APIKeySecurityScheme
   */
  public function getApiKeySecurityScheme()
  {
    return $this->apiKeySecurityScheme;
  }
  /**
   * @param HTTPAuthSecurityScheme $httpAuthSecurityScheme
   */
  public function setHttpAuthSecurityScheme(HTTPAuthSecurityScheme $httpAuthSecurityScheme)
  {
    $this->httpAuthSecurityScheme = $httpAuthSecurityScheme;
  }
  /**
   * @return HTTPAuthSecurityScheme
   */
  public function getHttpAuthSecurityScheme()
  {
    return $this->httpAuthSecurityScheme;
  }
  /**
   * @param MutualTlsSecurityScheme $mtlsSecurityScheme
   */
  public function setMtlsSecurityScheme(MutualTlsSecurityScheme $mtlsSecurityScheme)
  {
    $this->mtlsSecurityScheme = $mtlsSecurityScheme;
  }
  /**
   * @return MutualTlsSecurityScheme
   */
  public function getMtlsSecurityScheme()
  {
    return $this->mtlsSecurityScheme;
  }
  /**
   * @param OAuth2SecurityScheme $oauth2SecurityScheme
   */
  public function setOauth2SecurityScheme(OAuth2SecurityScheme $oauth2SecurityScheme)
  {
    $this->oauth2SecurityScheme = $oauth2SecurityScheme;
  }
  /**
   * @return OAuth2SecurityScheme
   */
  public function getOauth2SecurityScheme()
  {
    return $this->oauth2SecurityScheme;
  }
  /**
   * @param OpenIdConnectSecurityScheme $openIdConnectSecurityScheme
   */
  public function setOpenIdConnectSecurityScheme(OpenIdConnectSecurityScheme $openIdConnectSecurityScheme)
  {
    $this->openIdConnectSecurityScheme = $openIdConnectSecurityScheme;
  }
  /**
   * @return OpenIdConnectSecurityScheme
   */
  public function getOpenIdConnectSecurityScheme()
  {
    return $this->openIdConnectSecurityScheme;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityScheme::class, 'Google_Service_WorkspaceEvents_SecurityScheme');
