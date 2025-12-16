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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoCloudLoggingDetails extends \Google\Model
{
  /**
   * Unspecified
   */
  public const CLOUD_LOGGING_SEVERITY_CLOUD_LOGGING_SEVERITY_UNSPECIFIED = 'CLOUD_LOGGING_SEVERITY_UNSPECIFIED';
  /**
   * If Severity selected is `INFO`, then all the Integration Execution States
   * (`IN_PROCESS`, `ON_HOLD`, `SUCCEEDED`, `SUSPENDED`, `ERROR`, `CANCELLED`)
   * will be sent to Cloud Logging.
   */
  public const CLOUD_LOGGING_SEVERITY_INFO = 'INFO';
  /**
   * If Severity selected is `ERROR`, then only the following Integration
   * Execution States (`ERROR`, `CANCELLED`) will be sent to Cloud Logging.
   */
  public const CLOUD_LOGGING_SEVERITY_ERROR = 'ERROR';
  /**
   * If Severity selected is `WARNING`, then only the following Integration
   * Execution States (`ERROR`, `CANCELLED`) will be sent to Cloud Logging.
   */
  public const CLOUD_LOGGING_SEVERITY_WARNING = 'WARNING';
  /**
   * Severity selected by the customer for the logs to be sent to Cloud Logging,
   * for the integration version getting executed.
   *
   * @var string
   */
  public $cloudLoggingSeverity;
  /**
   * Status of whether Cloud Logging is enabled or not for the integration
   * version getting executed.
   *
   * @var bool
   */
  public $enableCloudLogging;

  /**
   * Severity selected by the customer for the logs to be sent to Cloud Logging,
   * for the integration version getting executed.
   *
   * Accepted values: CLOUD_LOGGING_SEVERITY_UNSPECIFIED, INFO, ERROR, WARNING
   *
   * @param self::CLOUD_LOGGING_SEVERITY_* $cloudLoggingSeverity
   */
  public function setCloudLoggingSeverity($cloudLoggingSeverity)
  {
    $this->cloudLoggingSeverity = $cloudLoggingSeverity;
  }
  /**
   * @return self::CLOUD_LOGGING_SEVERITY_*
   */
  public function getCloudLoggingSeverity()
  {
    return $this->cloudLoggingSeverity;
  }
  /**
   * Status of whether Cloud Logging is enabled or not for the integration
   * version getting executed.
   *
   * @param bool $enableCloudLogging
   */
  public function setEnableCloudLogging($enableCloudLogging)
  {
    $this->enableCloudLogging = $enableCloudLogging;
  }
  /**
   * @return bool
   */
  public function getEnableCloudLogging()
  {
    return $this->enableCloudLogging;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoCloudLoggingDetails::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoCloudLoggingDetails');
