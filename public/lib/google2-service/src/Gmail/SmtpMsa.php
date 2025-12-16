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

namespace Google\Service\Gmail;

class SmtpMsa extends \Google\Model
{
  /**
   * Unspecified security mode.
   */
  public const SECURITY_MODE_securityModeUnspecified = 'securityModeUnspecified';
  /**
   * Communication with the remote SMTP service is unsecured. Requires port 25.
   */
  public const SECURITY_MODE_none = 'none';
  /**
   * Communication with the remote SMTP service is secured using SSL.
   */
  public const SECURITY_MODE_ssl = 'ssl';
  /**
   * Communication with the remote SMTP service is secured using STARTTLS.
   */
  public const SECURITY_MODE_starttls = 'starttls';
  /**
   * The hostname of the SMTP service. Required.
   *
   * @var string
   */
  public $host;
  /**
   * The password that will be used for authentication with the SMTP service.
   * This is a write-only field that can be specified in requests to create or
   * update SendAs settings; it is never populated in responses.
   *
   * @var string
   */
  public $password;
  /**
   * The port of the SMTP service. Required.
   *
   * @var int
   */
  public $port;
  /**
   * The protocol that will be used to secure communication with the SMTP
   * service. Required.
   *
   * @var string
   */
  public $securityMode;
  /**
   * The username that will be used for authentication with the SMTP service.
   * This is a write-only field that can be specified in requests to create or
   * update SendAs settings; it is never populated in responses.
   *
   * @var string
   */
  public $username;

  /**
   * The hostname of the SMTP service. Required.
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
   * The password that will be used for authentication with the SMTP service.
   * This is a write-only field that can be specified in requests to create or
   * update SendAs settings; it is never populated in responses.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * The port of the SMTP service. Required.
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
   * The protocol that will be used to secure communication with the SMTP
   * service. Required.
   *
   * Accepted values: securityModeUnspecified, none, ssl, starttls
   *
   * @param self::SECURITY_MODE_* $securityMode
   */
  public function setSecurityMode($securityMode)
  {
    $this->securityMode = $securityMode;
  }
  /**
   * @return self::SECURITY_MODE_*
   */
  public function getSecurityMode()
  {
    return $this->securityMode;
  }
  /**
   * The username that will be used for authentication with the SMTP service.
   * This is a write-only field that can be specified in requests to create or
   * update SendAs settings; it is never populated in responses.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SmtpMsa::class, 'Google_Service_Gmail_SmtpMsa');
