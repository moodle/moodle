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

class CreativeOptimizationConfiguration extends \Google\Collection
{
  public const OPTIMIZATION_MODEL_CLICK = 'CLICK';
  public const OPTIMIZATION_MODEL_POST_CLICK = 'POST_CLICK';
  public const OPTIMIZATION_MODEL_POST_IMPRESSION = 'POST_IMPRESSION';
  public const OPTIMIZATION_MODEL_POST_CLICK_AND_IMPRESSION = 'POST_CLICK_AND_IMPRESSION';
  public const OPTIMIZATION_MODEL_VIDEO_COMPLETION = 'VIDEO_COMPLETION';
  protected $collection_key = 'optimizationActivitys';
  /**
   * ID of this creative optimization config. This field is auto-generated when
   * the campaign is inserted or updated. It can be null for existing campaigns.
   *
   * @var string
   */
  public $id;
  /**
   * Name of this creative optimization config. This is a required field and
   * must be less than 129 characters long.
   *
   * @var string
   */
  public $name;
  protected $optimizationActivitysType = OptimizationActivity::class;
  protected $optimizationActivitysDataType = 'array';
  /**
   * Optimization model for this configuration.
   *
   * @var string
   */
  public $optimizationModel;

  /**
   * ID of this creative optimization config. This field is auto-generated when
   * the campaign is inserted or updated. It can be null for existing campaigns.
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
   * Name of this creative optimization config. This is a required field and
   * must be less than 129 characters long.
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
   * List of optimization activities associated with this configuration.
   *
   * @param OptimizationActivity[] $optimizationActivitys
   */
  public function setOptimizationActivitys($optimizationActivitys)
  {
    $this->optimizationActivitys = $optimizationActivitys;
  }
  /**
   * @return OptimizationActivity[]
   */
  public function getOptimizationActivitys()
  {
    return $this->optimizationActivitys;
  }
  /**
   * Optimization model for this configuration.
   *
   * Accepted values: CLICK, POST_CLICK, POST_IMPRESSION,
   * POST_CLICK_AND_IMPRESSION, VIDEO_COMPLETION
   *
   * @param self::OPTIMIZATION_MODEL_* $optimizationModel
   */
  public function setOptimizationModel($optimizationModel)
  {
    $this->optimizationModel = $optimizationModel;
  }
  /**
   * @return self::OPTIMIZATION_MODEL_*
   */
  public function getOptimizationModel()
  {
    return $this->optimizationModel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeOptimizationConfiguration::class, 'Google_Service_Dfareporting_CreativeOptimizationConfiguration');
