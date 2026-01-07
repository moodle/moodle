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

class BiEngineStatistics extends \Google\Collection
{
  /**
   * BiEngineMode type not specified.
   */
  public const ACCELERATION_MODE_BI_ENGINE_ACCELERATION_MODE_UNSPECIFIED = 'BI_ENGINE_ACCELERATION_MODE_UNSPECIFIED';
  /**
   * BI Engine acceleration was attempted but disabled. bi_engine_reasons
   * specifies a more detailed reason.
   */
  public const ACCELERATION_MODE_BI_ENGINE_DISABLED = 'BI_ENGINE_DISABLED';
  /**
   * Some inputs were accelerated using BI Engine. See bi_engine_reasons for why
   * parts of the query were not accelerated.
   */
  public const ACCELERATION_MODE_PARTIAL_INPUT = 'PARTIAL_INPUT';
  /**
   * All of the query inputs were accelerated using BI Engine.
   */
  public const ACCELERATION_MODE_FULL_INPUT = 'FULL_INPUT';
  /**
   * All of the query was accelerated using BI Engine.
   */
  public const ACCELERATION_MODE_FULL_QUERY = 'FULL_QUERY';
  /**
   * BiEngineMode type not specified.
   */
  public const BI_ENGINE_MODE_ACCELERATION_MODE_UNSPECIFIED = 'ACCELERATION_MODE_UNSPECIFIED';
  /**
   * BI Engine disabled the acceleration. bi_engine_reasons specifies a more
   * detailed reason.
   */
  public const BI_ENGINE_MODE_DISABLED = 'DISABLED';
  /**
   * Part of the query was accelerated using BI Engine. See bi_engine_reasons
   * for why parts of the query were not accelerated.
   */
  public const BI_ENGINE_MODE_PARTIAL = 'PARTIAL';
  /**
   * All of the query was accelerated using BI Engine.
   */
  public const BI_ENGINE_MODE_FULL = 'FULL';
  protected $collection_key = 'biEngineReasons';
  /**
   * Output only. Specifies which mode of BI Engine acceleration was performed
   * (if any).
   *
   * @var string
   */
  public $accelerationMode;
  /**
   * Output only. Specifies which mode of BI Engine acceleration was performed
   * (if any).
   *
   * @var string
   */
  public $biEngineMode;
  protected $biEngineReasonsType = BiEngineReason::class;
  protected $biEngineReasonsDataType = 'array';

  /**
   * Output only. Specifies which mode of BI Engine acceleration was performed
   * (if any).
   *
   * Accepted values: BI_ENGINE_ACCELERATION_MODE_UNSPECIFIED,
   * BI_ENGINE_DISABLED, PARTIAL_INPUT, FULL_INPUT, FULL_QUERY
   *
   * @param self::ACCELERATION_MODE_* $accelerationMode
   */
  public function setAccelerationMode($accelerationMode)
  {
    $this->accelerationMode = $accelerationMode;
  }
  /**
   * @return self::ACCELERATION_MODE_*
   */
  public function getAccelerationMode()
  {
    return $this->accelerationMode;
  }
  /**
   * Output only. Specifies which mode of BI Engine acceleration was performed
   * (if any).
   *
   * Accepted values: ACCELERATION_MODE_UNSPECIFIED, DISABLED, PARTIAL, FULL
   *
   * @param self::BI_ENGINE_MODE_* $biEngineMode
   */
  public function setBiEngineMode($biEngineMode)
  {
    $this->biEngineMode = $biEngineMode;
  }
  /**
   * @return self::BI_ENGINE_MODE_*
   */
  public function getBiEngineMode()
  {
    return $this->biEngineMode;
  }
  /**
   * In case of DISABLED or PARTIAL bi_engine_mode, these contain the
   * explanatory reasons as to why BI Engine could not accelerate. In case the
   * full query was accelerated, this field is not populated.
   *
   * @param BiEngineReason[] $biEngineReasons
   */
  public function setBiEngineReasons($biEngineReasons)
  {
    $this->biEngineReasons = $biEngineReasons;
  }
  /**
   * @return BiEngineReason[]
   */
  public function getBiEngineReasons()
  {
    return $this->biEngineReasons;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BiEngineStatistics::class, 'Google_Service_Bigquery_BiEngineStatistics');
