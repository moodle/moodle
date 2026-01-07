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

namespace Google\Service\Compute;

class Operation extends \Google\Collection
{
  public const STATUS_DONE = 'DONE';
  public const STATUS_PENDING = 'PENDING';
  public const STATUS_RUNNING = 'RUNNING';
  protected $collection_key = 'warnings';
  /**
   * [Output Only] The value of `requestId` if you provided it in the request.
   * Not present otherwise.
   *
   * @var string
   */
  public $clientOperationId;
  /**
   * [Deprecated] This field is deprecated.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * [Output Only] A textual description of the operation, which is set when the
   * operation is created.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The time that this operation was completed. This value is
   * inRFC3339 text format.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = OperationError::class;
  protected $errorDataType = '';
  /**
   * [Output Only] If the operation fails, this field contains the HTTP error
   * message that was returned, such as `NOT FOUND`.
   *
   * @var string
   */
  public $httpErrorMessage;
  /**
   * [Output Only] If the operation fails, this field contains the HTTP error
   * status code that was returned. For example, a `404` means the resource was
   * not found.
   *
   * @var int
   */
  public $httpErrorStatusCode;
  /**
   * [Output Only] The unique identifier for the operation. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * [Output Only] The time that this operation was requested. This value is
   * inRFC3339 text format.
   *
   * @var string
   */
  public $insertTime;
  protected $instancesBulkInsertOperationMetadataType = InstancesBulkInsertOperationMetadata::class;
  protected $instancesBulkInsertOperationMetadataDataType = '';
  /**
   * Output only. [Output Only] Type of the resource. Always `compute#operation`
   * for Operation resources.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] Name of the operation.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] An ID that represents a group of operations,
   * such as when a group of operations results from a `bulkInsert` API request.
   *
   * @var string
   */
  public $operationGroupId;
  /**
   * [Output Only] The type of operation, such as `insert`, `update`, or
   * `delete`, and so on.
   *
   * @var string
   */
  public $operationType;
  /**
   * [Output Only] An optional progress indicator that ranges from 0 to 100.
   * There is no requirement that this be linear or support any granularity of
   * operations. This should not be used to guess when the operation will be
   * complete. This number should monotonically increase as the operation
   * progresses.
   *
   * @var int
   */
  public $progress;
  /**
   * [Output Only] The URL of the region where the operation resides. Only
   * applicable when performing regional operations.
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $setCommonInstanceMetadataOperationMetadataType = SetCommonInstanceMetadataOperationMetadata::class;
  protected $setCommonInstanceMetadataOperationMetadataDataType = '';
  /**
   * [Output Only] The time that this operation was started by the server. This
   * value is inRFC3339 text format.
   *
   * @var string
   */
  public $startTime;
  /**
   * [Output Only] The status of the operation, which can be one of the
   * following: `PENDING`, `RUNNING`, or `DONE`.
   *
   * @var string
   */
  public $status;
  /**
   * [Output Only] An optional textual description of the current status of the
   * operation.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * [Output Only] The unique target ID, which identifies a specific incarnation
   * of the target resource.
   *
   * @var string
   */
  public $targetId;
  /**
   * [Output Only] The URL of the resource that the operation modifies. For
   * operations related to creating a snapshot, this points to the disk that the
   * snapshot was created from.
   *
   * @var string
   */
  public $targetLink;
  /**
   * [Output Only] User who requested the operation, for example:
   * `user@example.com` or `alice_smith_identifier
   * (global/workforcePools/example-com-us-employees)`.
   *
   * @var string
   */
  public $user;
  protected $warningsType = OperationWarnings::class;
  protected $warningsDataType = 'array';
  /**
   * [Output Only] The URL of the zone where the operation resides. Only
   * applicable when performing per-zone operations.
   *
   * @var string
   */
  public $zone;

  /**
   * [Output Only] The value of `requestId` if you provided it in the request.
   * Not present otherwise.
   *
   * @param string $clientOperationId
   */
  public function setClientOperationId($clientOperationId)
  {
    $this->clientOperationId = $clientOperationId;
  }
  /**
   * @return string
   */
  public function getClientOperationId()
  {
    return $this->clientOperationId;
  }
  /**
   * [Deprecated] This field is deprecated.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * [Output Only] A textual description of the operation, which is set when the
   * operation is created.
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
   * [Output Only] The time that this operation was completed. This value is
   * inRFC3339 text format.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * [Output Only] If errors are generated during processing of the operation,
   * this field will be populated.
   *
   * @param OperationError $error
   */
  public function setError(OperationError $error)
  {
    $this->error = $error;
  }
  /**
   * @return OperationError
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * [Output Only] If the operation fails, this field contains the HTTP error
   * message that was returned, such as `NOT FOUND`.
   *
   * @param string $httpErrorMessage
   */
  public function setHttpErrorMessage($httpErrorMessage)
  {
    $this->httpErrorMessage = $httpErrorMessage;
  }
  /**
   * @return string
   */
  public function getHttpErrorMessage()
  {
    return $this->httpErrorMessage;
  }
  /**
   * [Output Only] If the operation fails, this field contains the HTTP error
   * status code that was returned. For example, a `404` means the resource was
   * not found.
   *
   * @param int $httpErrorStatusCode
   */
  public function setHttpErrorStatusCode($httpErrorStatusCode)
  {
    $this->httpErrorStatusCode = $httpErrorStatusCode;
  }
  /**
   * @return int
   */
  public function getHttpErrorStatusCode()
  {
    return $this->httpErrorStatusCode;
  }
  /**
   * [Output Only] The unique identifier for the operation. This identifier is
   * defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * [Output Only] The time that this operation was requested. This value is
   * inRFC3339 text format.
   *
   * @param string $insertTime
   */
  public function setInsertTime($insertTime)
  {
    $this->insertTime = $insertTime;
  }
  /**
   * @return string
   */
  public function getInsertTime()
  {
    return $this->insertTime;
  }
  /**
   * @param InstancesBulkInsertOperationMetadata $instancesBulkInsertOperationMetadata
   */
  public function setInstancesBulkInsertOperationMetadata(InstancesBulkInsertOperationMetadata $instancesBulkInsertOperationMetadata)
  {
    $this->instancesBulkInsertOperationMetadata = $instancesBulkInsertOperationMetadata;
  }
  /**
   * @return InstancesBulkInsertOperationMetadata
   */
  public function getInstancesBulkInsertOperationMetadata()
  {
    return $this->instancesBulkInsertOperationMetadata;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always `compute#operation`
   * for Operation resources.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * [Output Only] Name of the operation.
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
   * Output only. [Output Only] An ID that represents a group of operations,
   * such as when a group of operations results from a `bulkInsert` API request.
   *
   * @param string $operationGroupId
   */
  public function setOperationGroupId($operationGroupId)
  {
    $this->operationGroupId = $operationGroupId;
  }
  /**
   * @return string
   */
  public function getOperationGroupId()
  {
    return $this->operationGroupId;
  }
  /**
   * [Output Only] The type of operation, such as `insert`, `update`, or
   * `delete`, and so on.
   *
   * @param string $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return string
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * [Output Only] An optional progress indicator that ranges from 0 to 100.
   * There is no requirement that this be linear or support any granularity of
   * operations. This should not be used to guess when the operation will be
   * complete. This number should monotonically increase as the operation
   * progresses.
   *
   * @param int $progress
   */
  public function setProgress($progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return int
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * [Output Only] The URL of the region where the operation resides. Only
   * applicable when performing regional operations.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output Only] If the operation is for
   * projects.setCommonInstanceMetadata, this field will contain information on
   * all underlying zonal actions and their state.
   *
   * @param SetCommonInstanceMetadataOperationMetadata $setCommonInstanceMetadataOperationMetadata
   */
  public function setSetCommonInstanceMetadataOperationMetadata(SetCommonInstanceMetadataOperationMetadata $setCommonInstanceMetadataOperationMetadata)
  {
    $this->setCommonInstanceMetadataOperationMetadata = $setCommonInstanceMetadataOperationMetadata;
  }
  /**
   * @return SetCommonInstanceMetadataOperationMetadata
   */
  public function getSetCommonInstanceMetadataOperationMetadata()
  {
    return $this->setCommonInstanceMetadataOperationMetadata;
  }
  /**
   * [Output Only] The time that this operation was started by the server. This
   * value is inRFC3339 text format.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * [Output Only] The status of the operation, which can be one of the
   * following: `PENDING`, `RUNNING`, or `DONE`.
   *
   * Accepted values: DONE, PENDING, RUNNING
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * [Output Only] An optional textual description of the current status of the
   * operation.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * [Output Only] The unique target ID, which identifies a specific incarnation
   * of the target resource.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
  /**
   * [Output Only] The URL of the resource that the operation modifies. For
   * operations related to creating a snapshot, this points to the disk that the
   * snapshot was created from.
   *
   * @param string $targetLink
   */
  public function setTargetLink($targetLink)
  {
    $this->targetLink = $targetLink;
  }
  /**
   * @return string
   */
  public function getTargetLink()
  {
    return $this->targetLink;
  }
  /**
   * [Output Only] User who requested the operation, for example:
   * `user@example.com` or `alice_smith_identifier
   * (global/workforcePools/example-com-us-employees)`.
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
  /**
   * [Output Only] If warning messages are generated during processing of the
   * operation, this field will be populated.
   *
   * @param OperationWarnings[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return OperationWarnings[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
  /**
   * [Output Only] The URL of the zone where the operation resides. Only
   * applicable when performing per-zone operations.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Operation::class, 'Google_Service_Compute_Operation');
