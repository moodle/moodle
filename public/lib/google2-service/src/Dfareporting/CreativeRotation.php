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

namespace Google\Service\Dfareporting;

class CreativeRotation extends \Google\Collection
{
  /**
   * The weights of each creative in the rotation should be sequential starting
   * at 1. The user may adjust the order.
   */
  public const TYPE_CREATIVE_ROTATION_TYPE_SEQUENTIAL = 'CREATIVE_ROTATION_TYPE_SEQUENTIAL';
  /**
   * The weights are calculated according to the ad's
   * CreativeRoationWeightStrategy.
   */
  public const TYPE_CREATIVE_ROTATION_TYPE_RANDOM = 'CREATIVE_ROTATION_TYPE_RANDOM';
  /**
   * The creative weights should all be equal to 1. This is the default value
   * for all ads with a rotation type of Random.
   */
  public const WEIGHT_CALCULATION_STRATEGY_WEIGHT_STRATEGY_EQUAL = 'WEIGHT_STRATEGY_EQUAL';
  /**
   * The creative weights can be any user provided positive integer.
   */
  public const WEIGHT_CALCULATION_STRATEGY_WEIGHT_STRATEGY_CUSTOM = 'WEIGHT_STRATEGY_CUSTOM';
  /**
   * The weights will be automatically calculated giving preference to the
   * creative that has the highest CTR. The CTR for campaigns that are optimized
   * for clicks = clicks/impressions. The CTR for campaigns that are optimized
   * for view-through or click through is sum(activities + floodlight
   * weight)/impressions.
   */
  public const WEIGHT_CALCULATION_STRATEGY_WEIGHT_STRATEGY_HIGHEST_CTR = 'WEIGHT_STRATEGY_HIGHEST_CTR';
  /**
   * The creative weights will be automatically calculated using a formula that
   * could not possibly be explained in these comments. The value will be within
   * some predetermined range (probably 0 - 1,000,000).
   */
  public const WEIGHT_CALCULATION_STRATEGY_WEIGHT_STRATEGY_OPTIMIZED = 'WEIGHT_STRATEGY_OPTIMIZED';
  protected $collection_key = 'creativeAssignments';
  protected $creativeAssignmentsType = CreativeAssignment::class;
  protected $creativeAssignmentsDataType = 'array';
  /**
   * Creative optimization configuration that is used by this ad. It should
   * refer to one of the existing optimization configurations in the ad's
   * campaign. If it is unset or set to 0, then the campaign's default
   * optimization configuration will be used for this ad.
   *
   * @var string
   */
  public $creativeOptimizationConfigurationId;
  /**
   * Type of creative rotation. Can be used to specify whether to use sequential
   * or random rotation.
   *
   * @var string
   */
  public $type;
  /**
   * Strategy for calculating weights. Used with CREATIVE_ROTATION_TYPE_RANDOM.
   *
   * @var string
   */
  public $weightCalculationStrategy;

  /**
   * Creative assignments in this creative rotation.
   *
   * @param CreativeAssignment[] $creativeAssignments
   */
  public function setCreativeAssignments($creativeAssignments)
  {
    $this->creativeAssignments = $creativeAssignments;
  }
  /**
   * @return CreativeAssignment[]
   */
  public function getCreativeAssignments()
  {
    return $this->creativeAssignments;
  }
  /**
   * Creative optimization configuration that is used by this ad. It should
   * refer to one of the existing optimization configurations in the ad's
   * campaign. If it is unset or set to 0, then the campaign's default
   * optimization configuration will be used for this ad.
   *
   * @param string $creativeOptimizationConfigurationId
   */
  public function setCreativeOptimizationConfigurationId($creativeOptimizationConfigurationId)
  {
    $this->creativeOptimizationConfigurationId = $creativeOptimizationConfigurationId;
  }
  /**
   * @return string
   */
  public function getCreativeOptimizationConfigurationId()
  {
    return $this->creativeOptimizationConfigurationId;
  }
  /**
   * Type of creative rotation. Can be used to specify whether to use sequential
   * or random rotation.
   *
   * Accepted values: CREATIVE_ROTATION_TYPE_SEQUENTIAL,
   * CREATIVE_ROTATION_TYPE_RANDOM
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Strategy for calculating weights. Used with CREATIVE_ROTATION_TYPE_RANDOM.
   *
   * Accepted values: WEIGHT_STRATEGY_EQUAL, WEIGHT_STRATEGY_CUSTOM,
   * WEIGHT_STRATEGY_HIGHEST_CTR, WEIGHT_STRATEGY_OPTIMIZED
   *
   * @param self::WEIGHT_CALCULATION_STRATEGY_* $weightCalculationStrategy
   */
  public function setWeightCalculationStrategy($weightCalculationStrategy)
  {
    $this->weightCalculationStrategy = $weightCalculationStrategy;
  }
  /**
   * @return self::WEIGHT_CALCULATION_STRATEGY_*
   */
  public function getWeightCalculationStrategy()
  {
    return $this->weightCalculationStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeRotation::class, 'Google_Service_Dfareporting_CreativeRotation');
