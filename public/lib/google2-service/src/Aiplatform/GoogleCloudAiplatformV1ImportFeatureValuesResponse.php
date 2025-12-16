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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ImportFeatureValuesResponse extends \Google\Model
{
  /**
   * Number of entities that have been imported by the operation.
   *
   * @var string
   */
  public $importedEntityCount;
  /**
   * Number of Feature values that have been imported by the operation.
   *
   * @var string
   */
  public $importedFeatureValueCount;
  /**
   * The number of rows in input source that weren't imported due to either *
   * Not having any featureValues. * Having a null entityId. * Having a null
   * timestamp. * Not being parsable (applicable for CSV sources).
   *
   * @var string
   */
  public $invalidRowCount;
  /**
   * The number rows that weren't ingested due to having feature timestamps
   * outside the retention boundary.
   *
   * @var string
   */
  public $timestampOutsideRetentionRowsCount;

  /**
   * Number of entities that have been imported by the operation.
   *
   * @param string $importedEntityCount
   */
  public function setImportedEntityCount($importedEntityCount)
  {
    $this->importedEntityCount = $importedEntityCount;
  }
  /**
   * @return string
   */
  public function getImportedEntityCount()
  {
    return $this->importedEntityCount;
  }
  /**
   * Number of Feature values that have been imported by the operation.
   *
   * @param string $importedFeatureValueCount
   */
  public function setImportedFeatureValueCount($importedFeatureValueCount)
  {
    $this->importedFeatureValueCount = $importedFeatureValueCount;
  }
  /**
   * @return string
   */
  public function getImportedFeatureValueCount()
  {
    return $this->importedFeatureValueCount;
  }
  /**
   * The number of rows in input source that weren't imported due to either *
   * Not having any featureValues. * Having a null entityId. * Having a null
   * timestamp. * Not being parsable (applicable for CSV sources).
   *
   * @param string $invalidRowCount
   */
  public function setInvalidRowCount($invalidRowCount)
  {
    $this->invalidRowCount = $invalidRowCount;
  }
  /**
   * @return string
   */
  public function getInvalidRowCount()
  {
    return $this->invalidRowCount;
  }
  /**
   * The number rows that weren't ingested due to having feature timestamps
   * outside the retention boundary.
   *
   * @param string $timestampOutsideRetentionRowsCount
   */
  public function setTimestampOutsideRetentionRowsCount($timestampOutsideRetentionRowsCount)
  {
    $this->timestampOutsideRetentionRowsCount = $timestampOutsideRetentionRowsCount;
  }
  /**
   * @return string
   */
  public function getTimestampOutsideRetentionRowsCount()
  {
    return $this->timestampOutsideRetentionRowsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ImportFeatureValuesResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ImportFeatureValuesResponse');
