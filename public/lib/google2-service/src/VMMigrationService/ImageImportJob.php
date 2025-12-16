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

namespace Google\Service\VMMigrationService;

class ImageImportJob extends \Google\Collection
{
  /**
   * The state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The image import has not yet started.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The image import is active and running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The image import has finished successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The image import has finished with errors.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The image import is being cancelled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The image import was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  protected $collection_key = 'warnings';
  /**
   * Output only. The path to the Cloud Storage file from which the image should
   * be imported.
   *
   * @var string
   */
  public $cloudStorageUri;
  /**
   * Output only. The time the image import was created (as an API call, not
   * when it was actually created in the target).
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The resource paths of the resources created by the image
   * import job.
   *
   * @var string[]
   */
  public $createdResources;
  protected $diskImageTargetDetailsType = DiskImageTargetDetails::class;
  protected $diskImageTargetDetailsDataType = '';
  /**
   * Output only. The time the image import was ended.
   *
   * @var string
   */
  public $endTime;
  protected $errorsType = Status::class;
  protected $errorsDataType = 'array';
  protected $machineImageTargetDetailsType = MachineImageTargetDetails::class;
  protected $machineImageTargetDetailsDataType = '';
  /**
   * Output only. The resource path of the ImageImportJob.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the image import.
   *
   * @var string
   */
  public $state;
  protected $stepsType = ImageImportStep::class;
  protected $stepsDataType = 'array';
  protected $warningsType = MigrationWarning::class;
  protected $warningsDataType = 'array';

  /**
   * Output only. The path to the Cloud Storage file from which the image should
   * be imported.
   *
   * @param string $cloudStorageUri
   */
  public function setCloudStorageUri($cloudStorageUri)
  {
    $this->cloudStorageUri = $cloudStorageUri;
  }
  /**
   * @return string
   */
  public function getCloudStorageUri()
  {
    return $this->cloudStorageUri;
  }
  /**
   * Output only. The time the image import was created (as an API call, not
   * when it was actually created in the target).
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
   * Output only. The resource paths of the resources created by the image
   * import job.
   *
   * @param string[] $createdResources
   */
  public function setCreatedResources($createdResources)
  {
    $this->createdResources = $createdResources;
  }
  /**
   * @return string[]
   */
  public function getCreatedResources()
  {
    return $this->createdResources;
  }
  /**
   * Output only. Target details used to import a disk image.
   *
   * @param DiskImageTargetDetails $diskImageTargetDetails
   */
  public function setDiskImageTargetDetails(DiskImageTargetDetails $diskImageTargetDetails)
  {
    $this->diskImageTargetDetails = $diskImageTargetDetails;
  }
  /**
   * @return DiskImageTargetDetails
   */
  public function getDiskImageTargetDetails()
  {
    return $this->diskImageTargetDetails;
  }
  /**
   * Output only. The time the image import was ended.
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
   * Output only. Provides details on the error that led to the image import
   * state in case of an error.
   *
   * @param Status[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Status[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Target details used to import a machine image.
   *
   * @param MachineImageTargetDetails $machineImageTargetDetails
   */
  public function setMachineImageTargetDetails(MachineImageTargetDetails $machineImageTargetDetails)
  {
    $this->machineImageTargetDetails = $machineImageTargetDetails;
  }
  /**
   * @return MachineImageTargetDetails
   */
  public function getMachineImageTargetDetails()
  {
    return $this->machineImageTargetDetails;
  }
  /**
   * Output only. The resource path of the ImageImportJob.
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
   * Output only. The state of the image import.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED, FAILED,
   * CANCELLING, CANCELLED
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
   * Output only. The image import steps list representing its progress.
   *
   * @param ImageImportStep[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return ImageImportStep[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
  /**
   * Output only. Warnings that occurred during the image import.
   *
   * @param MigrationWarning[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return MigrationWarning[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageImportJob::class, 'Google_Service_VMMigrationService_ImageImportJob');
