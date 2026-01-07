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

namespace Google\Service\NetworkSecurity;

class AntivirusOverride extends \Google\Model
{
  /**
   * Threat action not specified.
   */
  public const ACTION_THREAT_ACTION_UNSPECIFIED = 'THREAT_ACTION_UNSPECIFIED';
  /**
   * The default action (as specified by the vendor) is taken.
   */
  public const ACTION_DEFAULT_ACTION = 'DEFAULT_ACTION';
  /**
   * The packet matching this rule will be allowed to transmit.
   */
  public const ACTION_ALLOW = 'ALLOW';
  /**
   * The packet matching this rule will be allowed to transmit, but a threat_log
   * entry will be sent to the consumer project.
   */
  public const ACTION_ALERT = 'ALERT';
  /**
   * The packet matching this rule will be dropped, and a threat_log entry will
   * be sent to the consumer project.
   */
  public const ACTION_DENY = 'DENY';
  /**
   * Protocol not specified.
   */
  public const PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * SMTP protocol
   */
  public const PROTOCOL_SMTP = 'SMTP';
  /**
   * SMB protocol
   */
  public const PROTOCOL_SMB = 'SMB';
  /**
   * POP3 protocol
   */
  public const PROTOCOL_POP3 = 'POP3';
  /**
   * IMAP protocol
   */
  public const PROTOCOL_IMAP = 'IMAP';
  /**
   * HTTP2 protocol
   */
  public const PROTOCOL_HTTP2 = 'HTTP2';
  /**
   * HTTP protocol
   */
  public const PROTOCOL_HTTP = 'HTTP';
  /**
   * FTP protocol
   */
  public const PROTOCOL_FTP = 'FTP';
  /**
   * Required. Threat action override. For some threat types, only a subset of
   * actions applies.
   *
   * @var string
   */
  public $action;
  /**
   * Required. Protocol to match.
   *
   * @var string
   */
  public $protocol;

  /**
   * Required. Threat action override. For some threat types, only a subset of
   * actions applies.
   *
   * Accepted values: THREAT_ACTION_UNSPECIFIED, DEFAULT_ACTION, ALLOW, ALERT,
   * DENY
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Required. Protocol to match.
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, SMTP, SMB, POP3, IMAP, HTTP2, HTTP,
   * FTP
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AntivirusOverride::class, 'Google_Service_NetworkSecurity_AntivirusOverride');
