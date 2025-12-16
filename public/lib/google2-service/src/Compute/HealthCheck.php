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

class HealthCheck extends \Google\Collection
{
  public const TYPE_GRPC = 'GRPC';
  public const TYPE_GRPC_WITH_TLS = 'GRPC_WITH_TLS';
  public const TYPE_HTTP = 'HTTP';
  public const TYPE_HTTP2 = 'HTTP2';
  public const TYPE_HTTPS = 'HTTPS';
  public const TYPE_INVALID = 'INVALID';
  public const TYPE_SSL = 'SSL';
  public const TYPE_TCP = 'TCP';
  protected $collection_key = 'sourceRegions';
  /**
   * How often (in seconds) to send a health check. The default value is 5
   * seconds.
   *
   * @var int
   */
  public $checkIntervalSec;
  /**
   * Output only. [Output Only] Creation timestamp in3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  protected $grpcHealthCheckType = GRPCHealthCheck::class;
  protected $grpcHealthCheckDataType = '';
  protected $grpcTlsHealthCheckType = GRPCTLSHealthCheck::class;
  protected $grpcTlsHealthCheckDataType = '';
  /**
   * A so-far unhealthy instance will be marked healthy after this many
   * consecutive successes. The default value is 2.
   *
   * @var int
   */
  public $healthyThreshold;
  protected $http2HealthCheckType = HTTP2HealthCheck::class;
  protected $http2HealthCheckDataType = '';
  protected $httpHealthCheckType = HTTPHealthCheck::class;
  protected $httpHealthCheckDataType = '';
  protected $httpsHealthCheckType = HTTPSHealthCheck::class;
  protected $httpsHealthCheckDataType = '';
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Type of the resource.
   *
   * @var string
   */
  public $kind;
  protected $logConfigType = HealthCheckLogConfig::class;
  protected $logConfigDataType = '';
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035. For example,
   * a name that is 1-63 characters long, matches the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?`, and otherwise complies with RFC1035. This
   * regular expression describes a name where the first character is a
   * lowercase letter, and all following characters are a dash, lowercase
   * letter, or digit, except the last character, which isn't a dash.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] Region where the health check resides.  Not
   * applicable to global health checks.
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The list of cloud regions from which health checks are performed. If any
   * regions are specified, then exactly 3 regions should be specified. The
   * region names must be valid names of Google Cloud regions. This can only be
   * set for global health check. If this list is non-empty, then there are
   * restrictions on what other health check fields are supported and what other
   * resources can use this health check:        - SSL, HTTP2, and GRPC
   * protocols are not supported.    - The TCP request field is not supported.
   * - The proxyHeader field for HTTP, HTTPS, and TCP is not    supported.    -
   * The checkIntervalSec field must be at least 30.    - The health check
   * cannot be used with BackendService nor with managed    instance group auto-
   * healing.
   *
   * @var string[]
   */
  public $sourceRegions;
  protected $sslHealthCheckType = SSLHealthCheck::class;
  protected $sslHealthCheckDataType = '';
  protected $tcpHealthCheckType = TCPHealthCheck::class;
  protected $tcpHealthCheckDataType = '';
  /**
   * How long (in seconds) to wait before claiming failure. The default value is
   * 5 seconds. It is invalid for timeoutSec to have greater value than
   * checkIntervalSec.
   *
   * @var int
   */
  public $timeoutSec;
  /**
   * Specifies the type of the healthCheck, either TCP,SSL, HTTP, HTTPS,HTTP2 or
   * GRPC. Exactly one of the protocol-specific health check fields must be
   * specified, which must matchtype field.
   *
   * @var string
   */
  public $type;
  /**
   * A so-far healthy instance will be marked unhealthy after this many
   * consecutive failures. The default value is 2.
   *
   * @var int
   */
  public $unhealthyThreshold;

  /**
   * How often (in seconds) to send a health check. The default value is 5
   * seconds.
   *
   * @param int $checkIntervalSec
   */
  public function setCheckIntervalSec($checkIntervalSec)
  {
    $this->checkIntervalSec = $checkIntervalSec;
  }
  /**
   * @return int
   */
  public function getCheckIntervalSec()
  {
    return $this->checkIntervalSec;
  }
  /**
   * Output only. [Output Only] Creation timestamp in3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * @param GRPCHealthCheck $grpcHealthCheck
   */
  public function setGrpcHealthCheck(GRPCHealthCheck $grpcHealthCheck)
  {
    $this->grpcHealthCheck = $grpcHealthCheck;
  }
  /**
   * @return GRPCHealthCheck
   */
  public function getGrpcHealthCheck()
  {
    return $this->grpcHealthCheck;
  }
  /**
   * @param GRPCTLSHealthCheck $grpcTlsHealthCheck
   */
  public function setGrpcTlsHealthCheck(GRPCTLSHealthCheck $grpcTlsHealthCheck)
  {
    $this->grpcTlsHealthCheck = $grpcTlsHealthCheck;
  }
  /**
   * @return GRPCTLSHealthCheck
   */
  public function getGrpcTlsHealthCheck()
  {
    return $this->grpcTlsHealthCheck;
  }
  /**
   * A so-far unhealthy instance will be marked healthy after this many
   * consecutive successes. The default value is 2.
   *
   * @param int $healthyThreshold
   */
  public function setHealthyThreshold($healthyThreshold)
  {
    $this->healthyThreshold = $healthyThreshold;
  }
  /**
   * @return int
   */
  public function getHealthyThreshold()
  {
    return $this->healthyThreshold;
  }
  /**
   * @param HTTP2HealthCheck $http2HealthCheck
   */
  public function setHttp2HealthCheck(HTTP2HealthCheck $http2HealthCheck)
  {
    $this->http2HealthCheck = $http2HealthCheck;
  }
  /**
   * @return HTTP2HealthCheck
   */
  public function getHttp2HealthCheck()
  {
    return $this->http2HealthCheck;
  }
  /**
   * @param HTTPHealthCheck $httpHealthCheck
   */
  public function setHttpHealthCheck(HTTPHealthCheck $httpHealthCheck)
  {
    $this->httpHealthCheck = $httpHealthCheck;
  }
  /**
   * @return HTTPHealthCheck
   */
  public function getHttpHealthCheck()
  {
    return $this->httpHealthCheck;
  }
  /**
   * @param HTTPSHealthCheck $httpsHealthCheck
   */
  public function setHttpsHealthCheck(HTTPSHealthCheck $httpsHealthCheck)
  {
    $this->httpsHealthCheck = $httpsHealthCheck;
  }
  /**
   * @return HTTPSHealthCheck
   */
  public function getHttpsHealthCheck()
  {
    return $this->httpsHealthCheck;
  }
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
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
   * Output only. Type of the resource.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Configure logging on this health check.
   *
   * @param HealthCheckLogConfig $logConfig
   */
  public function setLogConfig(HealthCheckLogConfig $logConfig)
  {
    $this->logConfig = $logConfig;
  }
  /**
   * @return HealthCheckLogConfig
   */
  public function getLogConfig()
  {
    return $this->logConfig;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035. For example,
   * a name that is 1-63 characters long, matches the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?`, and otherwise complies with RFC1035. This
   * regular expression describes a name where the first character is a
   * lowercase letter, and all following characters are a dash, lowercase
   * letter, or digit, except the last character, which isn't a dash.
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
  /**
   * Output only. [Output Only] Region where the health check resides.  Not
   * applicable to global health checks.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The list of cloud regions from which health checks are performed. If any
   * regions are specified, then exactly 3 regions should be specified. The
   * region names must be valid names of Google Cloud regions. This can only be
   * set for global health check. If this list is non-empty, then there are
   * restrictions on what other health check fields are supported and what other
   * resources can use this health check:        - SSL, HTTP2, and GRPC
   * protocols are not supported.    - The TCP request field is not supported.
   * - The proxyHeader field for HTTP, HTTPS, and TCP is not    supported.    -
   * The checkIntervalSec field must be at least 30.    - The health check
   * cannot be used with BackendService nor with managed    instance group auto-
   * healing.
   *
   * @param string[] $sourceRegions
   */
  public function setSourceRegions($sourceRegions)
  {
    $this->sourceRegions = $sourceRegions;
  }
  /**
   * @return string[]
   */
  public function getSourceRegions()
  {
    return $this->sourceRegions;
  }
  /**
   * @param SSLHealthCheck $sslHealthCheck
   */
  public function setSslHealthCheck(SSLHealthCheck $sslHealthCheck)
  {
    $this->sslHealthCheck = $sslHealthCheck;
  }
  /**
   * @return SSLHealthCheck
   */
  public function getSslHealthCheck()
  {
    return $this->sslHealthCheck;
  }
  /**
   * @param TCPHealthCheck $tcpHealthCheck
   */
  public function setTcpHealthCheck(TCPHealthCheck $tcpHealthCheck)
  {
    $this->tcpHealthCheck = $tcpHealthCheck;
  }
  /**
   * @return TCPHealthCheck
   */
  public function getTcpHealthCheck()
  {
    return $this->tcpHealthCheck;
  }
  /**
   * How long (in seconds) to wait before claiming failure. The default value is
   * 5 seconds. It is invalid for timeoutSec to have greater value than
   * checkIntervalSec.
   *
   * @param int $timeoutSec
   */
  public function setTimeoutSec($timeoutSec)
  {
    $this->timeoutSec = $timeoutSec;
  }
  /**
   * @return int
   */
  public function getTimeoutSec()
  {
    return $this->timeoutSec;
  }
  /**
   * Specifies the type of the healthCheck, either TCP,SSL, HTTP, HTTPS,HTTP2 or
   * GRPC. Exactly one of the protocol-specific health check fields must be
   * specified, which must matchtype field.
   *
   * Accepted values: GRPC, GRPC_WITH_TLS, HTTP, HTTP2, HTTPS, INVALID, SSL, TCP
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * A so-far healthy instance will be marked unhealthy after this many
   * consecutive failures. The default value is 2.
   *
   * @param int $unhealthyThreshold
   */
  public function setUnhealthyThreshold($unhealthyThreshold)
  {
    $this->unhealthyThreshold = $unhealthyThreshold;
  }
  /**
   * @return int
   */
  public function getUnhealthyThreshold()
  {
    return $this->unhealthyThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HealthCheck::class, 'Google_Service_Compute_HealthCheck');
