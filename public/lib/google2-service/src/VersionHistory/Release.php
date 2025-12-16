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

namespace Google\Service\VersionHistory;

class Release extends \Google\Collection
{
  protected $collection_key = 'rolloutData';
  /**
   * Rollout fraction. This fraction indicates the fraction of people that
   * should receive this version in this release. If the fraction is not
   * specified in ReleaseManager, the API will assume fraction is 1.
   *
   * @var 
   */
  public $fraction;
  /**
   * Rollout fraction group. Only fractions with the same fraction_group are
   * statistically comparable: there may be non-fractional differences between
   * different fraction groups.
   *
   * @var string
   */
  public $fractionGroup;
  /**
   * Release name. Format is "{product}/platforms/{platform}/channels/{channel}/
   * versions/{version}/releases/{release}"
   *
   * @var string
   */
  public $name;
  /**
   * Whether or not the release was available for version pinning.
   *
   * @var bool
   */
  public $pinnable;
  protected $rolloutDataType = RolloutData::class;
  protected $rolloutDataDataType = 'array';
  protected $servingType = Interval::class;
  protected $servingDataType = '';
  /**
   * String containing just the version number. e.g. "84.0.4147.38"
   *
   * @var string
   */
  public $version;

  public function setFraction($fraction)
  {
    $this->fraction = $fraction;
  }
  public function getFraction()
  {
    return $this->fraction;
  }
  /**
   * Rollout fraction group. Only fractions with the same fraction_group are
   * statistically comparable: there may be non-fractional differences between
   * different fraction groups.
   *
   * @param string $fractionGroup
   */
  public function setFractionGroup($fractionGroup)
  {
    $this->fractionGroup = $fractionGroup;
  }
  /**
   * @return string
   */
  public function getFractionGroup()
  {
    return $this->fractionGroup;
  }
  /**
   * Release name. Format is "{product}/platforms/{platform}/channels/{channel}/
   * versions/{version}/releases/{release}"
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
   * Whether or not the release was available for version pinning.
   *
   * @param bool $pinnable
   */
  public function setPinnable($pinnable)
  {
    $this->pinnable = $pinnable;
  }
  /**
   * @return bool
   */
  public function getPinnable()
  {
    return $this->pinnable;
  }
  /**
   * Rollout-related metadata. Some releases are part of one or more A/B
   * rollouts. This field contains the names and data describing this release's
   * role in any rollouts.
   *
   * @param RolloutData[] $rolloutData
   */
  public function setRolloutData($rolloutData)
  {
    $this->rolloutData = $rolloutData;
  }
  /**
   * @return RolloutData[]
   */
  public function getRolloutData()
  {
    return $this->rolloutData;
  }
  /**
   * Timestamp interval of when the release was live. If end_time is
   * unspecified, the release is currently live.
   *
   * @param Interval $serving
   */
  public function setServing(Interval $serving)
  {
    $this->serving = $serving;
  }
  /**
   * @return Interval
   */
  public function getServing()
  {
    return $this->serving;
  }
  /**
   * String containing just the version number. e.g. "84.0.4147.38"
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Release::class, 'Google_Service_VersionHistory_Release');
