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

namespace Google\Service\TPU;

class AcceleratorConfig extends \Google\Model
{
  /**
   * Unspecified version.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * TPU v2.
   */
  public const TYPE_V2 = 'V2';
  /**
   * TPU v3.
   */
  public const TYPE_V3 = 'V3';
  /**
   * TPU v4.
   */
  public const TYPE_V4 = 'V4';
  /**
   * TPU v5lite pod.
   */
  public const TYPE_V5LITE_POD = 'V5LITE_POD';
  /**
   * TPU v5p.
   */
  public const TYPE_V5P = 'V5P';
  /**
   * TPU v6e.
   */
  public const TYPE_V6E = 'V6E';
  /**
   * Required. Topology of TPU in chips.
   *
   * @var string
   */
  public $topology;
  /**
   * Required. Type of TPU.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Topology of TPU in chips.
   *
   * @param string $topology
   */
  public function setTopology($topology)
  {
    $this->topology = $topology;
  }
  /**
   * @return string
   */
  public function getTopology()
  {
    return $this->topology;
  }
  /**
   * Required. Type of TPU.
   *
   * Accepted values: TYPE_UNSPECIFIED, V2, V3, V4, V5LITE_POD, V5P, V6E
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AcceleratorConfig::class, 'Google_Service_TPU_AcceleratorConfig');
