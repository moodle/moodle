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

namespace Google\Service\Bigquery;

class DatasetListDatasets extends \Google\Model
{
  protected $datasetReferenceType = DatasetReference::class;
  protected $datasetReferenceDataType = '';
  protected $externalDatasetReferenceType = ExternalDatasetReference::class;
  protected $externalDatasetReferenceDataType = '';
  /**
   * An alternate name for the dataset. The friendly name is purely decorative
   * in nature.
   *
   * @var string
   */
  public $friendlyName;
  /**
   * The fully-qualified, unique, opaque ID of the dataset.
   *
   * @var string
   */
  public $id;
  /**
   * The resource type. This property always returns the value
   * "bigquery#dataset"
   *
   * @var string
   */
  public $kind;
  /**
   * The labels associated with this dataset. You can use these to organize and
   * group your datasets.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The geographic location where the dataset resides.
   *
   * @var string
   */
  public $location;

  /**
   * The dataset reference. Use this property to access specific parts of the
   * dataset's ID, such as project ID or dataset ID.
   *
   * @param DatasetReference $datasetReference
   */
  public function setDatasetReference(DatasetReference $datasetReference)
  {
    $this->datasetReference = $datasetReference;
  }
  /**
   * @return DatasetReference
   */
  public function getDatasetReference()
  {
    return $this->datasetReference;
  }
  /**
   * Output only. Reference to a read-only external dataset defined in data
   * catalogs outside of BigQuery. Filled out when the dataset type is EXTERNAL.
   *
   * @param ExternalDatasetReference $externalDatasetReference
   */
  public function setExternalDatasetReference(ExternalDatasetReference $externalDatasetReference)
  {
    $this->externalDatasetReference = $externalDatasetReference;
  }
  /**
   * @return ExternalDatasetReference
   */
  public function getExternalDatasetReference()
  {
    return $this->externalDatasetReference;
  }
  /**
   * An alternate name for the dataset. The friendly name is purely decorative
   * in nature.
   *
   * @param string $friendlyName
   */
  public function setFriendlyName($friendlyName)
  {
    $this->friendlyName = $friendlyName;
  }
  /**
   * @return string
   */
  public function getFriendlyName()
  {
    return $this->friendlyName;
  }
  /**
   * The fully-qualified, unique, opaque ID of the dataset.
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
   * The resource type. This property always returns the value
   * "bigquery#dataset"
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
   * The labels associated with this dataset. You can use these to organize and
   * group your datasets.
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
   * The geographic location where the dataset resides.
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
class_alias(DatasetListDatasets::class, 'Google_Service_Bigquery_DatasetListDatasets');
