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

namespace Google\Service\StorageBatchOperations;

class Job extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * In progress.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Cancelled by the user.
   */
  public const STATE_CANCELED = 'CANCELED';
  /**
   * Terminated due to an unrecoverable failure.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'errorSummaries';
  protected $bucketListType = BucketList::class;
  protected $bucketListDataType = '';
  /**
   * Output only. The time that the job was completed.
   *
   * @var string
   */
  public $completeTime;
  protected $countersType = Counters::class;
  protected $countersDataType = '';
  /**
   * Output only. The time that the job was created.
   *
   * @var string
   */
  public $createTime;
  protected $deleteObjectType = DeleteObject::class;
  protected $deleteObjectDataType = '';
  /**
   * Optional. A description provided by the user for the job. Its max length is
   * 1024 bytes when Unicode-encoded.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. If true, the job will run in dry run mode, returning the total
   * object count and, if the object configuration is a prefix list, the bytes
   * found from source. No transformations will be performed.
   *
   * @var bool
   */
  public $dryRun;
  protected $errorSummariesType = ErrorSummary::class;
  protected $errorSummariesDataType = 'array';
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * Identifier. The resource name of the Job. job_id is unique within the
   * project, that is either set by the customer or defined by the service.
   * Format: projects/{project}/locations/global/jobs/{job_id} . For example:
   * "projects/123456/locations/global/jobs/job01".
   *
   * @var string
   */
  public $name;
  protected $putMetadataType = PutMetadata::class;
  protected $putMetadataDataType = '';
  protected $putObjectHoldType = PutObjectHold::class;
  protected $putObjectHoldDataType = '';
  protected $rewriteObjectType = RewriteObject::class;
  protected $rewriteObjectDataType = '';
  /**
   * Output only. The time that the job was scheduled.
   *
   * @var string
   */
  public $scheduleTime;
  /**
   * Output only. State of the job.
   *
   * @var string
   */
  public $state;

  /**
   * Specifies a list of buckets and their objects to be transformed.
   *
   * @param BucketList $bucketList
   */
  public function setBucketList(BucketList $bucketList)
  {
    $this->bucketList = $bucketList;
  }
  /**
   * @return BucketList
   */
  public function getBucketList()
  {
    return $this->bucketList;
  }
  /**
   * Output only. The time that the job was completed.
   *
   * @param string $completeTime
   */
  public function setCompleteTime($completeTime)
  {
    $this->completeTime = $completeTime;
  }
  /**
   * @return string
   */
  public function getCompleteTime()
  {
    return $this->completeTime;
  }
  /**
   * Output only. Information about the progress of the job.
   *
   * @param Counters $counters
   */
  public function setCounters(Counters $counters)
  {
    $this->counters = $counters;
  }
  /**
   * @return Counters
   */
  public function getCounters()
  {
    return $this->counters;
  }
  /**
   * Output only. The time that the job was created.
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
   * Delete objects.
   *
   * @param DeleteObject $deleteObject
   */
  public function setDeleteObject(DeleteObject $deleteObject)
  {
    $this->deleteObject = $deleteObject;
  }
  /**
   * @return DeleteObject
   */
  public function getDeleteObject()
  {
    return $this->deleteObject;
  }
  /**
   * Optional. A description provided by the user for the job. Its max length is
   * 1024 bytes when Unicode-encoded.
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
   * Optional. If true, the job will run in dry run mode, returning the total
   * object count and, if the object configuration is a prefix list, the bytes
   * found from source. No transformations will be performed.
   *
   * @param bool $dryRun
   */
  public function setDryRun($dryRun)
  {
    $this->dryRun = $dryRun;
  }
  /**
   * @return bool
   */
  public function getDryRun()
  {
    return $this->dryRun;
  }
  /**
   * Output only. Summarizes errors encountered with sample error log entries.
   *
   * @param ErrorSummary[] $errorSummaries
   */
  public function setErrorSummaries($errorSummaries)
  {
    $this->errorSummaries = $errorSummaries;
  }
  /**
   * @return ErrorSummary[]
   */
  public function getErrorSummaries()
  {
    return $this->errorSummaries;
  }
  /**
   * Optional. Logging configuration.
   *
   * @param LoggingConfig $loggingConfig
   */
  public function setLoggingConfig(LoggingConfig $loggingConfig)
  {
    $this->loggingConfig = $loggingConfig;
  }
  /**
   * @return LoggingConfig
   */
  public function getLoggingConfig()
  {
    return $this->loggingConfig;
  }
  /**
   * Identifier. The resource name of the Job. job_id is unique within the
   * project, that is either set by the customer or defined by the service.
   * Format: projects/{project}/locations/global/jobs/{job_id} . For example:
   * "projects/123456/locations/global/jobs/job01".
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
   * Updates object metadata. Allows updating fixed-key and custom metadata and
   * fixed-key metadata i.e. Cache-Control, Content-Disposition, Content-
   * Encoding, Content-Language, Content-Type, Custom-Time.
   *
   * @param PutMetadata $putMetadata
   */
  public function setPutMetadata(PutMetadata $putMetadata)
  {
    $this->putMetadata = $putMetadata;
  }
  /**
   * @return PutMetadata
   */
  public function getPutMetadata()
  {
    return $this->putMetadata;
  }
  /**
   * Changes object hold status.
   *
   * @param PutObjectHold $putObjectHold
   */
  public function setPutObjectHold(PutObjectHold $putObjectHold)
  {
    $this->putObjectHold = $putObjectHold;
  }
  /**
   * @return PutObjectHold
   */
  public function getPutObjectHold()
  {
    return $this->putObjectHold;
  }
  /**
   * Rewrite the object and updates metadata like KMS key.
   *
   * @param RewriteObject $rewriteObject
   */
  public function setRewriteObject(RewriteObject $rewriteObject)
  {
    $this->rewriteObject = $rewriteObject;
  }
  /**
   * @return RewriteObject
   */
  public function getRewriteObject()
  {
    return $this->rewriteObject;
  }
  /**
   * Output only. The time that the job was scheduled.
   *
   * @param string $scheduleTime
   */
  public function setScheduleTime($scheduleTime)
  {
    $this->scheduleTime = $scheduleTime;
  }
  /**
   * @return string
   */
  public function getScheduleTime()
  {
    return $this->scheduleTime;
  }
  /**
   * Output only. State of the job.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, SUCCEEDED, CANCELED, FAILED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Job::class, 'Google_Service_StorageBatchOperations_Job');
