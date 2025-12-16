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

class DatasetList extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $datasetsType = DatasetListDatasets::class;
  protected $datasetsDataType = 'array';
  /**
   * Output only. A hash value of the results page. You can use this property to
   * determine if the page has changed since the last request.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The resource type. This property always returns the value
   * "bigquery#datasetList"
   *
   * @var string
   */
  public $kind;
  /**
   * A token that can be used to request the next results page. This property is
   * omitted on the final results page.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * A list of skipped locations that were unreachable. For more information
   * about BigQuery locations, see:
   * https://cloud.google.com/bigquery/docs/locations. Example: "europe-west5"
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * An array of the dataset resources in the project. Each resource contains
   * basic information. For full information about a particular dataset
   * resource, use the Datasets: get method. This property is omitted when there
   * are no datasets in the project.
   *
   * @param DatasetListDatasets[] $datasets
   */
  public function setDatasets($datasets)
  {
    $this->datasets = $datasets;
  }
  /**
   * @return DatasetListDatasets[]
   */
  public function getDatasets()
  {
    return $this->datasets;
  }
  /**
   * Output only. A hash value of the results page. You can use this property to
   * determine if the page has changed since the last request.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. The resource type. This property always returns the value
   * "bigquery#datasetList"
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
   * A token that can be used to request the next results page. This property is
   * omitted on the final results page.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * A list of skipped locations that were unreachable. For more information
   * about BigQuery locations, see:
   * https://cloud.google.com/bigquery/docs/locations. Example: "europe-west5"
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatasetList::class, 'Google_Service_Bigquery_DatasetList');
