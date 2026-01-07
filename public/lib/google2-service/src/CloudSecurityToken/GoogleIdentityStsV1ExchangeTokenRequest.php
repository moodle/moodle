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

namespace Google\Service\CloudSecurityToken;

class GoogleIdentityStsV1ExchangeTokenRequest extends \Google\Model
{
  /**
   * The full resource name of the identity provider; for example: `//iam.google
   * apis.com/projects//locations/global/workloadIdentityPools//providers/` for
   * workload identity pool providers, or
   * `//iam.googleapis.com/locations/global/workforcePools//providers/` for
   * workforce pool providers. Required when exchanging an external credential
   * for a Google access token.
   *
   * @var string
   */
  public $audience;
  /**
   * Required. The grant type. Must be `urn:ietf:params:oauth:grant-type:token-
   * exchange`, which indicates a token exchange.
   *
   * @var string
   */
  public $grantType;
  /**
   * A set of features that Security Token Service supports, in addition to the
   * standard OAuth 2.0 token exchange, formatted as a serialized JSON object of
   * Options. The size of the parameter value must not exceed 4 * 1024 * 1024
   * characters (4 MB).
   *
   * @var string
   */
  public $options;
  /**
   * Required. An identifier for the type of requested security token. Can be
   * `urn:ietf:params:oauth:token-type:access_token` or
   * `urn:ietf:params:oauth:token-type:access_boundary_intermediary_token`.
   *
   * @var string
   */
  public $requestedTokenType;
  /**
   * The OAuth 2.0 scopes to include on the resulting access token, formatted as
   * a list of space-delimited, case-sensitive strings; for example,
   * `https://www.googleapis.com/auth/cloud-platform`. Required when exchanging
   * an external credential for a Google access token. For a list of OAuth 2.0
   * scopes, see [OAuth 2.0 Scopes for Google
   * APIs](https://developers.google.com/identity/protocols/oauth2/scopes).
   *
   * @var string
   */
  public $scope;
  /**
   * Required. The input token. This token is either an external credential
   * issued by a workload identity pool provider, or a short-lived access token
   * issued by Google. If the token is an OIDC JWT, it must use the JWT format
   * defined in [RFC 7523](https://tools.ietf.org/html/rfc7523), and the
   * `subject_token_type` must be either `urn:ietf:params:oauth:token-type:jwt`
   * or `urn:ietf:params:oauth:token-type:id_token`. The following headers are
   * required: - `kid`: The identifier of the signing key securing the JWT. -
   * `alg`: The cryptographic algorithm securing the JWT. Must be `RS256` or
   * `ES256`. The following payload fields are required. For more information,
   * see [RFC 7523, Section 3](https://tools.ietf.org/html/rfc7523#section-3): -
   * `iss`: The issuer of the token. The issuer must provide a discovery
   * document at the URL `/.well-known/openid-configuration`, where `` is the
   * value of this field. The document must be formatted according to section
   * 4.2 of the [OIDC 1.0 Discovery
   * specification](https://openid.net/specs/openid-connect-
   * discovery-1_0.html#ProviderConfigurationResponse). - `iat`: The issue time,
   * in seconds, since the Unix epoch. Must be in the past. - `exp`: The
   * expiration time, in seconds, since the Unix epoch. Must be less than 48
   * hours after `iat`. Shorter expiration times are more secure. If possible,
   * we recommend setting an expiration time less than 6 hours. - `sub`: The
   * identity asserted in the JWT. - `aud`: For workload identity pools, this
   * must be a value specified in the allowed audiences for the workload
   * identity pool provider, or one of the audiences allowed by default if no
   * audiences were specified. See https://cloud.google.com/iam/docs/reference/r
   * est/v1/projects.locations.workloadIdentityPools.providers#oidc. For
   * workforce pools, this must match the client ID specified in the provider
   * configuration. See https://cloud.google.com/iam/docs/reference/rest/v1/loca
   * tions.workforcePools.providers#oidc. Example header: ``` { "alg": "RS256",
   * "kid": "us-east-11" } ``` Example payload: ``` { "iss":
   * "https://accounts.google.com", "iat": 1517963104, "exp": 1517966704, "aud":
   * "//iam.googleapis.com/projects/1234567890123/locations/global/workloadIdent
   * ityPools/my-pool/providers/my-provider", "sub": "113475438248934895348",
   * "my_claims": { "additional_claim": "value" } } ``` If `subject_token` is
   * for AWS, it must be a serialized `GetCallerIdentity` token. This token
   * contains the same information as a request to the AWS [`GetCallerIdentity()
   * `](https://docs.aws.amazon.com/STS/latest/APIReference/API_GetCallerIdentit
   * y) method, as well as the AWS [signature](https://docs.aws.amazon.com/gener
   * al/latest/gr/signing_aws_api_requests.html) for the request information.
   * Use Signature Version 4. Format the request as URL-encoded JSON, and set
   * the `subject_token_type` parameter to `urn:ietf:params:aws:token-
   * type:aws4_request`. The following parameters are required: - `url`: The URL
   * of the AWS STS endpoint for `GetCallerIdentity()`, such as
   * `https://sts.amazonaws.com?Action=GetCallerIdentity&Version=2011-06-15`.
   * Regional endpoints are also supported. - `method`: The HTTP request method:
   * `POST`. - `headers`: The HTTP request headers, which must include: -
   * `Authorization`: The request signature. - `x-amz-date`: The time you will
   * send the request, formatted as an [ISO8601 Basic](https://docs.aws.amazon.c
   * om/general/latest/gr/sigv4_elements.html#sigv4_elements_date) string. This
   * value is typically set to the current time and is used to help prevent
   * replay attacks. - `host`: The hostname of the `url` field; for example,
   * `sts.amazonaws.com`. - `x-goog-cloud-target-resource`: The full, canonical
   * resource name of the workload identity pool provider, with or without an
   * `https:` prefix. To help ensure data integrity, we recommend including this
   * header in the `SignedHeaders` field of the signed request. For example: //i
   * am.googleapis.com/projects//locations/global/workloadIdentityPools//provide
   * rs/ https://iam.googleapis.com/projects//locations/global/workloadIdentityP
   * ools//providers/ If you are using temporary security credentials provided
   * by AWS, you must also include the header `x-amz-security-token`, with the
   * value set to the session token. The following example shows a
   * `GetCallerIdentity` token: ``` { "headers": [ {"key": "x-amz-date",
   * "value": "20200815T015049Z"}, {"key": "Authorization", "value": "AWS4-HMAC-
   * SHA256+Credential=$credential,+SignedHeaders=host;x-amz-date;x-goog-cloud-
   * target-resource,+Signature=$signature"}, {"key": "x-goog-cloud-target-
   * resource", "value": "//iam.googleapis.com/projects//locations/global/worklo
   * adIdentityPools//providers/"}, {"key": "host", "value":
   * "sts.amazonaws.com"} . ], "method": "POST", "url":
   * "https://sts.amazonaws.com?Action=GetCallerIdentity&Version=2011-06-15" }
   * ``` If the token is a SAML 2.0 assertion, it must use the format defined in
   * [the SAML 2.0 spec](https://docs.oasis-open.org/security/saml/Post2.0/sstc-
   * saml-tech-overview-2.0-cd-02.pdf), and the `subject_token_type` must be
   * `urn:ietf:params:oauth:token-type:saml2`. See [Verification of external
   * credentials](https://cloud.google.com/iam/docs/using-workload-identity-
   * federation#verification_of_external_credentials) for details on how SAML
   * 2.0 assertions are validated during token exchanges. You can also use a
   * Google-issued OAuth 2.0 access token with this field to obtain an access
   * token with new security attributes applied, such as a Credential Access
   * Boundary. In this case, set `subject_token_type` to
   * `urn:ietf:params:oauth:token-type:access_token`. If an access token already
   * contains security attributes, you cannot apply additional security
   * attributes. If the request is for X.509 certificate-based authentication,
   * the `subject_token` must be a JSON-formatted list of X.509 certificates in
   * DER format, as defined in [RFC 7515](https://www.rfc-
   * editor.org/rfc/rfc7515#section-4.1.6). `subject_token_type` must be
   * `urn:ietf:params:oauth:token-type:mtls`. The following example shows a
   * JSON-formatted list of X.509 certificate in DER format: ```
   * [\"MIIEYDCCA0i...\", \"MCIFFGAGTT0...\"] ```
   *
   * @var string
   */
  public $subjectToken;
  /**
   * Required. An identifier that indicates the type of the security token in
   * the `subject_token` parameter. Supported values are
   * `urn:ietf:params:oauth:token-type:jwt`, `urn:ietf:params:oauth:token-
   * type:id_token`, `urn:ietf:params:aws:token-type:aws4_request`,
   * `urn:ietf:params:oauth:token-type:access_token`,
   * `urn:ietf:params:oauth:token-type:mtls`, and `urn:ietf:params:oauth:token-
   * type:saml2`.
   *
   * @var string
   */
  public $subjectTokenType;

  /**
   * The full resource name of the identity provider; for example: `//iam.google
   * apis.com/projects//locations/global/workloadIdentityPools//providers/` for
   * workload identity pool providers, or
   * `//iam.googleapis.com/locations/global/workforcePools//providers/` for
   * workforce pool providers. Required when exchanging an external credential
   * for a Google access token.
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * Required. The grant type. Must be `urn:ietf:params:oauth:grant-type:token-
   * exchange`, which indicates a token exchange.
   *
   * @param string $grantType
   */
  public function setGrantType($grantType)
  {
    $this->grantType = $grantType;
  }
  /**
   * @return string
   */
  public function getGrantType()
  {
    return $this->grantType;
  }
  /**
   * A set of features that Security Token Service supports, in addition to the
   * standard OAuth 2.0 token exchange, formatted as a serialized JSON object of
   * Options. The size of the parameter value must not exceed 4 * 1024 * 1024
   * characters (4 MB).
   *
   * @param string $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return string
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * Required. An identifier for the type of requested security token. Can be
   * `urn:ietf:params:oauth:token-type:access_token` or
   * `urn:ietf:params:oauth:token-type:access_boundary_intermediary_token`.
   *
   * @param string $requestedTokenType
   */
  public function setRequestedTokenType($requestedTokenType)
  {
    $this->requestedTokenType = $requestedTokenType;
  }
  /**
   * @return string
   */
  public function getRequestedTokenType()
  {
    return $this->requestedTokenType;
  }
  /**
   * The OAuth 2.0 scopes to include on the resulting access token, formatted as
   * a list of space-delimited, case-sensitive strings; for example,
   * `https://www.googleapis.com/auth/cloud-platform`. Required when exchanging
   * an external credential for a Google access token. For a list of OAuth 2.0
   * scopes, see [OAuth 2.0 Scopes for Google
   * APIs](https://developers.google.com/identity/protocols/oauth2/scopes).
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
   * Required. The input token. This token is either an external credential
   * issued by a workload identity pool provider, or a short-lived access token
   * issued by Google. If the token is an OIDC JWT, it must use the JWT format
   * defined in [RFC 7523](https://tools.ietf.org/html/rfc7523), and the
   * `subject_token_type` must be either `urn:ietf:params:oauth:token-type:jwt`
   * or `urn:ietf:params:oauth:token-type:id_token`. The following headers are
   * required: - `kid`: The identifier of the signing key securing the JWT. -
   * `alg`: The cryptographic algorithm securing the JWT. Must be `RS256` or
   * `ES256`. The following payload fields are required. For more information,
   * see [RFC 7523, Section 3](https://tools.ietf.org/html/rfc7523#section-3): -
   * `iss`: The issuer of the token. The issuer must provide a discovery
   * document at the URL `/.well-known/openid-configuration`, where `` is the
   * value of this field. The document must be formatted according to section
   * 4.2 of the [OIDC 1.0 Discovery
   * specification](https://openid.net/specs/openid-connect-
   * discovery-1_0.html#ProviderConfigurationResponse). - `iat`: The issue time,
   * in seconds, since the Unix epoch. Must be in the past. - `exp`: The
   * expiration time, in seconds, since the Unix epoch. Must be less than 48
   * hours after `iat`. Shorter expiration times are more secure. If possible,
   * we recommend setting an expiration time less than 6 hours. - `sub`: The
   * identity asserted in the JWT. - `aud`: For workload identity pools, this
   * must be a value specified in the allowed audiences for the workload
   * identity pool provider, or one of the audiences allowed by default if no
   * audiences were specified. See https://cloud.google.com/iam/docs/reference/r
   * est/v1/projects.locations.workloadIdentityPools.providers#oidc. For
   * workforce pools, this must match the client ID specified in the provider
   * configuration. See https://cloud.google.com/iam/docs/reference/rest/v1/loca
   * tions.workforcePools.providers#oidc. Example header: ``` { "alg": "RS256",
   * "kid": "us-east-11" } ``` Example payload: ``` { "iss":
   * "https://accounts.google.com", "iat": 1517963104, "exp": 1517966704, "aud":
   * "//iam.googleapis.com/projects/1234567890123/locations/global/workloadIdent
   * ityPools/my-pool/providers/my-provider", "sub": "113475438248934895348",
   * "my_claims": { "additional_claim": "value" } } ``` If `subject_token` is
   * for AWS, it must be a serialized `GetCallerIdentity` token. This token
   * contains the same information as a request to the AWS [`GetCallerIdentity()
   * `](https://docs.aws.amazon.com/STS/latest/APIReference/API_GetCallerIdentit
   * y) method, as well as the AWS [signature](https://docs.aws.amazon.com/gener
   * al/latest/gr/signing_aws_api_requests.html) for the request information.
   * Use Signature Version 4. Format the request as URL-encoded JSON, and set
   * the `subject_token_type` parameter to `urn:ietf:params:aws:token-
   * type:aws4_request`. The following parameters are required: - `url`: The URL
   * of the AWS STS endpoint for `GetCallerIdentity()`, such as
   * `https://sts.amazonaws.com?Action=GetCallerIdentity&Version=2011-06-15`.
   * Regional endpoints are also supported. - `method`: The HTTP request method:
   * `POST`. - `headers`: The HTTP request headers, which must include: -
   * `Authorization`: The request signature. - `x-amz-date`: The time you will
   * send the request, formatted as an [ISO8601 Basic](https://docs.aws.amazon.c
   * om/general/latest/gr/sigv4_elements.html#sigv4_elements_date) string. This
   * value is typically set to the current time and is used to help prevent
   * replay attacks. - `host`: The hostname of the `url` field; for example,
   * `sts.amazonaws.com`. - `x-goog-cloud-target-resource`: The full, canonical
   * resource name of the workload identity pool provider, with or without an
   * `https:` prefix. To help ensure data integrity, we recommend including this
   * header in the `SignedHeaders` field of the signed request. For example: //i
   * am.googleapis.com/projects//locations/global/workloadIdentityPools//provide
   * rs/ https://iam.googleapis.com/projects//locations/global/workloadIdentityP
   * ools//providers/ If you are using temporary security credentials provided
   * by AWS, you must also include the header `x-amz-security-token`, with the
   * value set to the session token. The following example shows a
   * `GetCallerIdentity` token: ``` { "headers": [ {"key": "x-amz-date",
   * "value": "20200815T015049Z"}, {"key": "Authorization", "value": "AWS4-HMAC-
   * SHA256+Credential=$credential,+SignedHeaders=host;x-amz-date;x-goog-cloud-
   * target-resource,+Signature=$signature"}, {"key": "x-goog-cloud-target-
   * resource", "value": "//iam.googleapis.com/projects//locations/global/worklo
   * adIdentityPools//providers/"}, {"key": "host", "value":
   * "sts.amazonaws.com"} . ], "method": "POST", "url":
   * "https://sts.amazonaws.com?Action=GetCallerIdentity&Version=2011-06-15" }
   * ``` If the token is a SAML 2.0 assertion, it must use the format defined in
   * [the SAML 2.0 spec](https://docs.oasis-open.org/security/saml/Post2.0/sstc-
   * saml-tech-overview-2.0-cd-02.pdf), and the `subject_token_type` must be
   * `urn:ietf:params:oauth:token-type:saml2`. See [Verification of external
   * credentials](https://cloud.google.com/iam/docs/using-workload-identity-
   * federation#verification_of_external_credentials) for details on how SAML
   * 2.0 assertions are validated during token exchanges. You can also use a
   * Google-issued OAuth 2.0 access token with this field to obtain an access
   * token with new security attributes applied, such as a Credential Access
   * Boundary. In this case, set `subject_token_type` to
   * `urn:ietf:params:oauth:token-type:access_token`. If an access token already
   * contains security attributes, you cannot apply additional security
   * attributes. If the request is for X.509 certificate-based authentication,
   * the `subject_token` must be a JSON-formatted list of X.509 certificates in
   * DER format, as defined in [RFC 7515](https://www.rfc-
   * editor.org/rfc/rfc7515#section-4.1.6). `subject_token_type` must be
   * `urn:ietf:params:oauth:token-type:mtls`. The following example shows a
   * JSON-formatted list of X.509 certificate in DER format: ```
   * [\"MIIEYDCCA0i...\", \"MCIFFGAGTT0...\"] ```
   *
   * @param string $subjectToken
   */
  public function setSubjectToken($subjectToken)
  {
    $this->subjectToken = $subjectToken;
  }
  /**
   * @return string
   */
  public function getSubjectToken()
  {
    return $this->subjectToken;
  }
  /**
   * Required. An identifier that indicates the type of the security token in
   * the `subject_token` parameter. Supported values are
   * `urn:ietf:params:oauth:token-type:jwt`, `urn:ietf:params:oauth:token-
   * type:id_token`, `urn:ietf:params:aws:token-type:aws4_request`,
   * `urn:ietf:params:oauth:token-type:access_token`,
   * `urn:ietf:params:oauth:token-type:mtls`, and `urn:ietf:params:oauth:token-
   * type:saml2`.
   *
   * @param string $subjectTokenType
   */
  public function setSubjectTokenType($subjectTokenType)
  {
    $this->subjectTokenType = $subjectTokenType;
  }
  /**
   * @return string
   */
  public function getSubjectTokenType()
  {
    return $this->subjectTokenType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityStsV1ExchangeTokenRequest::class, 'Google_Service_CloudSecurityToken_GoogleIdentityStsV1ExchangeTokenRequest');
