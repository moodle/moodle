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

namespace Google\Service\OracleDatabase;

class DatabaseConnectionStringProfile extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const CONSUMER_GROUP_CONSUMER_GROUP_UNSPECIFIED = 'CONSUMER_GROUP_UNSPECIFIED';
  /**
   * High consumer group.
   */
  public const CONSUMER_GROUP_HIGH = 'HIGH';
  /**
   * Medium consumer group.
   */
  public const CONSUMER_GROUP_MEDIUM = 'MEDIUM';
  /**
   * Low consumer group.
   */
  public const CONSUMER_GROUP_LOW = 'LOW';
  /**
   * TP consumer group.
   */
  public const CONSUMER_GROUP_TP = 'TP';
  /**
   * TPURGENT consumer group.
   */
  public const CONSUMER_GROUP_TPURGENT = 'TPURGENT';
  /**
   * Default unspecified value.
   */
  public const HOST_FORMAT_HOST_FORMAT_UNSPECIFIED = 'HOST_FORMAT_UNSPECIFIED';
  /**
   * FQDN
   */
  public const HOST_FORMAT_FQDN = 'FQDN';
  /**
   * IP
   */
  public const HOST_FORMAT_IP = 'IP';
  /**
   * Default unspecified value.
   */
  public const PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * Tcp
   */
  public const PROTOCOL_TCP = 'TCP';
  /**
   * Tcps
   */
  public const PROTOCOL_TCPS = 'TCPS';
  /**
   * Default unspecified value.
   */
  public const SESSION_MODE_SESSION_MODE_UNSPECIFIED = 'SESSION_MODE_UNSPECIFIED';
  /**
   * Direct
   */
  public const SESSION_MODE_DIRECT = 'DIRECT';
  /**
   * Indirect
   */
  public const SESSION_MODE_INDIRECT = 'INDIRECT';
  /**
   * Default unspecified value.
   */
  public const SYNTAX_FORMAT_SYNTAX_FORMAT_UNSPECIFIED = 'SYNTAX_FORMAT_UNSPECIFIED';
  /**
   * Long
   */
  public const SYNTAX_FORMAT_LONG = 'LONG';
  /**
   * Ezconnect
   */
  public const SYNTAX_FORMAT_EZCONNECT = 'EZCONNECT';
  /**
   * Ezconnectplus
   */
  public const SYNTAX_FORMAT_EZCONNECTPLUS = 'EZCONNECTPLUS';
  /**
   * Default unspecified value.
   */
  public const TLS_AUTHENTICATION_TLS_AUTHENTICATION_UNSPECIFIED = 'TLS_AUTHENTICATION_UNSPECIFIED';
  /**
   * Server
   */
  public const TLS_AUTHENTICATION_SERVER = 'SERVER';
  /**
   * Mutual
   */
  public const TLS_AUTHENTICATION_MUTUAL = 'MUTUAL';
  /**
   * Output only. The current consumer group being used by the connection.
   *
   * @var string
   */
  public $consumerGroup;
  /**
   * Output only. The display name for the database connection.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The host name format being currently used in connection
   * string.
   *
   * @var string
   */
  public $hostFormat;
  /**
   * Output only. This field indicates if the connection string is regional and
   * is only applicable for cross-region Data Guard.
   *
   * @var bool
   */
  public $isRegional;
  /**
   * Output only. The protocol being used by the connection.
   *
   * @var string
   */
  public $protocol;
  /**
   * Output only. The current session mode of the connection.
   *
   * @var string
   */
  public $sessionMode;
  /**
   * Output only. The syntax of the connection string.
   *
   * @var string
   */
  public $syntaxFormat;
  /**
   * Output only. This field indicates the TLS authentication type of the
   * connection.
   *
   * @var string
   */
  public $tlsAuthentication;
  /**
   * Output only. The value of the connection string.
   *
   * @var string
   */
  public $value;

  /**
   * Output only. The current consumer group being used by the connection.
   *
   * Accepted values: CONSUMER_GROUP_UNSPECIFIED, HIGH, MEDIUM, LOW, TP,
   * TPURGENT
   *
   * @param self::CONSUMER_GROUP_* $consumerGroup
   */
  public function setConsumerGroup($consumerGroup)
  {
    $this->consumerGroup = $consumerGroup;
  }
  /**
   * @return self::CONSUMER_GROUP_*
   */
  public function getConsumerGroup()
  {
    return $this->consumerGroup;
  }
  /**
   * Output only. The display name for the database connection.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The host name format being currently used in connection
   * string.
   *
   * Accepted values: HOST_FORMAT_UNSPECIFIED, FQDN, IP
   *
   * @param self::HOST_FORMAT_* $hostFormat
   */
  public function setHostFormat($hostFormat)
  {
    $this->hostFormat = $hostFormat;
  }
  /**
   * @return self::HOST_FORMAT_*
   */
  public function getHostFormat()
  {
    return $this->hostFormat;
  }
  /**
   * Output only. This field indicates if the connection string is regional and
   * is only applicable for cross-region Data Guard.
   *
   * @param bool $isRegional
   */
  public function setIsRegional($isRegional)
  {
    $this->isRegional = $isRegional;
  }
  /**
   * @return bool
   */
  public function getIsRegional()
  {
    return $this->isRegional;
  }
  /**
   * Output only. The protocol being used by the connection.
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, TCP, TCPS
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
   * Output only. The current session mode of the connection.
   *
   * Accepted values: SESSION_MODE_UNSPECIFIED, DIRECT, INDIRECT
   *
   * @param self::SESSION_MODE_* $sessionMode
   */
  public function setSessionMode($sessionMode)
  {
    $this->sessionMode = $sessionMode;
  }
  /**
   * @return self::SESSION_MODE_*
   */
  public function getSessionMode()
  {
    return $this->sessionMode;
  }
  /**
   * Output only. The syntax of the connection string.
   *
   * Accepted values: SYNTAX_FORMAT_UNSPECIFIED, LONG, EZCONNECT, EZCONNECTPLUS
   *
   * @param self::SYNTAX_FORMAT_* $syntaxFormat
   */
  public function setSyntaxFormat($syntaxFormat)
  {
    $this->syntaxFormat = $syntaxFormat;
  }
  /**
   * @return self::SYNTAX_FORMAT_*
   */
  public function getSyntaxFormat()
  {
    return $this->syntaxFormat;
  }
  /**
   * Output only. This field indicates the TLS authentication type of the
   * connection.
   *
   * Accepted values: TLS_AUTHENTICATION_UNSPECIFIED, SERVER, MUTUAL
   *
   * @param self::TLS_AUTHENTICATION_* $tlsAuthentication
   */
  public function setTlsAuthentication($tlsAuthentication)
  {
    $this->tlsAuthentication = $tlsAuthentication;
  }
  /**
   * @return self::TLS_AUTHENTICATION_*
   */
  public function getTlsAuthentication()
  {
    return $this->tlsAuthentication;
  }
  /**
   * Output only. The value of the connection string.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseConnectionStringProfile::class, 'Google_Service_OracleDatabase_DatabaseConnectionStringProfile');
