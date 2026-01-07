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

namespace Google\Service\Container;

class GetOpenIDConfigResponse extends \Google\Collection
{
  protected $collection_key = 'subject_types_supported';
  protected $internal_gapi_mappings = [
        "claimsSupported" => "claims_supported",
        "grantTypes" => "grant_types",
        "idTokenSigningAlgValuesSupported" => "id_token_signing_alg_values_supported",
        "jwksUri" => "jwks_uri",
        "responseTypesSupported" => "response_types_supported",
        "subjectTypesSupported" => "subject_types_supported",
  ];
  protected $cacheHeaderType = HttpCacheControlResponseHeader::class;
  protected $cacheHeaderDataType = '';
  /**
   * Supported claims.
   *
   * @var string[]
   */
  public $claimsSupported;
  /**
   * Supported grant types.
   *
   * @var string[]
   */
  public $grantTypes;
  /**
   * supported ID Token signing Algorithms.
   *
   * @var string[]
   */
  public $idTokenSigningAlgValuesSupported;
  /**
   * OIDC Issuer.
   *
   * @var string
   */
  public $issuer;
  /**
   * JSON Web Key uri.
   *
   * @var string
   */
  public $jwksUri;
  /**
   * Supported response types.
   *
   * @var string[]
   */
  public $responseTypesSupported;
  /**
   * Supported subject types.
   *
   * @var string[]
   */
  public $subjectTypesSupported;

  /**
   * For HTTP requests, this field is automatically extracted into the Cache-
   * Control HTTP header.
   *
   * @param HttpCacheControlResponseHeader $cacheHeader
   */
  public function setCacheHeader(HttpCacheControlResponseHeader $cacheHeader)
  {
    $this->cacheHeader = $cacheHeader;
  }
  /**
   * @return HttpCacheControlResponseHeader
   */
  public function getCacheHeader()
  {
    return $this->cacheHeader;
  }
  /**
   * Supported claims.
   *
   * @param string[] $claimsSupported
   */
  public function setClaimsSupported($claimsSupported)
  {
    $this->claimsSupported = $claimsSupported;
  }
  /**
   * @return string[]
   */
  public function getClaimsSupported()
  {
    return $this->claimsSupported;
  }
  /**
   * Supported grant types.
   *
   * @param string[] $grantTypes
   */
  public function setGrantTypes($grantTypes)
  {
    $this->grantTypes = $grantTypes;
  }
  /**
   * @return string[]
   */
  public function getGrantTypes()
  {
    return $this->grantTypes;
  }
  /**
   * supported ID Token signing Algorithms.
   *
   * @param string[] $idTokenSigningAlgValuesSupported
   */
  public function setIdTokenSigningAlgValuesSupported($idTokenSigningAlgValuesSupported)
  {
    $this->idTokenSigningAlgValuesSupported = $idTokenSigningAlgValuesSupported;
  }
  /**
   * @return string[]
   */
  public function getIdTokenSigningAlgValuesSupported()
  {
    return $this->idTokenSigningAlgValuesSupported;
  }
  /**
   * OIDC Issuer.
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
   * JSON Web Key uri.
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
   * Supported response types.
   *
   * @param string[] $responseTypesSupported
   */
  public function setResponseTypesSupported($responseTypesSupported)
  {
    $this->responseTypesSupported = $responseTypesSupported;
  }
  /**
   * @return string[]
   */
  public function getResponseTypesSupported()
  {
    return $this->responseTypesSupported;
  }
  /**
   * Supported subject types.
   *
   * @param string[] $subjectTypesSupported
   */
  public function setSubjectTypesSupported($subjectTypesSupported)
  {
    $this->subjectTypesSupported = $subjectTypesSupported;
  }
  /**
   * @return string[]
   */
  public function getSubjectTypesSupported()
  {
    return $this->subjectTypesSupported;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetOpenIDConfigResponse::class, 'Google_Service_Container_GetOpenIDConfigResponse');
