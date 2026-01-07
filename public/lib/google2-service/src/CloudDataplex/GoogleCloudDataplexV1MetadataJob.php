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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1MetadataJob extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Import job.
   */
  public const TYPE_IMPORT = 'IMPORT';
  /**
   * Export job.
   */
  public const TYPE_EXPORT = 'EXPORT';
  /**
   * Output only. The time when the metadata job was created.
   *
   * @var string
   */
  public $createTime;
  protected $exportResultType = GoogleCloudDataplexV1MetadataJobExportJobResult::class;
  protected $exportResultDataType = '';
  protected $exportSpecType = GoogleCloudDataplexV1MetadataJobExportJobSpec::class;
  protected $exportSpecDataType = '';
  protected $importResultType = GoogleCloudDataplexV1MetadataJobImportJobResult::class;
  protected $importResultDataType = '';
  protected $importSpecType = GoogleCloudDataplexV1MetadataJobImportJobSpec::class;
  protected $importSpecDataType = '';
  /**
   * Optional. User-defined labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The name of the resource that the configuration is
   * applied to, in the format projects/{project_number}/locations/{location_id}
   * /metadataJobs/{metadata_job_id}.
   *
   * @var string
   */
  public $name;
  protected $statusType = GoogleCloudDataplexV1MetadataJobStatus::class;
  protected $statusDataType = '';
  /**
   * Required. Metadata job type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. A system-generated, globally unique ID for the metadata job.
   * If the metadata job is deleted and then re-created with the same name, this
   * ID is different.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the metadata job was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the metadata job was created.
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
   * Output only. Export job result.
   *
   * @param GoogleCloudDataplexV1MetadataJobExportJobResult $exportResult
   */
  public function setExportResult(GoogleCloudDataplexV1MetadataJobExportJobResult $exportResult)
  {
    $this->exportResult = $exportResult;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJobExportJobResult
   */
  public function getExportResult()
  {
    return $this->exportResult;
  }
  /**
   * Export job specification.
   *
   * @param GoogleCloudDataplexV1MetadataJobExportJobSpec $exportSpec
   */
  public function setExportSpec(GoogleCloudDataplexV1MetadataJobExportJobSpec $exportSpec)
  {
    $this->exportSpec = $exportSpec;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJobExportJobSpec
   */
  public function getExportSpec()
  {
    return $this->exportSpec;
  }
  /**
   * Output only. Import job result.
   *
   * @param GoogleCloudDataplexV1MetadataJobImportJobResult $importResult
   */
  public function setImportResult(GoogleCloudDataplexV1MetadataJobImportJobResult $importResult)
  {
    $this->importResult = $importResult;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJobImportJobResult
   */
  public function getImportResult()
  {
    return $this->importResult;
  }
  /**
   * Import job specification.
   *
   * @param GoogleCloudDataplexV1MetadataJobImportJobSpec $importSpec
   */
  public function setImportSpec(GoogleCloudDataplexV1MetadataJobImportJobSpec $importSpec)
  {
    $this->importSpec = $importSpec;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJobImportJobSpec
   */
  public function getImportSpec()
  {
    return $this->importSpec;
  }
  /**
   * Optional. User-defined labels.
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
   * Output only. Identifier. The name of the resource that the configuration is
   * applied to, in the format projects/{project_number}/locations/{location_id}
   * /metadataJobs/{metadata_job_id}.
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
   * Output only. Metadata job status.
   *
   * @param GoogleCloudDataplexV1MetadataJobStatus $status
   */
  public function setStatus(GoogleCloudDataplexV1MetadataJobStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJobStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Required. Metadata job type.
   *
   * Accepted values: TYPE_UNSPECIFIED, IMPORT, EXPORT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. A system-generated, globally unique ID for the metadata job.
   * If the metadata job is deleted and then re-created with the same name, this
   * ID is different.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time when the metadata job was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1MetadataJob::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1MetadataJob');
