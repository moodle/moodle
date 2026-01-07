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

namespace Google\Service\Compute;

class SSLHealthCheck extends \Google\Model
{
  /**
   * The port number in the health check's port is used for health checking.
   * Applies to network endpoint group and instance group backends.
   */
  public const PORT_SPECIFICATION_USE_FIXED_PORT = 'USE_FIXED_PORT';
  /**
   * Not supported.
   */
  public const PORT_SPECIFICATION_USE_NAMED_PORT = 'USE_NAMED_PORT';
  /**
   * For network endpoint group backends, the health check uses the port number
   * specified on each endpoint in the network endpoint group. For instance
   * group backends, the health check uses the port number specified for the
   * backend service's named port defined in the instance group's named ports.
   */
  public const PORT_SPECIFICATION_USE_SERVING_PORT = 'USE_SERVING_PORT';
  public const PROXY_HEADER_NONE = 'NONE';
  public const PROXY_HEADER_PROXY_V1 = 'PROXY_V1';
  /**
   * The TCP port number to which the health check prober sends packets. The
   * default value is 443. Valid values are 1 through65535.
   *
   * @var int
   */
  public $port;
  /**
   * Not supported.
   *
   * @var string
   */
  public $portName;
  /**
   * Specifies how a port is selected for health checking. Can be one of the
   * following values:  USE_FIXED_PORT: Specifies a port number explicitly using
   * theport field  in the health check. Supported by backend services for
   * passthrough load balancers and backend services for proxy load balancers.
   * Not supported by target pools. The health check supports all backends
   * supported by the backend service provided the backend can be health
   * checked. For example, GCE_VM_IP network endpoint groups, GCE_VM_IP_PORT
   * network endpoint groups, and instance group backends.   USE_NAMED_PORT: Not
   * supported.  USE_SERVING_PORT: Provides an indirect method of specifying the
   * health check port by referring to the backend service. Only supported by
   * backend services for proxy load balancers. Not supported by target pools.
   * Not supported by backend services for passthrough load balancers. Supports
   * all backends that can be health checked; for example,GCE_VM_IP_PORT network
   * endpoint groups and instance group backends.
   *
   * For GCE_VM_IP_PORT network endpoint group backends, the health check uses
   * the port number specified for each endpoint in the network endpoint group.
   * For instance group backends, the health check uses the port number
   * determined by looking up the backend service's named port in the instance
   * group's list of named ports.
   *
   * @var string
   */
  public $portSpecification;
  /**
   * Specifies the type of proxy header to append before sending data to the
   * backend, either NONE or PROXY_V1. The default is NONE.
   *
   * @var string
   */
  public $proxyHeader;
  /**
   * Instructs the health check prober to send this exact ASCII string, up to
   * 1024 bytes in length, after establishing the TCP connection and SSL
   * handshake.
   *
   * @var string
   */
  public $request;
  /**
   * Creates a content-based SSL health check. In addition to establishing a TCP
   * connection and the TLS handshake, you can configure the health check to
   * pass only when the backend sends this exact response ASCII string, up to
   * 1024 bytes in length. For details, see: https://cloud.google.com/load-
   * balancing/docs/health-check-concepts#criteria-protocol-ssl-tcp
   *
   * @var string
   */
  public $response;

  /**
   * The TCP port number to which the health check prober sends packets. The
   * default value is 443. Valid values are 1 through65535.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Not supported.
   *
   * @param string $portName
   */
  public function setPortName($portName)
  {
    $this->portName = $portName;
  }
  /**
   * @return string
   */
  public function getPortName()
  {
    return $this->portName;
  }
  /**
   * Specifies how a port is selected for health checking. Can be one of the
   * following values:  USE_FIXED_PORT: Specifies a port number explicitly using
   * theport field  in the health check. Supported by backend services for
   * passthrough load balancers and backend services for proxy load balancers.
   * Not supported by target pools. The health check supports all backends
   * supported by the backend service provided the backend can be health
   * checked. For example, GCE_VM_IP network endpoint groups, GCE_VM_IP_PORT
   * network endpoint groups, and instance group backends.   USE_NAMED_PORT: Not
   * supported.  USE_SERVING_PORT: Provides an indirect method of specifying the
   * health check port by referring to the backend service. Only supported by
   * backend services for proxy load balancers. Not supported by target pools.
   * Not supported by backend services for passthrough load balancers. Supports
   * all backends that can be health checked; for example,GCE_VM_IP_PORT network
   * endpoint groups and instance group backends.
   *
   * For GCE_VM_IP_PORT network endpoint group backends, the health check uses
   * the port number specified for each endpoint in the network endpoint group.
   * For instance group backends, the health check uses the port number
   * determined by looking up the backend service's named port in the instance
   * group's list of named ports.
   *
   * Accepted values: USE_FIXED_PORT, USE_NAMED_PORT, USE_SERVING_PORT
   *
   * @param self::PORT_SPECIFICATION_* $portSpecification
   */
  public function setPortSpecification($portSpecification)
  {
    $this->portSpecification = $portSpecification;
  }
  /**
   * @return self::PORT_SPECIFICATION_*
   */
  public function getPortSpecification()
  {
    return $this->portSpecification;
  }
  /**
   * Specifies the type of proxy header to append before sending data to the
   * backend, either NONE or PROXY_V1. The default is NONE.
   *
   * Accepted values: NONE, PROXY_V1
   *
   * @param self::PROXY_HEADER_* $proxyHeader
   */
  public function setProxyHeader($proxyHeader)
  {
    $this->proxyHeader = $proxyHeader;
  }
  /**
   * @return self::PROXY_HEADER_*
   */
  public function getProxyHeader()
  {
    return $this->proxyHeader;
  }
  /**
   * Instructs the health check prober to send this exact ASCII string, up to
   * 1024 bytes in length, after establishing the TCP connection and SSL
   * handshake.
   *
   * @param string $request
   */
  public function setRequest($request)
  {
    $this->request = $request;
  }
  /**
   * @return string
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Creates a content-based SSL health check. In addition to establishing a TCP
   * connection and the TLS handshake, you can configure the health check to
   * pass only when the backend sends this exact response ASCII string, up to
   * 1024 bytes in length. For details, see: https://cloud.google.com/load-
   * balancing/docs/health-check-concepts#criteria-protocol-ssl-tcp
   *
   * @param string $response
   */
  public function setResponse($response)
  {
    $this->response = $response;
  }
  /**
   * @return string
   */
  public function getResponse()
  {
    return $this->response;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SSLHealthCheck::class, 'Google_Service_Compute_SSLHealthCheck');
