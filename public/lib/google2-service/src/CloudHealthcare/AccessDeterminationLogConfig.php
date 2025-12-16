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

namespace Google\Service\CloudHealthcare;

class AccessDeterminationLogConfig extends \Google\Model
{
  /**
   * No log level specified. This value is unused.
   */
  public const LOG_LEVEL_LOG_LEVEL_UNSPECIFIED = 'LOG_LEVEL_UNSPECIFIED';
  /**
   * No additional consent-related logging is added to audit logs.
   */
  public const LOG_LEVEL_DISABLED = 'DISABLED';
  /**
   * The following information is included: * One of the following
   * [`consentMode`](https://cloud.google.com/healthcare-api/docs/fhir-
   * consent#audit_logs) fields: (`off`|`emptyScope`|`enforced`|`btg`|`bypass`).
   * * The accessor's request headers * The `log_level` of the
   * AccessDeterminationLogConfig * The final consent evaluation (`PERMIT`,
   * `DENY`, or `NO_CONSENT`) * A human-readable summary of the evaluation
   */
  public const LOG_LEVEL_MINIMUM = 'MINIMUM';
  /**
   * Includes `MINIMUM` and, for each resource owner, returns: * The resource
   * owner's name * Most specific part of the `X-Consent-Scope` resulting in
   * consensual determination * Timestamp of the applied enforcement leading to
   * the decision * Enforcement version at the time the applicable consents were
   * applied * The Consent resource name * The timestamp of the Consent resource
   * used for enforcement * Policy type (`PATIENT` or `ADMIN`) Due to the
   * limited space for logging, this mode is the same as `MINIMUM` for methods
   * that return multiple resources (such as FHIR Search).
   */
  public const LOG_LEVEL_VERBOSE = 'VERBOSE';
  /**
   * Optional. Controls the amount of detail to include as part of the audit
   * logs.
   *
   * @var string
   */
  public $logLevel;

  /**
   * Optional. Controls the amount of detail to include as part of the audit
   * logs.
   *
   * Accepted values: LOG_LEVEL_UNSPECIFIED, DISABLED, MINIMUM, VERBOSE
   *
   * @param self::LOG_LEVEL_* $logLevel
   */
  public function setLogLevel($logLevel)
  {
    $this->logLevel = $logLevel;
  }
  /**
   * @return self::LOG_LEVEL_*
   */
  public function getLogLevel()
  {
    return $this->logLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessDeterminationLogConfig::class, 'Google_Service_CloudHealthcare_AccessDeterminationLogConfig');
