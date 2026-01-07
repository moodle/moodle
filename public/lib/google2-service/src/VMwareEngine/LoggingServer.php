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

namespace Google\Service\VMwareEngine;

class LoggingServer extends \Google\Model
{
  /**
   * Unspecified communications protocol. This is the default value.
   */
  public const PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * UDP
   */
  public const PROTOCOL_UDP = 'UDP';
  /**
   * TCP
   */
  public const PROTOCOL_TCP = 'TCP';
  /**
   * TLS
   */
  public const PROTOCOL_TLS = 'TLS';
  /**
   * SSL
   */
  public const PROTOCOL_SSL = 'SSL';
  /**
   * RELP
   */
  public const PROTOCOL_RELP = 'RELP';
  /**
   * The default value. This value should never be used.
   */
  public const SOURCE_TYPE_SOURCE_TYPE_UNSPECIFIED = 'SOURCE_TYPE_UNSPECIFIED';
  /**
   * Logs produced by ESXI hosts
   */
  public const SOURCE_TYPE_ESXI = 'ESXI';
  /**
   * Logs produced by vCenter server
   */
  public const SOURCE_TYPE_VCSA = 'VCSA';
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Fully-qualified domain name (FQDN) or IP Address of the logging
   * server.
   *
   * @var string
   */
  public $hostname;
  /**
   * Output only. The resource name of this logging server. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/loggingServers/my-logging-server`
   *
   * @var string
   */
  public $name;
  /**
   * Required. Port number at which the logging server receives logs.
   *
   * @var int
   */
  public $port;
  /**
   * Required. Protocol used by vCenter to send logs to a logging server.
   *
   * @var string
   */
  public $protocol;
  /**
   * Required. The type of component that produces logs that will be forwarded
   * to this logging server.
   *
   * @var string
   */
  public $sourceType;
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Creation time of this resource.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. Fully-qualified domain name (FQDN) or IP Address of the logging
   * server.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Output only. The resource name of this logging server. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/loggingServers/my-logging-server`
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
   * Required. Port number at which the logging server receives logs.
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
   * Required. Protocol used by vCenter to send logs to a logging server.
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, UDP, TCP, TLS, SSL, RELP
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
   * Required. The type of component that produces logs that will be forwarded
   * to this logging server.
   *
   * Accepted values: SOURCE_TYPE_UNSPECIFIED, ESXI, VCSA
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Last update time of this resource.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoggingServer::class, 'Google_Service_VMwareEngine_LoggingServer');
