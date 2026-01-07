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

namespace Google\Service\CloudNaturalLanguage;

class TpuMetric extends \Google\Model
{
  public const TPU_TYPE_UNKNOWN_TPU_TYPE = 'UNKNOWN_TPU_TYPE';
  public const TPU_TYPE_TPU_V2_POD = 'TPU_V2_POD';
  public const TPU_TYPE_TPU_V2 = 'TPU_V2';
  public const TPU_TYPE_TPU_V3_POD = 'TPU_V3_POD';
  public const TPU_TYPE_TPU_V3 = 'TPU_V3';
  public const TPU_TYPE_TPU_V5_LITEPOD = 'TPU_V5_LITEPOD';
  /**
   * Required. Seconds of TPU usage, e.g. 3600.
   *
   * @var string
   */
  public $tpuSec;
  /**
   * Required. Type of TPU, e.g. TPU_V2, TPU_V3_POD.
   *
   * @var string
   */
  public $tpuType;

  /**
   * Required. Seconds of TPU usage, e.g. 3600.
   *
   * @param string $tpuSec
   */
  public function setTpuSec($tpuSec)
  {
    $this->tpuSec = $tpuSec;
  }
  /**
   * @return string
   */
  public function getTpuSec()
  {
    return $this->tpuSec;
  }
  /**
   * Required. Type of TPU, e.g. TPU_V2, TPU_V3_POD.
   *
   * Accepted values: UNKNOWN_TPU_TYPE, TPU_V2_POD, TPU_V2, TPU_V3_POD, TPU_V3,
   * TPU_V5_LITEPOD
   *
   * @param self::TPU_TYPE_* $tpuType
   */
  public function setTpuType($tpuType)
  {
    $this->tpuType = $tpuType;
  }
  /**
   * @return self::TPU_TYPE_*
   */
  public function getTpuType()
  {
    return $this->tpuType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TpuMetric::class, 'Google_Service_CloudNaturalLanguage_TpuMetric');
