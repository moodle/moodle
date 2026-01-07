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

namespace Google\Service\Dataproc;

class SessionOperationMetadata extends \Google\Collection
{
  /**
   * Session operation type is unknown.
   */
  public const OPERATION_TYPE_SESSION_OPERATION_TYPE_UNSPECIFIED = 'SESSION_OPERATION_TYPE_UNSPECIFIED';
  /**
   * Create Session operation type.
   */
  public const OPERATION_TYPE_CREATE = 'CREATE';
  /**
   * Terminate Session operation type.
   */
  public const OPERATION_TYPE_TERMINATE = 'TERMINATE';
  /**
   * Delete Session operation type.
   */
  public const OPERATION_TYPE_DELETE = 'DELETE';
  protected $collection_key = 'warnings';
  /**
   * The time when the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Short description of the operation.
   *
   * @var string
   */
  public $description;
  /**
   * The time when the operation was finished.
   *
   * @var string
   */
  public $doneTime;
  /**
   * Labels associated with the operation.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The operation type.
   *
   * @var string
   */
  public $operationType;
  /**
   * Name of the session for the operation.
   *
   * @var string
   */
  public $session;
  /**
   * Session UUID for the operation.
   *
   * @var string
   */
  public $sessionUuid;
  /**
   * Warnings encountered during operation execution.
   *
   * @var string[]
   */
  public $warnings;

  /**
   * The time when the operation was created.
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
   * Short description of the operation.
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
   * The time when the operation was finished.
   *
   * @param string $doneTime
   */
  public function setDoneTime($doneTime)
  {
    $this->doneTime = $doneTime;
  }
  /**
   * @return string
   */
  public function getDoneTime()
  {
    return $this->doneTime;
  }
  /**
   * Labels associated with the operation.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The operation type.
   *
   * Accepted values: SESSION_OPERATION_TYPE_UNSPECIFIED, CREATE, TERMINATE,
   * DELETE
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * Name of the session for the operation.
   *
   * @param string $session
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return string
   */
  public function getSession()
  {
    return $this->session;
  }
  /**
   * Session UUID for the operation.
   *
   * @param string $sessionUuid
   */
  public function setSessionUuid($sessionUuid)
  {
    $this->sessionUuid = $sessionUuid;
  }
  /**
   * @return string
   */
  public function getSessionUuid()
  {
    return $this->sessionUuid;
  }
  /**
   * Warnings encountered during operation execution.
   *
   * @param string[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return string[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SessionOperationMetadata::class, 'Google_Service_Dataproc_SessionOperationMetadata');
