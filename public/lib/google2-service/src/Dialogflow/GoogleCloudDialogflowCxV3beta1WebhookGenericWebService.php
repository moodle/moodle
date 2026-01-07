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

class GoogleCloudDialogflowCxV3beta1WebhookGenericWebService extends \Google\Collection
{
  /**
   * HTTP method not specified.
   */
  public const HTTP_METHOD_HTTP_METHOD_UNSPECIFIED = 'HTTP_METHOD_UNSPECIFIED';
  /**
   * HTTP POST Method.
   */
  public const HTTP_METHOD_POST = 'POST';
  /**
   * HTTP GET Method.
   */
  public const HTTP_METHOD_GET = 'GET';
  /**
   * HTTP HEAD Method.
   */
  public const HTTP_METHOD_HEAD = 'HEAD';
  /**
   * HTTP PUT Method.
   */
  public const HTTP_METHOD_PUT = 'PUT';
  /**
   * HTTP DELETE Method.
   */
  public const HTTP_METHOD_DELETE = 'DELETE';
  /**
   * HTTP PATCH Method.
   */
  public const HTTP_METHOD_PATCH = 'PATCH';
  /**
   * HTTP OPTIONS Method.
   */
  public const HTTP_METHOD_OPTIONS = 'OPTIONS';
  /**
   * Service agent auth type unspecified. Default to ID_TOKEN.
   */
  public const SERVICE_AGENT_AUTH_SERVICE_AGENT_AUTH_UNSPECIFIED = 'SERVICE_AGENT_AUTH_UNSPECIFIED';
  /**
   * No token used.
   */
  public const SERVICE_AGENT_AUTH_NONE = 'NONE';
  /**
   * Use [ID token](https://cloud.google.com/docs/authentication/token-types#id)
   * generated from service agent. This can be used to access Cloud Function and
   * Cloud Run after you grant Invoker role to `service-@gcp-sa-
   * dialogflow.iam.gserviceaccount.com`.
   */
  public const SERVICE_AGENT_AUTH_ID_TOKEN = 'ID_TOKEN';
  /**
   * Use [access token](https://cloud.google.com/docs/authentication/token-
   * types#access) generated from service agent. This can be used to access
   * other Google Cloud APIs after you grant required roles to `service-@gcp-sa-
   * dialogflow.iam.gserviceaccount.com`.
   */
  public const SERVICE_AGENT_AUTH_ACCESS_TOKEN = 'ACCESS_TOKEN';
  /**
   * Default value. This value is unused.
   */
  public const WEBHOOK_TYPE_WEBHOOK_TYPE_UNSPECIFIED = 'WEBHOOK_TYPE_UNSPECIFIED';
  /**
   * Represents a standard webhook.
   */
  public const WEBHOOK_TYPE_STANDARD = 'STANDARD';
  /**
   * Represents a flexible webhook.
   */
  public const WEBHOOK_TYPE_FLEXIBLE = 'FLEXIBLE';
  protected $collection_key = 'allowedCaCerts';
  /**
   * Optional. Specifies a list of allowed custom CA certificates (in DER
   * format) for HTTPS verification. This overrides the default SSL trust store.
   * If this is empty or unspecified, Dialogflow will use Google's default trust
   * store to verify certificates. N.B. Make sure the HTTPS server certificates
   * are signed with "subject alt name". For instance a certificate can be self-
   * signed using the following command, ``` openssl x509 -req -days 200 -in
   * example.com.csr \ -signkey example.com.key \ -out example.com.crt \
   * -extfile <(printf "\nsubjectAltName='DNS:www.example.com'") ```
   *
   * @var string[]
   */
  public $allowedCaCerts;
  /**
   * Optional. HTTP method for the flexible webhook calls. Standard webhook
   * always uses POST.
   *
   * @var string
   */
  public $httpMethod;
  protected $oauthConfigType = GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceOAuthConfig::class;
  protected $oauthConfigDataType = '';
  /**
   * Optional. Maps the values extracted from specific fields of the flexible
   * webhook response into session parameters. - Key: session parameter name -
   * Value: field path in the webhook response
   *
   * @var string[]
   */
  public $parameterMapping;
  /**
   * The password for HTTP Basic authentication.
   *
   * @deprecated
   * @var string
   */
  public $password;
  /**
   * Optional. Defines a custom JSON object as request body to send to flexible
   * webhook.
   *
   * @var string
   */
  public $requestBody;
  /**
   * The HTTP request headers to send together with webhook requests.
   *
   * @var string[]
   */
  public $requestHeaders;
  /**
   * Optional. The SecretManager secret version resource storing the
   * username:password pair for HTTP Basic authentication. Format:
   * `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @var string
   */
  public $secretVersionForUsernamePassword;
  protected $secretVersionsForRequestHeadersType = GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceSecretVersionHeaderValue::class;
  protected $secretVersionsForRequestHeadersDataType = 'map';
  protected $serviceAccountAuthConfigType = GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceServiceAccountAuthConfig::class;
  protected $serviceAccountAuthConfigDataType = '';
  /**
   * Optional. Indicate the auth token type generated from the [Diglogflow
   * service agent](https://cloud.google.com/iam/docs/service-agents#dialogflow-
   * service-agent). The generated token is sent in the Authorization header.
   *
   * @var string
   */
  public $serviceAgentAuth;
  /**
   * Required. The webhook URI for receiving POST requests. It must use https
   * protocol.
   *
   * @var string
   */
  public $uri;
  /**
   * The user name for HTTP Basic authentication.
   *
   * @deprecated
   * @var string
   */
  public $username;
  /**
   * Optional. Type of the webhook.
   *
   * @var string
   */
  public $webhookType;

  /**
   * Optional. Specifies a list of allowed custom CA certificates (in DER
   * format) for HTTPS verification. This overrides the default SSL trust store.
   * If this is empty or unspecified, Dialogflow will use Google's default trust
   * store to verify certificates. N.B. Make sure the HTTPS server certificates
   * are signed with "subject alt name". For instance a certificate can be self-
   * signed using the following command, ``` openssl x509 -req -days 200 -in
   * example.com.csr \ -signkey example.com.key \ -out example.com.crt \
   * -extfile <(printf "\nsubjectAltName='DNS:www.example.com'") ```
   *
   * @param string[] $allowedCaCerts
   */
  public function setAllowedCaCerts($allowedCaCerts)
  {
    $this->allowedCaCerts = $allowedCaCerts;
  }
  /**
   * @return string[]
   */
  public function getAllowedCaCerts()
  {
    return $this->allowedCaCerts;
  }
  /**
   * Optional. HTTP method for the flexible webhook calls. Standard webhook
   * always uses POST.
   *
   * Accepted values: HTTP_METHOD_UNSPECIFIED, POST, GET, HEAD, PUT, DELETE,
   * PATCH, OPTIONS
   *
   * @param self::HTTP_METHOD_* $httpMethod
   */
  public function setHttpMethod($httpMethod)
  {
    $this->httpMethod = $httpMethod;
  }
  /**
   * @return self::HTTP_METHOD_*
   */
  public function getHttpMethod()
  {
    return $this->httpMethod;
  }
  /**
   * Optional. The OAuth configuration of the webhook. If specified, Dialogflow
   * will initiate the OAuth client credential flow to exchange an access token
   * from the 3rd party platform and put it in the auth header.
   *
   * @param GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceOAuthConfig $oauthConfig
   */
  public function setOauthConfig(GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceOAuthConfig $oauthConfig)
  {
    $this->oauthConfig = $oauthConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceOAuthConfig
   */
  public function getOauthConfig()
  {
    return $this->oauthConfig;
  }
  /**
   * Optional. Maps the values extracted from specific fields of the flexible
   * webhook response into session parameters. - Key: session parameter name -
   * Value: field path in the webhook response
   *
   * @param string[] $parameterMapping
   */
  public function setParameterMapping($parameterMapping)
  {
    $this->parameterMapping = $parameterMapping;
  }
  /**
   * @return string[]
   */
  public function getParameterMapping()
  {
    return $this->parameterMapping;
  }
  /**
   * The password for HTTP Basic authentication.
   *
   * @deprecated
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Optional. Defines a custom JSON object as request body to send to flexible
   * webhook.
   *
   * @param string $requestBody
   */
  public function setRequestBody($requestBody)
  {
    $this->requestBody = $requestBody;
  }
  /**
   * @return string
   */
  public function getRequestBody()
  {
    return $this->requestBody;
  }
  /**
   * The HTTP request headers to send together with webhook requests.
   *
   * @param string[] $requestHeaders
   */
  public function setRequestHeaders($requestHeaders)
  {
    $this->requestHeaders = $requestHeaders;
  }
  /**
   * @return string[]
   */
  public function getRequestHeaders()
  {
    return $this->requestHeaders;
  }
  /**
   * Optional. The SecretManager secret version resource storing the
   * username:password pair for HTTP Basic authentication. Format:
   * `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @param string $secretVersionForUsernamePassword
   */
  public function setSecretVersionForUsernamePassword($secretVersionForUsernamePassword)
  {
    $this->secretVersionForUsernamePassword = $secretVersionForUsernamePassword;
  }
  /**
   * @return string
   */
  public function getSecretVersionForUsernamePassword()
  {
    return $this->secretVersionForUsernamePassword;
  }
  /**
   * Optional. The HTTP request headers to send together with webhook requests.
   * Header values are stored in SecretManager secret versions. When the same
   * header name is specified in both `request_headers` and
   * `secret_versions_for_request_headers`, the value in
   * `secret_versions_for_request_headers` will be used.
   *
   * @param GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceSecretVersionHeaderValue[] $secretVersionsForRequestHeaders
   */
  public function setSecretVersionsForRequestHeaders($secretVersionsForRequestHeaders)
  {
    $this->secretVersionsForRequestHeaders = $secretVersionsForRequestHeaders;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceSecretVersionHeaderValue[]
   */
  public function getSecretVersionsForRequestHeaders()
  {
    return $this->secretVersionsForRequestHeaders;
  }
  /**
   * Optional. Configuration for service account authentication.
   *
   * @param GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceServiceAccountAuthConfig $serviceAccountAuthConfig
   */
  public function setServiceAccountAuthConfig(GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceServiceAccountAuthConfig $serviceAccountAuthConfig)
  {
    $this->serviceAccountAuthConfig = $serviceAccountAuthConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceServiceAccountAuthConfig
   */
  public function getServiceAccountAuthConfig()
  {
    return $this->serviceAccountAuthConfig;
  }
  /**
   * Optional. Indicate the auth token type generated from the [Diglogflow
   * service agent](https://cloud.google.com/iam/docs/service-agents#dialogflow-
   * service-agent). The generated token is sent in the Authorization header.
   *
   * Accepted values: SERVICE_AGENT_AUTH_UNSPECIFIED, NONE, ID_TOKEN,
   * ACCESS_TOKEN
   *
   * @param self::SERVICE_AGENT_AUTH_* $serviceAgentAuth
   */
  public function setServiceAgentAuth($serviceAgentAuth)
  {
    $this->serviceAgentAuth = $serviceAgentAuth;
  }
  /**
   * @return self::SERVICE_AGENT_AUTH_*
   */
  public function getServiceAgentAuth()
  {
    return $this->serviceAgentAuth;
  }
  /**
   * Required. The webhook URI for receiving POST requests. It must use https
   * protocol.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * The user name for HTTP Basic authentication.
   *
   * @deprecated
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
  /**
   * Optional. Type of the webhook.
   *
   * Accepted values: WEBHOOK_TYPE_UNSPECIFIED, STANDARD, FLEXIBLE
   *
   * @param self::WEBHOOK_TYPE_* $webhookType
   */
  public function setWebhookType($webhookType)
  {
    $this->webhookType = $webhookType;
  }
  /**
   * @return self::WEBHOOK_TYPE_*
   */
  public function getWebhookType()
  {
    return $this->webhookType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1WebhookGenericWebService::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1WebhookGenericWebService');
