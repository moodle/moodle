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

namespace Google\Service\Container;

class StatusCondition extends \Google\Model
{
  /**
   * Not an error; returned on success. HTTP Mapping: 200 OK
   */
  public const CANONICAL_CODE_OK = 'OK';
  /**
   * The operation was cancelled, typically by the caller. HTTP Mapping: 499
   * Client Closed Request
   */
  public const CANONICAL_CODE_CANCELLED = 'CANCELLED';
  /**
   * Unknown error. For example, this error may be returned when a `Status`
   * value received from another address space belongs to an error space that is
   * not known in this address space. Also errors raised by APIs that do not
   * return enough error information may be converted to this error. HTTP
   * Mapping: 500 Internal Server Error
   */
  public const CANONICAL_CODE_UNKNOWN = 'UNKNOWN';
  /**
   * The client specified an invalid argument. Note that this differs from
   * `FAILED_PRECONDITION`. `INVALID_ARGUMENT` indicates arguments that are
   * problematic regardless of the state of the system (e.g., a malformed file
   * name). HTTP Mapping: 400 Bad Request
   */
  public const CANONICAL_CODE_INVALID_ARGUMENT = 'INVALID_ARGUMENT';
  /**
   * The deadline expired before the operation could complete. For operations
   * that change the state of the system, this error may be returned even if the
   * operation has completed successfully. For example, a successful response
   * from a server could have been delayed long enough for the deadline to
   * expire. HTTP Mapping: 504 Gateway Timeout
   */
  public const CANONICAL_CODE_DEADLINE_EXCEEDED = 'DEADLINE_EXCEEDED';
  /**
   * Some requested entity (e.g., file or directory) was not found. Note to
   * server developers: if a request is denied for an entire class of users,
   * such as gradual feature rollout or undocumented allowlist, `NOT_FOUND` may
   * be used. If a request is denied for some users within a class of users,
   * such as user-based access control, `PERMISSION_DENIED` must be used. HTTP
   * Mapping: 404 Not Found
   */
  public const CANONICAL_CODE_NOT_FOUND = 'NOT_FOUND';
  /**
   * The entity that a client attempted to create (e.g., file or directory)
   * already exists. HTTP Mapping: 409 Conflict
   */
  public const CANONICAL_CODE_ALREADY_EXISTS = 'ALREADY_EXISTS';
  /**
   * The caller does not have permission to execute the specified operation.
   * `PERMISSION_DENIED` must not be used for rejections caused by exhausting
   * some resource (use `RESOURCE_EXHAUSTED` instead for those errors).
   * `PERMISSION_DENIED` must not be used if the caller can not be identified
   * (use `UNAUTHENTICATED` instead for those errors). This error code does not
   * imply the request is valid or the requested entity exists or satisfies
   * other pre-conditions. HTTP Mapping: 403 Forbidden
   */
  public const CANONICAL_CODE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * The request does not have valid authentication credentials for the
   * operation. HTTP Mapping: 401 Unauthorized
   */
  public const CANONICAL_CODE_UNAUTHENTICATED = 'UNAUTHENTICATED';
  /**
   * Some resource has been exhausted, perhaps a per-user quota, or perhaps the
   * entire file system is out of space. HTTP Mapping: 429 Too Many Requests
   */
  public const CANONICAL_CODE_RESOURCE_EXHAUSTED = 'RESOURCE_EXHAUSTED';
  /**
   * The operation was rejected because the system is not in a state required
   * for the operation's execution. For example, the directory to be deleted is
   * non-empty, an rmdir operation is applied to a non-directory, etc. Service
   * implementors can use the following guidelines to decide between
   * `FAILED_PRECONDITION`, `ABORTED`, and `UNAVAILABLE`: (a) Use `UNAVAILABLE`
   * if the client can retry just the failing call. (b) Use `ABORTED` if the
   * client should retry at a higher level. For example, when a client-specified
   * test-and-set fails, indicating the client should restart a read-modify-
   * write sequence. (c) Use `FAILED_PRECONDITION` if the client should not
   * retry until the system state has been explicitly fixed. For example, if an
   * "rmdir" fails because the directory is non-empty, `FAILED_PRECONDITION`
   * should be returned since the client should not retry unless the files are
   * deleted from the directory. HTTP Mapping: 400 Bad Request
   */
  public const CANONICAL_CODE_FAILED_PRECONDITION = 'FAILED_PRECONDITION';
  /**
   * The operation was aborted, typically due to a concurrency issue such as a
   * sequencer check failure or transaction abort. See the guidelines above for
   * deciding between `FAILED_PRECONDITION`, `ABORTED`, and `UNAVAILABLE`. HTTP
   * Mapping: 409 Conflict
   */
  public const CANONICAL_CODE_ABORTED = 'ABORTED';
  /**
   * The operation was attempted past the valid range. E.g., seeking or reading
   * past end-of-file. Unlike `INVALID_ARGUMENT`, this error indicates a problem
   * that may be fixed if the system state changes. For example, a 32-bit file
   * system will generate `INVALID_ARGUMENT` if asked to read at an offset that
   * is not in the range [0,2^32-1], but it will generate `OUT_OF_RANGE` if
   * asked to read from an offset past the current file size. There is a fair
   * bit of overlap between `FAILED_PRECONDITION` and `OUT_OF_RANGE`. We
   * recommend using `OUT_OF_RANGE` (the more specific error) when it applies so
   * that callers who are iterating through a space can easily look for an
   * `OUT_OF_RANGE` error to detect when they are done. HTTP Mapping: 400 Bad
   * Request
   */
  public const CANONICAL_CODE_OUT_OF_RANGE = 'OUT_OF_RANGE';
  /**
   * The operation is not implemented or is not supported/enabled in this
   * service. HTTP Mapping: 501 Not Implemented
   */
  public const CANONICAL_CODE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Internal errors. This means that some invariants expected by the underlying
   * system have been broken. This error code is reserved for serious errors.
   * HTTP Mapping: 500 Internal Server Error
   */
  public const CANONICAL_CODE_INTERNAL = 'INTERNAL';
  /**
   * The service is currently unavailable. This is most likely a transient
   * condition, which can be corrected by retrying with a backoff. Note that it
   * is not always safe to retry non-idempotent operations. See the guidelines
   * above for deciding between `FAILED_PRECONDITION`, `ABORTED`, and
   * `UNAVAILABLE`. HTTP Mapping: 503 Service Unavailable
   */
  public const CANONICAL_CODE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Unrecoverable data loss or corruption. HTTP Mapping: 500 Internal Server
   * Error
   */
  public const CANONICAL_CODE_DATA_LOSS = 'DATA_LOSS';
  /**
   * UNKNOWN indicates a generic condition.
   */
  public const CODE_UNKNOWN = 'UNKNOWN';
  /**
   * GCE_STOCKOUT indicates that Google Compute Engine resources are temporarily
   * unavailable.
   */
  public const CODE_GCE_STOCKOUT = 'GCE_STOCKOUT';
  /**
   * GKE_SERVICE_ACCOUNT_DELETED indicates that the user deleted their robot
   * service account.
   */
  public const CODE_GKE_SERVICE_ACCOUNT_DELETED = 'GKE_SERVICE_ACCOUNT_DELETED';
  /**
   * Google Compute Engine quota was exceeded.
   */
  public const CODE_GCE_QUOTA_EXCEEDED = 'GCE_QUOTA_EXCEEDED';
  /**
   * Cluster state was manually changed by an SRE due to a system logic error.
   */
  public const CODE_SET_BY_OPERATOR = 'SET_BY_OPERATOR';
  /**
   * Unable to perform an encrypt operation against the CloudKMS key used for
   * etcd level encryption.
   */
  public const CODE_CLOUD_KMS_KEY_ERROR = 'CLOUD_KMS_KEY_ERROR';
  /**
   * Cluster CA is expiring soon.
   */
  public const CODE_CA_EXPIRING = 'CA_EXPIRING';
  /**
   * Node service account is missing permissions.
   */
  public const CODE_NODE_SERVICE_ACCOUNT_MISSING_PERMISSIONS = 'NODE_SERVICE_ACCOUNT_MISSING_PERMISSIONS';
  /**
   * Cloud KMS key version used for etcd level encryption has been destroyed.
   * This is a permanent error.
   */
  public const CODE_CLOUD_KMS_KEY_DESTROYED = 'CLOUD_KMS_KEY_DESTROYED';
  /**
   * Canonical code of the condition.
   *
   * @var string
   */
  public $canonicalCode;
  /**
   * Machine-friendly representation of the condition Deprecated. Use
   * canonical_code instead.
   *
   * @deprecated
   * @var string
   */
  public $code;
  /**
   * Human-friendly representation of the condition
   *
   * @var string
   */
  public $message;

  /**
   * Canonical code of the condition.
   *
   * Accepted values: OK, CANCELLED, UNKNOWN, INVALID_ARGUMENT,
   * DEADLINE_EXCEEDED, NOT_FOUND, ALREADY_EXISTS, PERMISSION_DENIED,
   * UNAUTHENTICATED, RESOURCE_EXHAUSTED, FAILED_PRECONDITION, ABORTED,
   * OUT_OF_RANGE, UNIMPLEMENTED, INTERNAL, UNAVAILABLE, DATA_LOSS
   *
   * @param self::CANONICAL_CODE_* $canonicalCode
   */
  public function setCanonicalCode($canonicalCode)
  {
    $this->canonicalCode = $canonicalCode;
  }
  /**
   * @return self::CANONICAL_CODE_*
   */
  public function getCanonicalCode()
  {
    return $this->canonicalCode;
  }
  /**
   * Machine-friendly representation of the condition Deprecated. Use
   * canonical_code instead.
   *
   * Accepted values: UNKNOWN, GCE_STOCKOUT, GKE_SERVICE_ACCOUNT_DELETED,
   * GCE_QUOTA_EXCEEDED, SET_BY_OPERATOR, CLOUD_KMS_KEY_ERROR, CA_EXPIRING,
   * NODE_SERVICE_ACCOUNT_MISSING_PERMISSIONS, CLOUD_KMS_KEY_DESTROYED
   *
   * @deprecated
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @deprecated
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Human-friendly representation of the condition
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StatusCondition::class, 'Google_Service_Container_StatusCondition');
