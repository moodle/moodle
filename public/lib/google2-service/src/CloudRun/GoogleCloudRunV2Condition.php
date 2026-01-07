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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2Condition extends \Google\Model
{
  /**
   * Default value.
   */
  public const EXECUTION_REASON_EXECUTION_REASON_UNDEFINED = 'EXECUTION_REASON_UNDEFINED';
  /**
   * Internal system error getting execution status. System will retry.
   */
  public const EXECUTION_REASON_JOB_STATUS_SERVICE_POLLING_ERROR = 'JOB_STATUS_SERVICE_POLLING_ERROR';
  /**
   * A task reached its retry limit and the last attempt failed due to the user
   * container exiting with a non-zero exit code.
   */
  public const EXECUTION_REASON_NON_ZERO_EXIT_CODE = 'NON_ZERO_EXIT_CODE';
  /**
   * The execution was cancelled by users.
   */
  public const EXECUTION_REASON_CANCELLED = 'CANCELLED';
  /**
   * The execution is in the process of being cancelled.
   */
  public const EXECUTION_REASON_CANCELLING = 'CANCELLING';
  /**
   * The execution was deleted.
   */
  public const EXECUTION_REASON_DELETED = 'DELETED';
  /**
   * A delayed execution is waiting for a start time.
   */
  public const EXECUTION_REASON_DELAYED_START_PENDING = 'DELAYED_START_PENDING';
  /**
   * Default value.
   */
  public const REASON_COMMON_REASON_UNDEFINED = 'COMMON_REASON_UNDEFINED';
  /**
   * Reason unknown. Further details will be in message.
   */
  public const REASON_UNKNOWN = 'UNKNOWN';
  /**
   * Revision creation process failed.
   */
  public const REASON_REVISION_FAILED = 'REVISION_FAILED';
  /**
   * Timed out waiting for completion.
   */
  public const REASON_PROGRESS_DEADLINE_EXCEEDED = 'PROGRESS_DEADLINE_EXCEEDED';
  /**
   * The container image path is incorrect.
   */
  public const REASON_CONTAINER_MISSING = 'CONTAINER_MISSING';
  /**
   * Insufficient permissions on the container image.
   */
  public const REASON_CONTAINER_PERMISSION_DENIED = 'CONTAINER_PERMISSION_DENIED';
  /**
   * Container image is not authorized by policy.
   */
  public const REASON_CONTAINER_IMAGE_UNAUTHORIZED = 'CONTAINER_IMAGE_UNAUTHORIZED';
  /**
   * Container image policy authorization check failed.
   */
  public const REASON_CONTAINER_IMAGE_AUTHORIZATION_CHECK_FAILED = 'CONTAINER_IMAGE_AUTHORIZATION_CHECK_FAILED';
  /**
   * Insufficient permissions on encryption key.
   */
  public const REASON_ENCRYPTION_KEY_PERMISSION_DENIED = 'ENCRYPTION_KEY_PERMISSION_DENIED';
  /**
   * Permission check on encryption key failed.
   */
  public const REASON_ENCRYPTION_KEY_CHECK_FAILED = 'ENCRYPTION_KEY_CHECK_FAILED';
  /**
   * At least one Access check on secrets failed.
   */
  public const REASON_SECRETS_ACCESS_CHECK_FAILED = 'SECRETS_ACCESS_CHECK_FAILED';
  /**
   * Waiting for operation to complete.
   */
  public const REASON_WAITING_FOR_OPERATION = 'WAITING_FOR_OPERATION';
  /**
   * System will retry immediately.
   */
  public const REASON_IMMEDIATE_RETRY = 'IMMEDIATE_RETRY';
  /**
   * System will retry later; current attempt failed.
   */
  public const REASON_POSTPONED_RETRY = 'POSTPONED_RETRY';
  /**
   * An internal error occurred. Further information may be in the message.
   */
  public const REASON_INTERNAL = 'INTERNAL';
  /**
   * User-provided VPC network was not found.
   */
  public const REASON_VPC_NETWORK_NOT_FOUND = 'VPC_NETWORK_NOT_FOUND';
  /**
   * Default value.
   */
  public const REVISION_REASON_REVISION_REASON_UNDEFINED = 'REVISION_REASON_UNDEFINED';
  /**
   * Revision in Pending state.
   */
  public const REVISION_REASON_PENDING = 'PENDING';
  /**
   * Revision is in Reserve state.
   */
  public const REVISION_REASON_RESERVE = 'RESERVE';
  /**
   * Revision is Retired.
   */
  public const REVISION_REASON_RETIRED = 'RETIRED';
  /**
   * Revision is being retired.
   */
  public const REVISION_REASON_RETIRING = 'RETIRING';
  /**
   * Revision is being recreated.
   */
  public const REVISION_REASON_RECREATING = 'RECREATING';
  /**
   * There was a health check error.
   */
  public const REVISION_REASON_HEALTH_CHECK_CONTAINER_ERROR = 'HEALTH_CHECK_CONTAINER_ERROR';
  /**
   * Health check failed due to user error from customized path of the
   * container. System will retry.
   */
  public const REVISION_REASON_CUSTOMIZED_PATH_RESPONSE_PENDING = 'CUSTOMIZED_PATH_RESPONSE_PENDING';
  /**
   * A revision with min_instance_count > 0 was created and is reserved, but it
   * was not configured to serve traffic, so it's not live. This can also happen
   * momentarily during traffic migration.
   */
  public const REVISION_REASON_MIN_INSTANCES_NOT_PROVISIONED = 'MIN_INSTANCES_NOT_PROVISIONED';
  /**
   * The maximum allowed number of active revisions has been reached.
   */
  public const REVISION_REASON_ACTIVE_REVISION_LIMIT_REACHED = 'ACTIVE_REVISION_LIMIT_REACHED';
  /**
   * There was no deployment defined. This value is no longer used, but Services
   * created in older versions of the API might contain this value.
   */
  public const REVISION_REASON_NO_DEPLOYMENT = 'NO_DEPLOYMENT';
  /**
   * A revision's container has no port specified since the revision is of a
   * manually scaled service with 0 instance count
   */
  public const REVISION_REASON_HEALTH_CHECK_SKIPPED = 'HEALTH_CHECK_SKIPPED';
  /**
   * A revision with min_instance_count > 0 was created and is waiting for
   * enough instances to begin a traffic migration.
   */
  public const REVISION_REASON_MIN_INSTANCES_WARMING = 'MIN_INSTANCES_WARMING';
  /**
   * Unspecified severity
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Error severity.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Warning severity.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Info severity.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Transient state: Reconciliation has not started yet.
   */
  public const STATE_CONDITION_PENDING = 'CONDITION_PENDING';
  /**
   * Transient state: reconciliation is still in progress.
   */
  public const STATE_CONDITION_RECONCILING = 'CONDITION_RECONCILING';
  /**
   * Terminal state: Reconciliation did not succeed.
   */
  public const STATE_CONDITION_FAILED = 'CONDITION_FAILED';
  /**
   * Terminal state: Reconciliation completed successfully.
   */
  public const STATE_CONDITION_SUCCEEDED = 'CONDITION_SUCCEEDED';
  /**
   * Output only. A reason for the execution condition.
   *
   * @var string
   */
  public $executionReason;
  /**
   * Last time the condition transitioned from one status to another.
   *
   * @var string
   */
  public $lastTransitionTime;
  /**
   * Human readable message indicating details about the current status.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. A common (service-level) reason for this condition.
   *
   * @var string
   */
  public $reason;
  /**
   * Output only. A reason for the revision condition.
   *
   * @var string
   */
  public $revisionReason;
  /**
   * How to interpret failures of this condition, one of Error, Warning, Info
   *
   * @var string
   */
  public $severity;
  /**
   * State of the condition.
   *
   * @var string
   */
  public $state;
  /**
   * type is used to communicate the status of the reconciliation process. See
   * also:
   * https://github.com/knative/serving/blob/main/docs/spec/errors.md#error-
   * conditions-and-reporting Types common to all resources include: * "Ready":
   * True when the Resource is ready.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. A reason for the execution condition.
   *
   * Accepted values: EXECUTION_REASON_UNDEFINED,
   * JOB_STATUS_SERVICE_POLLING_ERROR, NON_ZERO_EXIT_CODE, CANCELLED,
   * CANCELLING, DELETED, DELAYED_START_PENDING
   *
   * @param self::EXECUTION_REASON_* $executionReason
   */
  public function setExecutionReason($executionReason)
  {
    $this->executionReason = $executionReason;
  }
  /**
   * @return self::EXECUTION_REASON_*
   */
  public function getExecutionReason()
  {
    return $this->executionReason;
  }
  /**
   * Last time the condition transitioned from one status to another.
   *
   * @param string $lastTransitionTime
   */
  public function setLastTransitionTime($lastTransitionTime)
  {
    $this->lastTransitionTime = $lastTransitionTime;
  }
  /**
   * @return string
   */
  public function getLastTransitionTime()
  {
    return $this->lastTransitionTime;
  }
  /**
   * Human readable message indicating details about the current status.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Output only. A common (service-level) reason for this condition.
   *
   * Accepted values: COMMON_REASON_UNDEFINED, UNKNOWN, REVISION_FAILED,
   * PROGRESS_DEADLINE_EXCEEDED, CONTAINER_MISSING, CONTAINER_PERMISSION_DENIED,
   * CONTAINER_IMAGE_UNAUTHORIZED, CONTAINER_IMAGE_AUTHORIZATION_CHECK_FAILED,
   * ENCRYPTION_KEY_PERMISSION_DENIED, ENCRYPTION_KEY_CHECK_FAILED,
   * SECRETS_ACCESS_CHECK_FAILED, WAITING_FOR_OPERATION, IMMEDIATE_RETRY,
   * POSTPONED_RETRY, INTERNAL, VPC_NETWORK_NOT_FOUND
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Output only. A reason for the revision condition.
   *
   * Accepted values: REVISION_REASON_UNDEFINED, PENDING, RESERVE, RETIRED,
   * RETIRING, RECREATING, HEALTH_CHECK_CONTAINER_ERROR,
   * CUSTOMIZED_PATH_RESPONSE_PENDING, MIN_INSTANCES_NOT_PROVISIONED,
   * ACTIVE_REVISION_LIMIT_REACHED, NO_DEPLOYMENT, HEALTH_CHECK_SKIPPED,
   * MIN_INSTANCES_WARMING
   *
   * @param self::REVISION_REASON_* $revisionReason
   */
  public function setRevisionReason($revisionReason)
  {
    $this->revisionReason = $revisionReason;
  }
  /**
   * @return self::REVISION_REASON_*
   */
  public function getRevisionReason()
  {
    return $this->revisionReason;
  }
  /**
   * How to interpret failures of this condition, one of Error, Warning, Info
   *
   * Accepted values: SEVERITY_UNSPECIFIED, ERROR, WARNING, INFO
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * State of the condition.
   *
   * Accepted values: STATE_UNSPECIFIED, CONDITION_PENDING,
   * CONDITION_RECONCILING, CONDITION_FAILED, CONDITION_SUCCEEDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * type is used to communicate the status of the reconciliation process. See
   * also:
   * https://github.com/knative/serving/blob/main/docs/spec/errors.md#error-
   * conditions-and-reporting Types common to all resources include: * "Ready":
   * True when the Resource is ready.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2Condition::class, 'Google_Service_CloudRun_GoogleCloudRunV2Condition');
