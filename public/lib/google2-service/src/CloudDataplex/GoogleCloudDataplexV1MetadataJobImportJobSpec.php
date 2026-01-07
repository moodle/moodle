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

class GoogleCloudDataplexV1MetadataJobImportJobSpec extends \Google\Model
{
  /**
   * Sync mode unspecified.
   */
  public const ASPECT_SYNC_MODE_SYNC_MODE_UNSPECIFIED = 'SYNC_MODE_UNSPECIFIED';
  /**
   * All resources in the job's scope are modified. If a resource exists in
   * Dataplex Universal Catalog but isn't included in the metadata import file,
   * the resource is deleted when you run the metadata job. Use this mode to
   * perform a full sync of the set of entries in the job scope.This sync mode
   * is supported for entries.
   */
  public const ASPECT_SYNC_MODE_FULL = 'FULL';
  /**
   * Only the resources that are explicitly included in the metadata import file
   * are modified. Use this mode to modify a subset of resources while leaving
   * unreferenced resources unchanged.This sync mode is supported for aspects.
   */
  public const ASPECT_SYNC_MODE_INCREMENTAL = 'INCREMENTAL';
  /**
   * If entry sync mode is NONE, then aspects are modified according to the
   * aspect sync mode. Other metadata that belongs to entries in the job's scope
   * isn't modified.This sync mode is supported for entries.
   */
  public const ASPECT_SYNC_MODE_NONE = 'NONE';
  /**
   * Sync mode unspecified.
   */
  public const ENTRY_SYNC_MODE_SYNC_MODE_UNSPECIFIED = 'SYNC_MODE_UNSPECIFIED';
  /**
   * All resources in the job's scope are modified. If a resource exists in
   * Dataplex Universal Catalog but isn't included in the metadata import file,
   * the resource is deleted when you run the metadata job. Use this mode to
   * perform a full sync of the set of entries in the job scope.This sync mode
   * is supported for entries.
   */
  public const ENTRY_SYNC_MODE_FULL = 'FULL';
  /**
   * Only the resources that are explicitly included in the metadata import file
   * are modified. Use this mode to modify a subset of resources while leaving
   * unreferenced resources unchanged.This sync mode is supported for aspects.
   */
  public const ENTRY_SYNC_MODE_INCREMENTAL = 'INCREMENTAL';
  /**
   * If entry sync mode is NONE, then aspects are modified according to the
   * aspect sync mode. Other metadata that belongs to entries in the job's scope
   * isn't modified.This sync mode is supported for entries.
   */
  public const ENTRY_SYNC_MODE_NONE = 'NONE';
  /**
   * Log level unspecified.
   */
  public const LOG_LEVEL_LOG_LEVEL_UNSPECIFIED = 'LOG_LEVEL_UNSPECIFIED';
  /**
   * Debug-level logging. Captures detailed logs for each import item. Use
   * debug-level logging to troubleshoot issues with specific import items. For
   * example, use debug-level logging to identify resources that are missing
   * from the job scope, entries or aspects that don't conform to the associated
   * entry type or aspect type, or other misconfigurations with the metadata
   * import file.Depending on the size of your metadata job and the number of
   * logs that are generated, debug-level logging might incur additional costs
   * (https://cloud.google.com/stackdriver/pricing).
   */
  public const LOG_LEVEL_DEBUG = 'DEBUG';
  /**
   * Info-level logging. Captures logs at the overall job level. Includes
   * aggregate logs about import items, but doesn't specify which import item
   * has an error.
   */
  public const LOG_LEVEL_INFO = 'INFO';
  /**
   * Required. The sync mode for aspects.
   *
   * @var string
   */
  public $aspectSyncMode;
  /**
   * Required. The sync mode for entries.
   *
   * @var string
   */
  public $entrySyncMode;
  /**
   * Optional. The level of logs to write to Cloud Logging for this job.Debug-
   * level logs provide highly-detailed information for troubleshooting, but
   * their increased verbosity could incur additional costs
   * (https://cloud.google.com/stackdriver/pricing) that might not be merited
   * for all jobs.If unspecified, defaults to INFO.
   *
   * @var string
   */
  public $logLevel;
  protected $scopeType = GoogleCloudDataplexV1MetadataJobImportJobSpecImportJobScope::class;
  protected $scopeDataType = '';
  /**
   * Optional. The time when the process that created the metadata import files
   * began.
   *
   * @var string
   */
  public $sourceCreateTime;
  /**
   * Optional. The URI of a Cloud Storage bucket or folder (beginning with gs://
   * and ending with /) that contains the metadata import files for this job.A
   * metadata import file defines the values to set for each of the entries and
   * aspects in a metadata import job. For more information about how to create
   * a metadata import file and the file requirements, see Metadata import file
   * (https://cloud.google.com/dataplex/docs/import-metadata#metadata-import-
   * file).You can provide multiple metadata import files in the same metadata
   * job. The bucket or folder must contain at least one metadata import file,
   * in JSON Lines format (either .json or .jsonl file extension).In FULL entry
   * sync mode, don't save the metadata import file in a folder named
   * SOURCE_STORAGE_URI/deletions/.Caution: If the metadata import file contains
   * no data, all entries and aspects that belong to the job's scope are
   * deleted.
   *
   * @var string
   */
  public $sourceStorageUri;

  /**
   * Required. The sync mode for aspects.
   *
   * Accepted values: SYNC_MODE_UNSPECIFIED, FULL, INCREMENTAL, NONE
   *
   * @param self::ASPECT_SYNC_MODE_* $aspectSyncMode
   */
  public function setAspectSyncMode($aspectSyncMode)
  {
    $this->aspectSyncMode = $aspectSyncMode;
  }
  /**
   * @return self::ASPECT_SYNC_MODE_*
   */
  public function getAspectSyncMode()
  {
    return $this->aspectSyncMode;
  }
  /**
   * Required. The sync mode for entries.
   *
   * Accepted values: SYNC_MODE_UNSPECIFIED, FULL, INCREMENTAL, NONE
   *
   * @param self::ENTRY_SYNC_MODE_* $entrySyncMode
   */
  public function setEntrySyncMode($entrySyncMode)
  {
    $this->entrySyncMode = $entrySyncMode;
  }
  /**
   * @return self::ENTRY_SYNC_MODE_*
   */
  public function getEntrySyncMode()
  {
    return $this->entrySyncMode;
  }
  /**
   * Optional. The level of logs to write to Cloud Logging for this job.Debug-
   * level logs provide highly-detailed information for troubleshooting, but
   * their increased verbosity could incur additional costs
   * (https://cloud.google.com/stackdriver/pricing) that might not be merited
   * for all jobs.If unspecified, defaults to INFO.
   *
   * Accepted values: LOG_LEVEL_UNSPECIFIED, DEBUG, INFO
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
  /**
   * Required. A boundary on the scope of impact that the metadata import job
   * can have.
   *
   * @param GoogleCloudDataplexV1MetadataJobImportJobSpecImportJobScope $scope
   */
  public function setScope(GoogleCloudDataplexV1MetadataJobImportJobSpecImportJobScope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJobImportJobSpecImportJobScope
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Optional. The time when the process that created the metadata import files
   * began.
   *
   * @param string $sourceCreateTime
   */
  public function setSourceCreateTime($sourceCreateTime)
  {
    $this->sourceCreateTime = $sourceCreateTime;
  }
  /**
   * @return string
   */
  public function getSourceCreateTime()
  {
    return $this->sourceCreateTime;
  }
  /**
   * Optional. The URI of a Cloud Storage bucket or folder (beginning with gs://
   * and ending with /) that contains the metadata import files for this job.A
   * metadata import file defines the values to set for each of the entries and
   * aspects in a metadata import job. For more information about how to create
   * a metadata import file and the file requirements, see Metadata import file
   * (https://cloud.google.com/dataplex/docs/import-metadata#metadata-import-
   * file).You can provide multiple metadata import files in the same metadata
   * job. The bucket or folder must contain at least one metadata import file,
   * in JSON Lines format (either .json or .jsonl file extension).In FULL entry
   * sync mode, don't save the metadata import file in a folder named
   * SOURCE_STORAGE_URI/deletions/.Caution: If the metadata import file contains
   * no data, all entries and aspects that belong to the job's scope are
   * deleted.
   *
   * @param string $sourceStorageUri
   */
  public function setSourceStorageUri($sourceStorageUri)
  {
    $this->sourceStorageUri = $sourceStorageUri;
  }
  /**
   * @return string
   */
  public function getSourceStorageUri()
  {
    return $this->sourceStorageUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1MetadataJobImportJobSpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1MetadataJobImportJobSpec');
