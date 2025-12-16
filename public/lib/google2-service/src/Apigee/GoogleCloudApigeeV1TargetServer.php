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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1TargetServer extends \Google\Model
{
  /**
   * UNSPECIFIED defaults to HTTP for backwards compatibility.
   */
  public const PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * The TargetServer uses HTTP.
   */
  public const PROTOCOL_HTTP = 'HTTP';
  /**
   * The TargetSever uses HTTP2.
   */
  public const PROTOCOL_HTTP2 = 'HTTP2';
  /**
   * The TargetServer uses GRPC.
   */
  public const PROTOCOL_GRPC_TARGET = 'GRPC_TARGET';
  /**
   * GRPC TargetServer to be used in ExternalCallout Policy. Prefer to use
   * EXTERNAL_CALLOUT instead. TODO(b/266125112) deprecate once EXTERNAL
   * _CALLOUT generally available.
   */
  public const PROTOCOL_GRPC = 'GRPC';
  /**
   * The TargetServer is to be used in the ExternalCallout Policy
   */
  public const PROTOCOL_EXTERNAL_CALLOUT = 'EXTERNAL_CALLOUT';
  /**
   * Optional. A human-readable description of this TargetServer.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The host name this target connects to. Value must be a valid
   * hostname as described by RFC-1123.
   *
   * @var string
   */
  public $host;
  /**
   * Optional. Enabling/disabling a TargetServer is useful when TargetServers
   * are used in load balancing configurations, and one or more TargetServers
   * need to taken out of rotation periodically. Defaults to true.
   *
   * @var bool
   */
  public $isEnabled;
  /**
   * Required. The resource id of this target server. Values must match the
   * regular expression
   *
   * @var string
   */
  public $name;
  /**
   * Required. The port number this target connects to on the given host. Value
   * must be between 1 and 65535, inclusive.
   *
   * @var int
   */
  public $port;
  /**
   * Immutable. The protocol used by this TargetServer.
   *
   * @var string
   */
  public $protocol;
  protected $sSLInfoType = GoogleCloudApigeeV1TlsInfo::class;
  protected $sSLInfoDataType = '';

  /**
   * Optional. A human-readable description of this TargetServer.
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
   * Required. The host name this target connects to. Value must be a valid
   * hostname as described by RFC-1123.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Optional. Enabling/disabling a TargetServer is useful when TargetServers
   * are used in load balancing configurations, and one or more TargetServers
   * need to taken out of rotation periodically. Defaults to true.
   *
   * @param bool $isEnabled
   */
  public function setIsEnabled($isEnabled)
  {
    $this->isEnabled = $isEnabled;
  }
  /**
   * @return bool
   */
  public function getIsEnabled()
  {
    return $this->isEnabled;
  }
  /**
   * Required. The resource id of this target server. Values must match the
   * regular expression
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
   * Required. The port number this target connects to on the given host. Value
   * must be between 1 and 65535, inclusive.
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
   * Immutable. The protocol used by this TargetServer.
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, HTTP, HTTP2, GRPC_TARGET, GRPC,
   * EXTERNAL_CALLOUT
   *
   * @param self::PROTOCOL_* $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return self::PROTOCOL_*
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Optional. Specifies TLS configuration info for this TargetServer. The JSON
   * name is `sSLInfo` for legacy/backwards compatibility reasons -- Edge
   * originally supported SSL, and the name is still used for TLS configuration.
   *
   * @param GoogleCloudApigeeV1TlsInfo $sSLInfo
   */
  public function setSSLInfo(GoogleCloudApigeeV1TlsInfo $sSLInfo)
  {
    $this->sSLInfo = $sSLInfo;
  }
  /**
   * @return GoogleCloudApigeeV1TlsInfo
   */
  public function getSSLInfo()
  {
    return $this->sSLInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1TargetServer::class, 'Google_Service_Apigee_GoogleCloudApigeeV1TargetServer');
