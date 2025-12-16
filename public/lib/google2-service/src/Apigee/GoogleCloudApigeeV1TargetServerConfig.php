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

class GoogleCloudApigeeV1TargetServerConfig extends \Google\Model
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
   * Whether the target server is enabled. An empty/omitted value for this field
   * should be interpreted as true.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Host name of the target server.
   *
   * @var string
   */
  public $host;
  /**
   * Target server revision name in the following format: `organizations/{org}/e
   * nvironments/{env}/targetservers/{targetserver}/revisions/{rev}`
   *
   * @var string
   */
  public $name;
  /**
   * Port number for the target server.
   *
   * @var int
   */
  public $port;
  /**
   * The protocol used by this target server.
   *
   * @var string
   */
  public $protocol;
  protected $tlsInfoType = GoogleCloudApigeeV1TlsInfoConfig::class;
  protected $tlsInfoDataType = '';

  /**
   * Whether the target server is enabled. An empty/omitted value for this field
   * should be interpreted as true.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Host name of the target server.
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
   * Target server revision name in the following format: `organizations/{org}/e
   * nvironments/{env}/targetservers/{targetserver}/revisions/{rev}`
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
   * Port number for the target server.
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
   * The protocol used by this target server.
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
   * TLS settings for the target server.
   *
   * @param GoogleCloudApigeeV1TlsInfoConfig $tlsInfo
   */
  public function setTlsInfo(GoogleCloudApigeeV1TlsInfoConfig $tlsInfo)
  {
    $this->tlsInfo = $tlsInfo;
  }
  /**
   * @return GoogleCloudApigeeV1TlsInfoConfig
   */
  public function getTlsInfo()
  {
    return $this->tlsInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1TargetServerConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1TargetServerConfig');
