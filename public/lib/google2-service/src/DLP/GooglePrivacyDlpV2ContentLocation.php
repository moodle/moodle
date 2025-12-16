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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2ContentLocation extends \Google\Model
{
  /**
   * Name of the container where the finding is located. The top level name is
   * the source file name or table name. Names of some common storage containers
   * are formatted as follows: * BigQuery tables:
   * `{project_id}:{dataset_id}.{table_id}` * Cloud Storage files:
   * `gs://{bucket}/{path}` * Datastore namespace: {namespace} Nested names
   * could be absent if the embedded object has no string identifier (for
   * example, an image contained within a document).
   *
   * @var string
   */
  public $containerName;
  /**
   * Finding container modification timestamp, if applicable. For Cloud Storage,
   * this field contains the last file modification timestamp. For a BigQuery
   * table, this field contains the last_modified_time property. For Datastore,
   * this field isn't populated.
   *
   * @var string
   */
  public $containerTimestamp;
  /**
   * Finding container version, if available ("generation" for Cloud Storage).
   *
   * @var string
   */
  public $containerVersion;
  protected $documentLocationType = GooglePrivacyDlpV2DocumentLocation::class;
  protected $documentLocationDataType = '';
  protected $imageLocationType = GooglePrivacyDlpV2ImageLocation::class;
  protected $imageLocationDataType = '';
  protected $metadataLocationType = GooglePrivacyDlpV2MetadataLocation::class;
  protected $metadataLocationDataType = '';
  protected $recordLocationType = GooglePrivacyDlpV2RecordLocation::class;
  protected $recordLocationDataType = '';

  /**
   * Name of the container where the finding is located. The top level name is
   * the source file name or table name. Names of some common storage containers
   * are formatted as follows: * BigQuery tables:
   * `{project_id}:{dataset_id}.{table_id}` * Cloud Storage files:
   * `gs://{bucket}/{path}` * Datastore namespace: {namespace} Nested names
   * could be absent if the embedded object has no string identifier (for
   * example, an image contained within a document).
   *
   * @param string $containerName
   */
  public function setContainerName($containerName)
  {
    $this->containerName = $containerName;
  }
  /**
   * @return string
   */
  public function getContainerName()
  {
    return $this->containerName;
  }
  /**
   * Finding container modification timestamp, if applicable. For Cloud Storage,
   * this field contains the last file modification timestamp. For a BigQuery
   * table, this field contains the last_modified_time property. For Datastore,
   * this field isn't populated.
   *
   * @param string $containerTimestamp
   */
  public function setContainerTimestamp($containerTimestamp)
  {
    $this->containerTimestamp = $containerTimestamp;
  }
  /**
   * @return string
   */
  public function getContainerTimestamp()
  {
    return $this->containerTimestamp;
  }
  /**
   * Finding container version, if available ("generation" for Cloud Storage).
   *
   * @param string $containerVersion
   */
  public function setContainerVersion($containerVersion)
  {
    $this->containerVersion = $containerVersion;
  }
  /**
   * @return string
   */
  public function getContainerVersion()
  {
    return $this->containerVersion;
  }
  /**
   * Location data for document files.
   *
   * @param GooglePrivacyDlpV2DocumentLocation $documentLocation
   */
  public function setDocumentLocation(GooglePrivacyDlpV2DocumentLocation $documentLocation)
  {
    $this->documentLocation = $documentLocation;
  }
  /**
   * @return GooglePrivacyDlpV2DocumentLocation
   */
  public function getDocumentLocation()
  {
    return $this->documentLocation;
  }
  /**
   * Location within an image's pixels.
   *
   * @param GooglePrivacyDlpV2ImageLocation $imageLocation
   */
  public function setImageLocation(GooglePrivacyDlpV2ImageLocation $imageLocation)
  {
    $this->imageLocation = $imageLocation;
  }
  /**
   * @return GooglePrivacyDlpV2ImageLocation
   */
  public function getImageLocation()
  {
    return $this->imageLocation;
  }
  /**
   * Location within the metadata for inspected content.
   *
   * @param GooglePrivacyDlpV2MetadataLocation $metadataLocation
   */
  public function setMetadataLocation(GooglePrivacyDlpV2MetadataLocation $metadataLocation)
  {
    $this->metadataLocation = $metadataLocation;
  }
  /**
   * @return GooglePrivacyDlpV2MetadataLocation
   */
  public function getMetadataLocation()
  {
    return $this->metadataLocation;
  }
  /**
   * Location within a row or record of a database table.
   *
   * @param GooglePrivacyDlpV2RecordLocation $recordLocation
   */
  public function setRecordLocation(GooglePrivacyDlpV2RecordLocation $recordLocation)
  {
    $this->recordLocation = $recordLocation;
  }
  /**
   * @return GooglePrivacyDlpV2RecordLocation
   */
  public function getRecordLocation()
  {
    return $this->recordLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2ContentLocation::class, 'Google_Service_DLP_GooglePrivacyDlpV2ContentLocation');
