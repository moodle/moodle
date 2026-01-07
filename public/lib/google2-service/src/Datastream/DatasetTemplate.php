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

namespace Google\Service\Datastream;

class DatasetTemplate extends \Google\Model
{
  /**
   * If supplied, every created dataset will have its name prefixed by the
   * provided value. The prefix and name will be separated by an underscore.
   * i.e. _.
   *
   * @var string
   */
  public $datasetIdPrefix;
  /**
   * Describes the Cloud KMS encryption key that will be used to protect
   * destination BigQuery table. The BigQuery Service Account associated with
   * your project requires access to this encryption key. i.e. projects/{project
   * }/locations/{location}/keyRings/{key_ring}/cryptoKeys/{cryptoKey}. See
   * https://cloud.google.com/bigquery/docs/customer-managed-encryption for more
   * information.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Required. The geographic location where the dataset should reside. See
   * https://cloud.google.com/bigquery/docs/locations for supported locations.
   *
   * @var string
   */
  public $location;

  /**
   * If supplied, every created dataset will have its name prefixed by the
   * provided value. The prefix and name will be separated by an underscore.
   * i.e. _.
   *
   * @param string $datasetIdPrefix
   */
  public function setDatasetIdPrefix($datasetIdPrefix)
  {
    $this->datasetIdPrefix = $datasetIdPrefix;
  }
  /**
   * @return string
   */
  public function getDatasetIdPrefix()
  {
    return $this->datasetIdPrefix;
  }
  /**
   * Describes the Cloud KMS encryption key that will be used to protect
   * destination BigQuery table. The BigQuery Service Account associated with
   * your project requires access to this encryption key. i.e. projects/{project
   * }/locations/{location}/keyRings/{key_ring}/cryptoKeys/{cryptoKey}. See
   * https://cloud.google.com/bigquery/docs/customer-managed-encryption for more
   * information.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Required. The geographic location where the dataset should reside. See
   * https://cloud.google.com/bigquery/docs/locations for supported locations.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatasetTemplate::class, 'Google_Service_Datastream_DatasetTemplate');
