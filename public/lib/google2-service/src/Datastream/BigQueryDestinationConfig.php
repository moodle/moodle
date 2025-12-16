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

class BigQueryDestinationConfig extends \Google\Model
{
  protected $appendOnlyType = AppendOnly::class;
  protected $appendOnlyDataType = '';
  protected $blmtConfigType = BlmtConfig::class;
  protected $blmtConfigDataType = '';
  /**
   * The guaranteed data freshness (in seconds) when querying tables created by
   * the stream. Editing this field will only affect new tables created in the
   * future, but existing tables will not be impacted. Lower values mean that
   * queries will return fresher data, but may result in higher cost.
   *
   * @var string
   */
  public $dataFreshness;
  protected $mergeType = Merge::class;
  protected $mergeDataType = '';
  protected $singleTargetDatasetType = SingleTargetDataset::class;
  protected $singleTargetDatasetDataType = '';
  protected $sourceHierarchyDatasetsType = SourceHierarchyDatasets::class;
  protected $sourceHierarchyDatasetsDataType = '';

  /**
   * Append only mode
   *
   * @param AppendOnly $appendOnly
   */
  public function setAppendOnly(AppendOnly $appendOnly)
  {
    $this->appendOnly = $appendOnly;
  }
  /**
   * @return AppendOnly
   */
  public function getAppendOnly()
  {
    return $this->appendOnly;
  }
  /**
   * Optional. Big Lake Managed Tables (BLMT) configuration.
   *
   * @param BlmtConfig $blmtConfig
   */
  public function setBlmtConfig(BlmtConfig $blmtConfig)
  {
    $this->blmtConfig = $blmtConfig;
  }
  /**
   * @return BlmtConfig
   */
  public function getBlmtConfig()
  {
    return $this->blmtConfig;
  }
  /**
   * The guaranteed data freshness (in seconds) when querying tables created by
   * the stream. Editing this field will only affect new tables created in the
   * future, but existing tables will not be impacted. Lower values mean that
   * queries will return fresher data, but may result in higher cost.
   *
   * @param string $dataFreshness
   */
  public function setDataFreshness($dataFreshness)
  {
    $this->dataFreshness = $dataFreshness;
  }
  /**
   * @return string
   */
  public function getDataFreshness()
  {
    return $this->dataFreshness;
  }
  /**
   * The standard mode
   *
   * @param Merge $merge
   */
  public function setMerge(Merge $merge)
  {
    $this->merge = $merge;
  }
  /**
   * @return Merge
   */
  public function getMerge()
  {
    return $this->merge;
  }
  /**
   * Single destination dataset.
   *
   * @param SingleTargetDataset $singleTargetDataset
   */
  public function setSingleTargetDataset(SingleTargetDataset $singleTargetDataset)
  {
    $this->singleTargetDataset = $singleTargetDataset;
  }
  /**
   * @return SingleTargetDataset
   */
  public function getSingleTargetDataset()
  {
    return $this->singleTargetDataset;
  }
  /**
   * Source hierarchy datasets.
   *
   * @param SourceHierarchyDatasets $sourceHierarchyDatasets
   */
  public function setSourceHierarchyDatasets(SourceHierarchyDatasets $sourceHierarchyDatasets)
  {
    $this->sourceHierarchyDatasets = $sourceHierarchyDatasets;
  }
  /**
   * @return SourceHierarchyDatasets
   */
  public function getSourceHierarchyDatasets()
  {
    return $this->sourceHierarchyDatasets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryDestinationConfig::class, 'Google_Service_Datastream_BigQueryDestinationConfig');
