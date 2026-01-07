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

namespace Google\Service\Container;

class OperationProgress extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The operation has been created.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * The operation is currently running.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The operation is done, either cancelled or completed.
   */
  public const STATUS_DONE = 'DONE';
  /**
   * The operation is aborting.
   */
  public const STATUS_ABORTING = 'ABORTING';
  protected $collection_key = 'stages';
  protected $metricsType = Metric::class;
  protected $metricsDataType = 'array';
  /**
   * A non-parameterized string describing an operation stage. Unset for single-
   * stage operations.
   *
   * @var string
   */
  public $name;
  protected $stagesType = OperationProgress::class;
  protected $stagesDataType = 'array';
  /**
   * Status of an operation stage. Unset for single-stage operations.
   *
   * @var string
   */
  public $status;

  /**
   * Progress metric bundle, for example: metrics: [{name: "nodes done",
   * int_value: 15}, {name: "nodes total", int_value: 32}] or metrics: [{name:
   * "progress", double_value: 0.56}, {name: "progress scale", double_value:
   * 1.0}]
   *
   * @param Metric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return Metric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * A non-parameterized string describing an operation stage. Unset for single-
   * stage operations.
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
   * Substages of an operation or a stage.
   *
   * @param OperationProgress[] $stages
   */
  public function setStages($stages)
  {
    $this->stages = $stages;
  }
  /**
   * @return OperationProgress[]
   */
  public function getStages()
  {
    return $this->stages;
  }
  /**
   * Status of an operation stage. Unset for single-stage operations.
   *
   * Accepted values: STATUS_UNSPECIFIED, PENDING, RUNNING, DONE, ABORTING
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationProgress::class, 'Google_Service_Container_OperationProgress');
