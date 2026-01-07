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

namespace Google\Service\ServiceNetworking;

class AuthProvider extends \Google\Collection
{
  protected $collection_key = 'jwtLocations';
  /**
   * The list of JWT [audiences](https://tools.ietf.org/html/draft-ietf-oauth-
   * json-web-token-32#section-4.1.3). that are allowed to access. A JWT
   * containing any of these audiences will be accepted. When this setting is
   * absent, JWTs with audiences: -
   * "https://[service.name]/[google.protobuf.Api.name]" -
   * "https://[service.name]/" will be accepted. For example, if no audiences
   * are in the setting, LibraryService API will accept JWTs with the following
   * audiences: - https://library-
   * example.googleapis.com/google.example.library.v1.LibraryService -
   * https://library-example.googleapis.com/ Example: audiences:
   * bookstore_android.apps.googleusercontent.com,
   * bookstore_web.apps.googleusercontent.com
   *
   * @var string
   */
  public $audiences;
  /**
   * Redirect URL if JWT token is required but not present or is expired.
   * Implement authorizationUrl of securityDefinitions in OpenAPI spec.
   *
   * @var string
   */
  public $authorizationUrl;
  /**
   * The unique identifier of the auth provider. It will be referred to by
   * `AuthRequirement.provider_id`. Example: "bookstore_auth".
   *
   * @var string
   */
  public $id;
  /**
   * Identifies the principal that issued the JWT. See
   * https://tools.ietf.org/html/draft-ietf-oauth-json-web-
   * token-32#section-4.1.1 Usually a URL or an email address. Example:
   * https://securetoken.google.com Example:
   * 1234567-compute@developer.gserviceaccount.com
   *
   * @var string
   */
  public $issuer;
  /**
   * URL of the provider's public key set to validate signature of the JWT. See
   * [OpenID Discovery](https://openid.net/specs/openid-connect-
   * discovery-1_0.html#ProviderMetadata). Optional if the key set document: -
   * can be retrieved from [OpenID Discovery](https://openid.net/specs/openid-
   * connect-discovery-1_0.html) of the issuer. - can be inferred from the email
   * domain of the issuer (e.g. a Google service account). Example:
   * https://www.googleapis.com/oauth2/v1/certs
   *
   * @var string
   */
  public $jwksUri;
  protected $jwtLocationsType = JwtLocation::class;
  protected $jwtLocationsDataType = 'array';

  /**
   * The list of JWT [audiences](https://tools.ietf.org/html/draft-ietf-oauth-
   * json-web-token-32#section-4.1.3). that are allowed to access. A JWT
   * containing any of these audiences will be accepted. When this setting is
   * absent, JWTs with audiences: -
   * "https://[service.name]/[google.protobuf.Api.name]" -
   * "https://[service.name]/" will be accepted. For example, if no audiences
   * are in the setting, LibraryService API will accept JWTs with the following
   * audiences: - https://library-
   * example.googleapis.com/google.example.library.v1.LibraryService -
   * https://library-example.googleapis.com/ Example: audiences:
   * bookstore_android.apps.googleusercontent.com,
   * bookstore_web.apps.googleusercontent.com
   *
   * @param string $audiences
   */
  public function setAudiences($audiences)
  {
    $this->audiences = $audiences;
  }
  /**
   * @return string
   */
  public function getAudiences()
  {
    return $this->audiences;
  }
  /**
   * Redirect URL if JWT token is required but not present or is expired.
   * Implement authorizationUrl of securityDefinitions in OpenAPI spec.
   *
   * @param string $authorizationUrl
   */
  public function setAuthorizationUrl($authorizationUrl)
  {
    $this->authorizationUrl = $authorizationUrl;
  }
  /**
   * @return string
   */
  public function getAuthorizationUrl()
  {
    return $this->authorizationUrl;
  }
  /**
   * The unique identifier of the auth provider. It will be referred to by
   * `AuthRequirement.provider_id`. Example: "bookstore_auth".
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies the principal that issued the JWT. See
   * https://tools.ietf.org/html/draft-ietf-oauth-json-web-
   * token-32#section-4.1.1 Usually a URL or an email address. Example:
   * https://securetoken.google.com Example:
   * 1234567-compute@developer.gserviceaccount.com
   *
   * @param string $issuer
   */
  public function setIssuer($issuer)
  {
    $this->issuer = $issuer;
  }
  /**
   * @return string
   */
  public function getIssuer()
  {
    return $this->issuer;
  }
  /**
   * URL of the provider's public key set to validate signature of the JWT. See
   * [OpenID Discovery](https://openid.net/specs/openid-connect-
   * discovery-1_0.html#ProviderMetadata). Optional if the key set document: -
   * can be retrieved from [OpenID Discovery](https://openid.net/specs/openid-
   * connect-discovery-1_0.html) of the issuer. - can be inferred from the email
   * domain of the issuer (e.g. a Google service account). Example:
   * https://www.googleapis.com/oauth2/v1/certs
   *
   * @param string $jwksUri
   */
  public function setJwksUri($jwksUri)
  {
    $this->jwksUri = $jwksUri;
  }
  /**
   * @return string
   */
  public function getJwksUri()
  {
    return $this->jwksUri;
  }
  /**
   * Defines the locations to extract the JWT. For now it is only used by the
   * Cloud Endpoints to store the OpenAPI extension [x-google-jwt-locations]
   * (https://cloud.google.com/endpoints/docs/openapi/openapi-
   * extensions#x-google-jwt-locations) JWT locations can be one of HTTP
   * headers, URL query parameters or cookies. The rule is that the first match
   * wins. If not specified, default to use following 3 locations: 1)
   * Authorization: Bearer 2) x-goog-iap-jwt-assertion 3) access_token query
   * parameter Default locations can be specified as followings: jwt_locations:
   * - header: Authorization value_prefix: "Bearer " - header: x-goog-iap-jwt-
   * assertion - query: access_token
   *
   * @param JwtLocation[] $jwtLocations
   */
  public function setJwtLocations($jwtLocations)
  {
    $this->jwtLocations = $jwtLocations;
  }
  /**
   * @return JwtLocation[]
   */
  public function getJwtLocations()
  {
    return $this->jwtLocations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthProvider::class, 'Google_Service_ServiceNetworking_AuthProvider');
