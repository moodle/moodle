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

class BackendRule extends \Google\Model
{
  public const PATH_TRANSLATION_PATH_TRANSLATION_UNSPECIFIED = 'PATH_TRANSLATION_UNSPECIFIED';
  public const PATH_TRANSLATION_CONSTANT_ADDRESS = 'CONSTANT_ADDRESS';
  /**
   * The request path will be appended to the backend address. # Examples Given
   * the following operation config: Method path: /api/company/{cid}/user/{uid}
   * Backend address: https://example.appspot.com Requests to the following
   * request paths will call the backend at the translated path: Request path:
   * /api/company/widgetworks/user/johndoe Translated:
   * https://example.appspot.com/api/company/widgetworks/user/johndoe Request
   * path: /api/company/widgetworks/user/johndoe?timezone=EST Translated: https:
   * //example.appspot.com/api/company/widgetworks/user/johndoe?timezone=EST
   */
  public const PATH_TRANSLATION_APPEND_PATH_TO_ADDRESS = 'APPEND_PATH_TO_ADDRESS';
  /**
   * The address of the API backend. The scheme is used to determine the backend
   * protocol and security. The following schemes are accepted: SCHEME PROTOCOL
   * SECURITY http:// HTTP None https:// HTTP TLS grpc:// gRPC None grpcs://
   * gRPC TLS It is recommended to explicitly include a scheme. Leaving out the
   * scheme may cause constrasting behaviors across platforms. If the port is
   * unspecified, the default is: - 80 for schemes without TLS - 443 for schemes
   * with TLS For HTTP backends, use protocol to specify the protocol version.
   *
   * @var string
   */
  public $address;
  /**
   * The number of seconds to wait for a response from a request. The default
   * varies based on the request protocol and deployment environment.
   *
   * @var 
   */
  public $deadline;
  /**
   * When disable_auth is true, a JWT ID token won't be generated and the
   * original "Authorization" HTTP header will be preserved. If the header is
   * used to carry the original token and is expected by the backend, this field
   * must be set to true to preserve the header.
   *
   * @var bool
   */
  public $disableAuth;
  /**
   * The JWT audience is used when generating a JWT ID token for the backend.
   * This ID token will be added in the HTTP "authorization" header, and sent to
   * the backend.
   *
   * @var string
   */
  public $jwtAudience;
  /**
   * The load balancing policy used for connection to the application backend.
   * Defined as an arbitrary string to accomondate custom load balancing
   * policies supported by the underlying channel, but suggest most users use
   * one of the standard policies, such as the default, "RoundRobin".
   *
   * @var string
   */
  public $loadBalancingPolicy;
  /**
   * Deprecated, do not use.
   *
   * @deprecated
   * @var 
   */
  public $minDeadline;
  /**
   * The number of seconds to wait for the completion of a long running
   * operation. The default is no deadline.
   *
   * @var 
   */
  public $operationDeadline;
  protected $overridesByRequestProtocolType = BackendRule::class;
  protected $overridesByRequestProtocolDataType = 'map';
  /**
   * no-lint
   *
   * @var string
   */
  public $pathTranslation;
  /**
   * The protocol used for sending a request to the backend. The supported
   * values are "http/1.1" and "h2". The default value is inferred from the
   * scheme in the address field: SCHEME PROTOCOL http:// http/1.1 https://
   * http/1.1 grpc:// h2 grpcs:// h2 For secure HTTP backends (https://) that
   * support HTTP/2, set this field to "h2" for improved performance.
   * Configuring this field to non-default values is only supported for secure
   * HTTP backends. This field will be ignored for all other backends. See
   * https://www.iana.org/assignments/tls-extensiontype-values/tls-
   * extensiontype-values.xhtml#alpn-protocol-ids for more details on the
   * supported values.
   *
   * @var string
   */
  public $protocol;
  /**
   * Selects the methods to which this rule applies. Refer to selector for
   * syntax details.
   *
   * @var string
   */
  public $selector;

  /**
   * The address of the API backend. The scheme is used to determine the backend
   * protocol and security. The following schemes are accepted: SCHEME PROTOCOL
   * SECURITY http:// HTTP None https:// HTTP TLS grpc:// gRPC None grpcs://
   * gRPC TLS It is recommended to explicitly include a scheme. Leaving out the
   * scheme may cause constrasting behaviors across platforms. If the port is
   * unspecified, the default is: - 80 for schemes without TLS - 443 for schemes
   * with TLS For HTTP backends, use protocol to specify the protocol version.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  public function setDeadline($deadline)
  {
    $this->deadline = $deadline;
  }
  public function getDeadline()
  {
    return $this->deadline;
  }
  /**
   * When disable_auth is true, a JWT ID token won't be generated and the
   * original "Authorization" HTTP header will be preserved. If the header is
   * used to carry the original token and is expected by the backend, this field
   * must be set to true to preserve the header.
   *
   * @param bool $disableAuth
   */
  public function setDisableAuth($disableAuth)
  {
    $this->disableAuth = $disableAuth;
  }
  /**
   * @return bool
   */
  public function getDisableAuth()
  {
    return $this->disableAuth;
  }
  /**
   * The JWT audience is used when generating a JWT ID token for the backend.
   * This ID token will be added in the HTTP "authorization" header, and sent to
   * the backend.
   *
   * @param string $jwtAudience
   */
  public function setJwtAudience($jwtAudience)
  {
    $this->jwtAudience = $jwtAudience;
  }
  /**
   * @return string
   */
  public function getJwtAudience()
  {
    return $this->jwtAudience;
  }
  /**
   * The load balancing policy used for connection to the application backend.
   * Defined as an arbitrary string to accomondate custom load balancing
   * policies supported by the underlying channel, but suggest most users use
   * one of the standard policies, such as the default, "RoundRobin".
   *
   * @param string $loadBalancingPolicy
   */
  public function setLoadBalancingPolicy($loadBalancingPolicy)
  {
    $this->loadBalancingPolicy = $loadBalancingPolicy;
  }
  /**
   * @return string
   */
  public function getLoadBalancingPolicy()
  {
    return $this->loadBalancingPolicy;
  }
  public function setMinDeadline($minDeadline)
  {
    $this->minDeadline = $minDeadline;
  }
  public function getMinDeadline()
  {
    return $this->minDeadline;
  }
  public function setOperationDeadline($operationDeadline)
  {
    $this->operationDeadline = $operationDeadline;
  }
  public function getOperationDeadline()
  {
    return $this->operationDeadline;
  }
  /**
   * The map between request protocol and the backend address.
   *
   * @param BackendRule[] $overridesByRequestProtocol
   */
  public function setOverridesByRequestProtocol($overridesByRequestProtocol)
  {
    $this->overridesByRequestProtocol = $overridesByRequestProtocol;
  }
  /**
   * @return BackendRule[]
   */
  public function getOverridesByRequestProtocol()
  {
    return $this->overridesByRequestProtocol;
  }
  /**
   * no-lint
   *
   * Accepted values: PATH_TRANSLATION_UNSPECIFIED, CONSTANT_ADDRESS,
   * APPEND_PATH_TO_ADDRESS
   *
   * @param self::PATH_TRANSLATION_* $pathTranslation
   */
  public function setPathTranslation($pathTranslation)
  {
    $this->pathTranslation = $pathTranslation;
  }
  /**
   * @return self::PATH_TRANSLATION_*
   */
  public function getPathTranslation()
  {
    return $this->pathTranslation;
  }
  /**
   * The protocol used for sending a request to the backend. The supported
   * values are "http/1.1" and "h2". The default value is inferred from the
   * scheme in the address field: SCHEME PROTOCOL http:// http/1.1 https://
   * http/1.1 grpc:// h2 grpcs:// h2 For secure HTTP backends (https://) that
   * support HTTP/2, set this field to "h2" for improved performance.
   * Configuring this field to non-default values is only supported for secure
   * HTTP backends. This field will be ignored for all other backends. See
   * https://www.iana.org/assignments/tls-extensiontype-values/tls-
   * extensiontype-values.xhtml#alpn-protocol-ids for more details on the
   * supported values.
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Selects the methods to which this rule applies. Refer to selector for
   * syntax details.
   *
   * @param string $selector
   */
  public function setSelector($selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return string
   */
  public function getSelector()
  {
    return $this->selector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendRule::class, 'Google_Service_ServiceNetworking_BackendRule');
