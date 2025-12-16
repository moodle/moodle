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

namespace Google\Service\AnalyticsHub;

class DestinationDataset extends \Google\Collection
{
  protected $collection_key = 'replicaLocations';
  protected $datasetReferenceType = DestinationDatasetReference::class;
  protected $datasetReferenceDataType = '';
  /**
   * Optional. A user-friendly description of the dataset.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. A descriptive name for the dataset.
   *
   * @var string
   */
  public $friendlyName;
  /**
   * Optional. The labels associated with this dataset. You can use these to
   * organize and group your datasets. You can set this property when inserting
   * or updating a dataset. See https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels for more information.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The geographic location where the dataset should reside. See
   * https://cloud.google.com/bigquery/docs/locations for supported locations.
   *
   * @var string
   */
  public $location;
  /**
   * Optional. The geographic locations where the dataset should be replicated.
   * See [BigQuery locations](https://cloud.google.com/bigquery/docs/locations)
   * for supported locations.
   *
   * @var string[]
   */
  public $replicaLocations;

  /**
   * Required. A reference that identifies the destination dataset.
   *
   * @param DestinationDatasetReference $datasetReference
   */
  public function setDatasetReference(DestinationDatasetReference $datasetReference)
  {
    $this->datasetReference = $datasetReference;
  }
  /**
   * @return DestinationDatasetReference
   */
  public function getDatasetReference()
  {
    return $this->datasetReference;
  }
  /**
   * Optional. A user-friendly description of the dataset.
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
   * Optional. A descriptive name for the dataset.
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
   * Optional. The labels associated with this dataset. You can use these to
   * organize and group your datasets. You can set this property when inserting
   * or updating a dataset. See https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels for more information.
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
  /**
   * Optional. The geographic locations where the dataset should be replicated.
   * See [BigQuery locations](https://cloud.google.com/bigquery/docs/locations)
   * for supported locations.
   *
   * @param string[] $replicaLocations
   */
  public function setReplicaLocations($replicaLocations)
  {
    $this->replicaLocations = $replicaLocations;
  }
  /**
   * @return string[]
   */
  public function getReplicaLocations()
  {
    return $this->replicaLocations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DestinationDataset::class, 'Google_Service_AnalyticsHub_DestinationDataset');
