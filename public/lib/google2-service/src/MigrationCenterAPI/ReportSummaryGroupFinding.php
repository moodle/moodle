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

namespace Google\Service\MigrationCenterAPI;

class ReportSummaryGroupFinding extends \Google\Collection
{
  protected $collection_key = 'preferenceSetFindings';
  protected $assetAggregateStatsType = ReportSummaryAssetAggregateStats::class;
  protected $assetAggregateStatsDataType = '';
  /**
   * Description for the Group.
   *
   * @var string
   */
  public $description;
  /**
   * Display Name for the Group.
   *
   * @var string
   */
  public $displayName;
  /**
   * This field is deprecated, do not rely on it having a value.
   *
   * @deprecated
   * @var string
   */
  public $overlappingAssetCount;
  protected $preferenceSetFindingsType = ReportSummaryGroupPreferenceSetFinding::class;
  protected $preferenceSetFindingsDataType = 'array';

  /**
   * Summary statistics for all the assets in this group.
   *
   * @param ReportSummaryAssetAggregateStats $assetAggregateStats
   */
  public function setAssetAggregateStats(ReportSummaryAssetAggregateStats $assetAggregateStats)
  {
    $this->assetAggregateStats = $assetAggregateStats;
  }
  /**
   * @return ReportSummaryAssetAggregateStats
   */
  public function getAssetAggregateStats()
  {
    return $this->assetAggregateStats;
  }
  /**
   * Description for the Group.
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
   * Display Name for the Group.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * This field is deprecated, do not rely on it having a value.
   *
   * @deprecated
   * @param string $overlappingAssetCount
   */
  public function setOverlappingAssetCount($overlappingAssetCount)
  {
    $this->overlappingAssetCount = $overlappingAssetCount;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getOverlappingAssetCount()
  {
    return $this->overlappingAssetCount;
  }
  /**
   * Findings for each of the PreferenceSets for this group.
   *
   * @param ReportSummaryGroupPreferenceSetFinding[] $preferenceSetFindings
   */
  public function setPreferenceSetFindings($preferenceSetFindings)
  {
    $this->preferenceSetFindings = $preferenceSetFindings;
  }
  /**
   * @return ReportSummaryGroupPreferenceSetFinding[]
   */
  public function getPreferenceSetFindings()
  {
    return $this->preferenceSetFindings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSummaryGroupFinding::class, 'Google_Service_MigrationCenterAPI_ReportSummaryGroupFinding');
