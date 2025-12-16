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

class RamMetric extends \Google\Model
{
  public const MACHINE_SPEC_UNKNOWN_MACHINE_SPEC = 'UNKNOWN_MACHINE_SPEC';
  public const MACHINE_SPEC_N1_STANDARD_2 = 'N1_STANDARD_2';
  public const MACHINE_SPEC_N1_STANDARD_4 = 'N1_STANDARD_4';
  public const MACHINE_SPEC_N1_STANDARD_8 = 'N1_STANDARD_8';
  public const MACHINE_SPEC_N1_STANDARD_16 = 'N1_STANDARD_16';
  public const MACHINE_SPEC_N1_STANDARD_32 = 'N1_STANDARD_32';
  public const MACHINE_SPEC_N1_STANDARD_64 = 'N1_STANDARD_64';
  public const MACHINE_SPEC_N1_STANDARD_96 = 'N1_STANDARD_96';
  public const MACHINE_SPEC_N1_HIGHMEM_2 = 'N1_HIGHMEM_2';
  public const MACHINE_SPEC_N1_HIGHMEM_4 = 'N1_HIGHMEM_4';
  public const MACHINE_SPEC_N1_HIGHMEM_8 = 'N1_HIGHMEM_8';
  public const MACHINE_SPEC_N1_HIGHMEM_16 = 'N1_HIGHMEM_16';
  public const MACHINE_SPEC_N1_HIGHMEM_32 = 'N1_HIGHMEM_32';
  public const MACHINE_SPEC_N1_HIGHMEM_64 = 'N1_HIGHMEM_64';
  public const MACHINE_SPEC_N1_HIGHMEM_96 = 'N1_HIGHMEM_96';
  public const MACHINE_SPEC_N1_HIGHCPU_2 = 'N1_HIGHCPU_2';
  public const MACHINE_SPEC_N1_HIGHCPU_4 = 'N1_HIGHCPU_4';
  public const MACHINE_SPEC_N1_HIGHCPU_8 = 'N1_HIGHCPU_8';
  public const MACHINE_SPEC_N1_HIGHCPU_16 = 'N1_HIGHCPU_16';
  public const MACHINE_SPEC_N1_HIGHCPU_32 = 'N1_HIGHCPU_32';
  public const MACHINE_SPEC_N1_HIGHCPU_64 = 'N1_HIGHCPU_64';
  public const MACHINE_SPEC_N1_HIGHCPU_96 = 'N1_HIGHCPU_96';
  public const MACHINE_SPEC_A2_HIGHGPU_1G = 'A2_HIGHGPU_1G';
  public const MACHINE_SPEC_A2_HIGHGPU_2G = 'A2_HIGHGPU_2G';
  public const MACHINE_SPEC_A2_HIGHGPU_4G = 'A2_HIGHGPU_4G';
  public const MACHINE_SPEC_A2_HIGHGPU_8G = 'A2_HIGHGPU_8G';
  public const MACHINE_SPEC_A2_MEGAGPU_16G = 'A2_MEGAGPU_16G';
  public const MACHINE_SPEC_A2_ULTRAGPU_1G = 'A2_ULTRAGPU_1G';
  public const MACHINE_SPEC_A2_ULTRAGPU_2G = 'A2_ULTRAGPU_2G';
  public const MACHINE_SPEC_A2_ULTRAGPU_4G = 'A2_ULTRAGPU_4G';
  public const MACHINE_SPEC_A2_ULTRAGPU_8G = 'A2_ULTRAGPU_8G';
  public const MACHINE_SPEC_A3_HIGHGPU_1G = 'A3_HIGHGPU_1G';
  public const MACHINE_SPEC_A3_HIGHGPU_2G = 'A3_HIGHGPU_2G';
  public const MACHINE_SPEC_A3_HIGHGPU_4G = 'A3_HIGHGPU_4G';
  public const MACHINE_SPEC_A3_HIGHGPU_8G = 'A3_HIGHGPU_8G';
  public const MACHINE_SPEC_A3_MEGAGPU_8G = 'A3_MEGAGPU_8G';
  public const MACHINE_SPEC_A3_ULTRAGPU_8G = 'A3_ULTRAGPU_8G';
  public const MACHINE_SPEC_A3_EDGEGPU_8G = 'A3_EDGEGPU_8G';
  public const MACHINE_SPEC_A4_HIGHGPU_8G = 'A4_HIGHGPU_8G';
  public const MACHINE_SPEC_A4X_HIGHGPU_4G = 'A4X_HIGHGPU_4G';
  public const MACHINE_SPEC_E2_STANDARD_2 = 'E2_STANDARD_2';
  public const MACHINE_SPEC_E2_STANDARD_4 = 'E2_STANDARD_4';
  public const MACHINE_SPEC_E2_STANDARD_8 = 'E2_STANDARD_8';
  public const MACHINE_SPEC_E2_STANDARD_16 = 'E2_STANDARD_16';
  public const MACHINE_SPEC_E2_STANDARD_32 = 'E2_STANDARD_32';
  public const MACHINE_SPEC_E2_HIGHMEM_2 = 'E2_HIGHMEM_2';
  public const MACHINE_SPEC_E2_HIGHMEM_4 = 'E2_HIGHMEM_4';
  public const MACHINE_SPEC_E2_HIGHMEM_8 = 'E2_HIGHMEM_8';
  public const MACHINE_SPEC_E2_HIGHMEM_16 = 'E2_HIGHMEM_16';
  public const MACHINE_SPEC_E2_HIGHCPU_2 = 'E2_HIGHCPU_2';
  public const MACHINE_SPEC_E2_HIGHCPU_4 = 'E2_HIGHCPU_4';
  public const MACHINE_SPEC_E2_HIGHCPU_8 = 'E2_HIGHCPU_8';
  public const MACHINE_SPEC_E2_HIGHCPU_16 = 'E2_HIGHCPU_16';
  public const MACHINE_SPEC_E2_HIGHCPU_32 = 'E2_HIGHCPU_32';
  public const MACHINE_SPEC_N2_STANDARD_2 = 'N2_STANDARD_2';
  public const MACHINE_SPEC_N2_STANDARD_4 = 'N2_STANDARD_4';
  public const MACHINE_SPEC_N2_STANDARD_8 = 'N2_STANDARD_8';
  public const MACHINE_SPEC_N2_STANDARD_16 = 'N2_STANDARD_16';
  public const MACHINE_SPEC_N2_STANDARD_32 = 'N2_STANDARD_32';
  public const MACHINE_SPEC_N2_STANDARD_48 = 'N2_STANDARD_48';
  public const MACHINE_SPEC_N2_STANDARD_64 = 'N2_STANDARD_64';
  public const MACHINE_SPEC_N2_STANDARD_80 = 'N2_STANDARD_80';
  public const MACHINE_SPEC_N2_STANDARD_96 = 'N2_STANDARD_96';
  public const MACHINE_SPEC_N2_STANDARD_128 = 'N2_STANDARD_128';
  public const MACHINE_SPEC_N2_HIGHMEM_2 = 'N2_HIGHMEM_2';
  public const MACHINE_SPEC_N2_HIGHMEM_4 = 'N2_HIGHMEM_4';
  public const MACHINE_SPEC_N2_HIGHMEM_8 = 'N2_HIGHMEM_8';
  public const MACHINE_SPEC_N2_HIGHMEM_16 = 'N2_HIGHMEM_16';
  public const MACHINE_SPEC_N2_HIGHMEM_32 = 'N2_HIGHMEM_32';
  public const MACHINE_SPEC_N2_HIGHMEM_48 = 'N2_HIGHMEM_48';
  public const MACHINE_SPEC_N2_HIGHMEM_64 = 'N2_HIGHMEM_64';
  public const MACHINE_SPEC_N2_HIGHMEM_80 = 'N2_HIGHMEM_80';
  public const MACHINE_SPEC_N2_HIGHMEM_96 = 'N2_HIGHMEM_96';
  public const MACHINE_SPEC_N2_HIGHMEM_128 = 'N2_HIGHMEM_128';
  public const MACHINE_SPEC_N2_HIGHCPU_2 = 'N2_HIGHCPU_2';
  public const MACHINE_SPEC_N2_HIGHCPU_4 = 'N2_HIGHCPU_4';
  public const MACHINE_SPEC_N2_HIGHCPU_8 = 'N2_HIGHCPU_8';
  public const MACHINE_SPEC_N2_HIGHCPU_16 = 'N2_HIGHCPU_16';
  public const MACHINE_SPEC_N2_HIGHCPU_32 = 'N2_HIGHCPU_32';
  public const MACHINE_SPEC_N2_HIGHCPU_48 = 'N2_HIGHCPU_48';
  public const MACHINE_SPEC_N2_HIGHCPU_64 = 'N2_HIGHCPU_64';
  public const MACHINE_SPEC_N2_HIGHCPU_80 = 'N2_HIGHCPU_80';
  public const MACHINE_SPEC_N2_HIGHCPU_96 = 'N2_HIGHCPU_96';
  public const MACHINE_SPEC_N2D_STANDARD_2 = 'N2D_STANDARD_2';
  public const MACHINE_SPEC_N2D_STANDARD_4 = 'N2D_STANDARD_4';
  public const MACHINE_SPEC_N2D_STANDARD_8 = 'N2D_STANDARD_8';
  public const MACHINE_SPEC_N2D_STANDARD_16 = 'N2D_STANDARD_16';
  public const MACHINE_SPEC_N2D_STANDARD_32 = 'N2D_STANDARD_32';
  public const MACHINE_SPEC_N2D_STANDARD_48 = 'N2D_STANDARD_48';
  public const MACHINE_SPEC_N2D_STANDARD_64 = 'N2D_STANDARD_64';
  public const MACHINE_SPEC_N2D_STANDARD_80 = 'N2D_STANDARD_80';
  public const MACHINE_SPEC_N2D_STANDARD_96 = 'N2D_STANDARD_96';
  public const MACHINE_SPEC_N2D_STANDARD_128 = 'N2D_STANDARD_128';
  public const MACHINE_SPEC_N2D_STANDARD_224 = 'N2D_STANDARD_224';
  public const MACHINE_SPEC_N2D_HIGHMEM_2 = 'N2D_HIGHMEM_2';
  public const MACHINE_SPEC_N2D_HIGHMEM_4 = 'N2D_HIGHMEM_4';
  public const MACHINE_SPEC_N2D_HIGHMEM_8 = 'N2D_HIGHMEM_8';
  public const MACHINE_SPEC_N2D_HIGHMEM_16 = 'N2D_HIGHMEM_16';
  public const MACHINE_SPEC_N2D_HIGHMEM_32 = 'N2D_HIGHMEM_32';
  public const MACHINE_SPEC_N2D_HIGHMEM_48 = 'N2D_HIGHMEM_48';
  public const MACHINE_SPEC_N2D_HIGHMEM_64 = 'N2D_HIGHMEM_64';
  public const MACHINE_SPEC_N2D_HIGHMEM_80 = 'N2D_HIGHMEM_80';
  public const MACHINE_SPEC_N2D_HIGHMEM_96 = 'N2D_HIGHMEM_96';
  public const MACHINE_SPEC_N2D_HIGHCPU_2 = 'N2D_HIGHCPU_2';
  public const MACHINE_SPEC_N2D_HIGHCPU_4 = 'N2D_HIGHCPU_4';
  public const MACHINE_SPEC_N2D_HIGHCPU_8 = 'N2D_HIGHCPU_8';
  public const MACHINE_SPEC_N2D_HIGHCPU_16 = 'N2D_HIGHCPU_16';
  public const MACHINE_SPEC_N2D_HIGHCPU_32 = 'N2D_HIGHCPU_32';
  public const MACHINE_SPEC_N2D_HIGHCPU_48 = 'N2D_HIGHCPU_48';
  public const MACHINE_SPEC_N2D_HIGHCPU_64 = 'N2D_HIGHCPU_64';
  public const MACHINE_SPEC_N2D_HIGHCPU_80 = 'N2D_HIGHCPU_80';
  public const MACHINE_SPEC_N2D_HIGHCPU_96 = 'N2D_HIGHCPU_96';
  public const MACHINE_SPEC_N2D_HIGHCPU_128 = 'N2D_HIGHCPU_128';
  public const MACHINE_SPEC_N2D_HIGHCPU_224 = 'N2D_HIGHCPU_224';
  public const MACHINE_SPEC_C2_STANDARD_4 = 'C2_STANDARD_4';
  public const MACHINE_SPEC_C2_STANDARD_8 = 'C2_STANDARD_8';
  public const MACHINE_SPEC_C2_STANDARD_16 = 'C2_STANDARD_16';
  public const MACHINE_SPEC_C2_STANDARD_30 = 'C2_STANDARD_30';
  public const MACHINE_SPEC_C2_STANDARD_60 = 'C2_STANDARD_60';
  public const MACHINE_SPEC_C2D_STANDARD_2 = 'C2D_STANDARD_2';
  public const MACHINE_SPEC_C2D_STANDARD_4 = 'C2D_STANDARD_4';
  public const MACHINE_SPEC_C2D_STANDARD_8 = 'C2D_STANDARD_8';
  public const MACHINE_SPEC_C2D_STANDARD_16 = 'C2D_STANDARD_16';
  public const MACHINE_SPEC_C2D_STANDARD_32 = 'C2D_STANDARD_32';
  public const MACHINE_SPEC_C2D_STANDARD_56 = 'C2D_STANDARD_56';
  public const MACHINE_SPEC_C2D_STANDARD_112 = 'C2D_STANDARD_112';
  public const MACHINE_SPEC_C2D_HIGHCPU_2 = 'C2D_HIGHCPU_2';
  public const MACHINE_SPEC_C2D_HIGHCPU_4 = 'C2D_HIGHCPU_4';
  public const MACHINE_SPEC_C2D_HIGHCPU_8 = 'C2D_HIGHCPU_8';
  public const MACHINE_SPEC_C2D_HIGHCPU_16 = 'C2D_HIGHCPU_16';
  public const MACHINE_SPEC_C2D_HIGHCPU_32 = 'C2D_HIGHCPU_32';
  public const MACHINE_SPEC_C2D_HIGHCPU_56 = 'C2D_HIGHCPU_56';
  public const MACHINE_SPEC_C2D_HIGHCPU_112 = 'C2D_HIGHCPU_112';
  public const MACHINE_SPEC_C2D_HIGHMEM_2 = 'C2D_HIGHMEM_2';
  public const MACHINE_SPEC_C2D_HIGHMEM_4 = 'C2D_HIGHMEM_4';
  public const MACHINE_SPEC_C2D_HIGHMEM_8 = 'C2D_HIGHMEM_8';
  public const MACHINE_SPEC_C2D_HIGHMEM_16 = 'C2D_HIGHMEM_16';
  public const MACHINE_SPEC_C2D_HIGHMEM_32 = 'C2D_HIGHMEM_32';
  public const MACHINE_SPEC_C2D_HIGHMEM_56 = 'C2D_HIGHMEM_56';
  public const MACHINE_SPEC_C2D_HIGHMEM_112 = 'C2D_HIGHMEM_112';
  public const MACHINE_SPEC_G2_STANDARD_4 = 'G2_STANDARD_4';
  public const MACHINE_SPEC_G2_STANDARD_8 = 'G2_STANDARD_8';
  public const MACHINE_SPEC_G2_STANDARD_12 = 'G2_STANDARD_12';
  public const MACHINE_SPEC_G2_STANDARD_16 = 'G2_STANDARD_16';
  public const MACHINE_SPEC_G2_STANDARD_24 = 'G2_STANDARD_24';
  public const MACHINE_SPEC_G2_STANDARD_32 = 'G2_STANDARD_32';
  public const MACHINE_SPEC_G2_STANDARD_48 = 'G2_STANDARD_48';
  public const MACHINE_SPEC_G2_STANDARD_96 = 'G2_STANDARD_96';
  public const MACHINE_SPEC_G4_STANDARD_48 = 'G4_STANDARD_48';
  public const MACHINE_SPEC_C3_STANDARD_4 = 'C3_STANDARD_4';
  public const MACHINE_SPEC_C3_STANDARD_8 = 'C3_STANDARD_8';
  public const MACHINE_SPEC_C3_STANDARD_22 = 'C3_STANDARD_22';
  public const MACHINE_SPEC_C3_STANDARD_44 = 'C3_STANDARD_44';
  public const MACHINE_SPEC_C3_STANDARD_88 = 'C3_STANDARD_88';
  public const MACHINE_SPEC_C3_STANDARD_176 = 'C3_STANDARD_176';
  public const MACHINE_SPEC_C3_HIGHCPU_4 = 'C3_HIGHCPU_4';
  public const MACHINE_SPEC_C3_HIGHCPU_8 = 'C3_HIGHCPU_8';
  public const MACHINE_SPEC_C3_HIGHCPU_22 = 'C3_HIGHCPU_22';
  public const MACHINE_SPEC_C3_HIGHCPU_44 = 'C3_HIGHCPU_44';
  public const MACHINE_SPEC_C3_HIGHCPU_88 = 'C3_HIGHCPU_88';
  public const MACHINE_SPEC_C3_HIGHCPU_176 = 'C3_HIGHCPU_176';
  public const MACHINE_SPEC_C3_HIGHMEM_4 = 'C3_HIGHMEM_4';
  public const MACHINE_SPEC_C3_HIGHMEM_8 = 'C3_HIGHMEM_8';
  public const MACHINE_SPEC_C3_HIGHMEM_22 = 'C3_HIGHMEM_22';
  public const MACHINE_SPEC_C3_HIGHMEM_44 = 'C3_HIGHMEM_44';
  public const MACHINE_SPEC_C3_HIGHMEM_88 = 'C3_HIGHMEM_88';
  public const MACHINE_SPEC_C3_HIGHMEM_176 = 'C3_HIGHMEM_176';
  public const MACHINE_SPEC_C4_STANDARD_8 = 'C4_STANDARD_8';
  public const MACHINE_SPEC_C4_STANDARD_16 = 'C4_STANDARD_16';
  public const MACHINE_SPEC_C4_STANDARD_24 = 'C4_STANDARD_24';
  public const MACHINE_SPEC_C4_STANDARD_32 = 'C4_STANDARD_32';
  public const MACHINE_SPEC_C4_STANDARD_48 = 'C4_STANDARD_48';
  public const MACHINE_SPEC_C4_STANDARD_96 = 'C4_STANDARD_96';
  public const MACHINE_SPEC_C4_STANDARD_144 = 'C4_STANDARD_144';
  public const MACHINE_SPEC_C4_STANDARD_192 = 'C4_STANDARD_192';
  public const MACHINE_SPEC_C4_STANDARD_288 = 'C4_STANDARD_288';
  public const MACHINE_SPEC_C4_HIGHCPU_8 = 'C4_HIGHCPU_8';
  public const MACHINE_SPEC_C4_HIGHCPU_16 = 'C4_HIGHCPU_16';
  public const MACHINE_SPEC_C4_HIGHCPU_24 = 'C4_HIGHCPU_24';
  public const MACHINE_SPEC_C4_HIGHCPU_32 = 'C4_HIGHCPU_32';
  public const MACHINE_SPEC_C4_HIGHCPU_48 = 'C4_HIGHCPU_48';
  public const MACHINE_SPEC_C4_HIGHCPU_96 = 'C4_HIGHCPU_96';
  public const MACHINE_SPEC_C4_HIGHCPU_144 = 'C4_HIGHCPU_144';
  public const MACHINE_SPEC_C4_HIGHCPU_192 = 'C4_HIGHCPU_192';
  public const MACHINE_SPEC_C4_HIGHCPU_288 = 'C4_HIGHCPU_288';
  public const MACHINE_SPEC_C4_HIGHMEM_8 = 'C4_HIGHMEM_8';
  public const MACHINE_SPEC_C4_HIGHMEM_16 = 'C4_HIGHMEM_16';
  public const MACHINE_SPEC_C4_HIGHMEM_24 = 'C4_HIGHMEM_24';
  public const MACHINE_SPEC_C4_HIGHMEM_32 = 'C4_HIGHMEM_32';
  public const MACHINE_SPEC_C4_HIGHMEM_48 = 'C4_HIGHMEM_48';
  public const MACHINE_SPEC_C4_HIGHMEM_96 = 'C4_HIGHMEM_96';
  public const MACHINE_SPEC_C4_HIGHMEM_144 = 'C4_HIGHMEM_144';
  public const MACHINE_SPEC_C4_HIGHMEM_192 = 'C4_HIGHMEM_192';
  public const MACHINE_SPEC_C4_HIGHMEM_288 = 'C4_HIGHMEM_288';
  public const RAM_TYPE_UNKNOWN_RAM_TYPE = 'UNKNOWN_RAM_TYPE';
  public const RAM_TYPE_A2 = 'A2';
  public const RAM_TYPE_A3 = 'A3';
  public const RAM_TYPE_A4 = 'A4';
  public const RAM_TYPE_A4X = 'A4X';
  /**
   * COMPUTE_OPTIMIZED
   */
  public const RAM_TYPE_C2 = 'C2';
  public const RAM_TYPE_C2D = 'C2D';
  public const RAM_TYPE_CUSTOM = 'CUSTOM';
  public const RAM_TYPE_E2 = 'E2';
  public const RAM_TYPE_G2 = 'G2';
  public const RAM_TYPE_G4 = 'G4';
  public const RAM_TYPE_C4 = 'C4';
  public const RAM_TYPE_C3 = 'C3';
  /**
   * MEMORY_OPTIMIZED_UPGRADE_PREMIUM
   */
  public const RAM_TYPE_M2 = 'M2';
  /**
   * MEMORY_OPTIMIZED
   */
  public const RAM_TYPE_M1 = 'M1';
  public const RAM_TYPE_N1 = 'N1';
  public const RAM_TYPE_N2_CUSTOM = 'N2_CUSTOM';
  public const RAM_TYPE_N2 = 'N2';
  public const RAM_TYPE_N2D = 'N2D';
  /**
   * Required. VM memory in Gigabyte second, e.g. 3600. Using int64 type to
   * match billing metrics definition.
   *
   * @var string
   */
  public $gibSec;
  /**
   * Required. Machine spec, e.g. N1_STANDARD_4.
   *
   * @var string
   */
  public $machineSpec;
  /**
   * Required. VM memory in gb.
   *
   * @var 
   */
  public $memories;
  /**
   * Required. Type of ram.
   *
   * @var string
   */
  public $ramType;
  /**
   * Billing tracking labels. They do not contain any user data but only the
   * labels set by Vertex Core Infra itself. Tracking labels' keys are defined
   * with special format: goog-[\p{Ll}\p{N}]+ E.g. "key": "goog-k8s-cluster-
   * name","value": "us-east1-b4rk"
   *
   * @var string[]
   */
  public $trackingLabels;

  /**
   * Required. VM memory in Gigabyte second, e.g. 3600. Using int64 type to
   * match billing metrics definition.
   *
   * @param string $gibSec
   */
  public function setGibSec($gibSec)
  {
    $this->gibSec = $gibSec;
  }
  /**
   * @return string
   */
  public function getGibSec()
  {
    return $this->gibSec;
  }
  /**
   * Required. Machine spec, e.g. N1_STANDARD_4.
   *
   * Accepted values: UNKNOWN_MACHINE_SPEC, N1_STANDARD_2, N1_STANDARD_4,
   * N1_STANDARD_8, N1_STANDARD_16, N1_STANDARD_32, N1_STANDARD_64,
   * N1_STANDARD_96, N1_HIGHMEM_2, N1_HIGHMEM_4, N1_HIGHMEM_8, N1_HIGHMEM_16,
   * N1_HIGHMEM_32, N1_HIGHMEM_64, N1_HIGHMEM_96, N1_HIGHCPU_2, N1_HIGHCPU_4,
   * N1_HIGHCPU_8, N1_HIGHCPU_16, N1_HIGHCPU_32, N1_HIGHCPU_64, N1_HIGHCPU_96,
   * A2_HIGHGPU_1G, A2_HIGHGPU_2G, A2_HIGHGPU_4G, A2_HIGHGPU_8G, A2_MEGAGPU_16G,
   * A2_ULTRAGPU_1G, A2_ULTRAGPU_2G, A2_ULTRAGPU_4G, A2_ULTRAGPU_8G,
   * A3_HIGHGPU_1G, A3_HIGHGPU_2G, A3_HIGHGPU_4G, A3_HIGHGPU_8G, A3_MEGAGPU_8G,
   * A3_ULTRAGPU_8G, A3_EDGEGPU_8G, A4_HIGHGPU_8G, A4X_HIGHGPU_4G,
   * E2_STANDARD_2, E2_STANDARD_4, E2_STANDARD_8, E2_STANDARD_16,
   * E2_STANDARD_32, E2_HIGHMEM_2, E2_HIGHMEM_4, E2_HIGHMEM_8, E2_HIGHMEM_16,
   * E2_HIGHCPU_2, E2_HIGHCPU_4, E2_HIGHCPU_8, E2_HIGHCPU_16, E2_HIGHCPU_32,
   * N2_STANDARD_2, N2_STANDARD_4, N2_STANDARD_8, N2_STANDARD_16,
   * N2_STANDARD_32, N2_STANDARD_48, N2_STANDARD_64, N2_STANDARD_80,
   * N2_STANDARD_96, N2_STANDARD_128, N2_HIGHMEM_2, N2_HIGHMEM_4, N2_HIGHMEM_8,
   * N2_HIGHMEM_16, N2_HIGHMEM_32, N2_HIGHMEM_48, N2_HIGHMEM_64, N2_HIGHMEM_80,
   * N2_HIGHMEM_96, N2_HIGHMEM_128, N2_HIGHCPU_2, N2_HIGHCPU_4, N2_HIGHCPU_8,
   * N2_HIGHCPU_16, N2_HIGHCPU_32, N2_HIGHCPU_48, N2_HIGHCPU_64, N2_HIGHCPU_80,
   * N2_HIGHCPU_96, N2D_STANDARD_2, N2D_STANDARD_4, N2D_STANDARD_8,
   * N2D_STANDARD_16, N2D_STANDARD_32, N2D_STANDARD_48, N2D_STANDARD_64,
   * N2D_STANDARD_80, N2D_STANDARD_96, N2D_STANDARD_128, N2D_STANDARD_224,
   * N2D_HIGHMEM_2, N2D_HIGHMEM_4, N2D_HIGHMEM_8, N2D_HIGHMEM_16,
   * N2D_HIGHMEM_32, N2D_HIGHMEM_48, N2D_HIGHMEM_64, N2D_HIGHMEM_80,
   * N2D_HIGHMEM_96, N2D_HIGHCPU_2, N2D_HIGHCPU_4, N2D_HIGHCPU_8,
   * N2D_HIGHCPU_16, N2D_HIGHCPU_32, N2D_HIGHCPU_48, N2D_HIGHCPU_64,
   * N2D_HIGHCPU_80, N2D_HIGHCPU_96, N2D_HIGHCPU_128, N2D_HIGHCPU_224,
   * C2_STANDARD_4, C2_STANDARD_8, C2_STANDARD_16, C2_STANDARD_30,
   * C2_STANDARD_60, C2D_STANDARD_2, C2D_STANDARD_4, C2D_STANDARD_8,
   * C2D_STANDARD_16, C2D_STANDARD_32, C2D_STANDARD_56, C2D_STANDARD_112,
   * C2D_HIGHCPU_2, C2D_HIGHCPU_4, C2D_HIGHCPU_8, C2D_HIGHCPU_16,
   * C2D_HIGHCPU_32, C2D_HIGHCPU_56, C2D_HIGHCPU_112, C2D_HIGHMEM_2,
   * C2D_HIGHMEM_4, C2D_HIGHMEM_8, C2D_HIGHMEM_16, C2D_HIGHMEM_32,
   * C2D_HIGHMEM_56, C2D_HIGHMEM_112, G2_STANDARD_4, G2_STANDARD_8,
   * G2_STANDARD_12, G2_STANDARD_16, G2_STANDARD_24, G2_STANDARD_32,
   * G2_STANDARD_48, G2_STANDARD_96, G4_STANDARD_48, C3_STANDARD_4,
   * C3_STANDARD_8, C3_STANDARD_22, C3_STANDARD_44, C3_STANDARD_88,
   * C3_STANDARD_176, C3_HIGHCPU_4, C3_HIGHCPU_8, C3_HIGHCPU_22, C3_HIGHCPU_44,
   * C3_HIGHCPU_88, C3_HIGHCPU_176, C3_HIGHMEM_4, C3_HIGHMEM_8, C3_HIGHMEM_22,
   * C3_HIGHMEM_44, C3_HIGHMEM_88, C3_HIGHMEM_176, C4_STANDARD_8,
   * C4_STANDARD_16, C4_STANDARD_24, C4_STANDARD_32, C4_STANDARD_48,
   * C4_STANDARD_96, C4_STANDARD_144, C4_STANDARD_192, C4_STANDARD_288,
   * C4_HIGHCPU_8, C4_HIGHCPU_16, C4_HIGHCPU_24, C4_HIGHCPU_32, C4_HIGHCPU_48,
   * C4_HIGHCPU_96, C4_HIGHCPU_144, C4_HIGHCPU_192, C4_HIGHCPU_288,
   * C4_HIGHMEM_8, C4_HIGHMEM_16, C4_HIGHMEM_24, C4_HIGHMEM_32, C4_HIGHMEM_48,
   * C4_HIGHMEM_96, C4_HIGHMEM_144, C4_HIGHMEM_192, C4_HIGHMEM_288
   *
   * @param self::MACHINE_SPEC_* $machineSpec
   */
  public function setMachineSpec($machineSpec)
  {
    $this->machineSpec = $machineSpec;
  }
  /**
   * @return self::MACHINE_SPEC_*
   */
  public function getMachineSpec()
  {
    return $this->machineSpec;
  }
  public function setMemories($memories)
  {
    $this->memories = $memories;
  }
  public function getMemories()
  {
    return $this->memories;
  }
  /**
   * Required. Type of ram.
   *
   * Accepted values: UNKNOWN_RAM_TYPE, A2, A3, A4, A4X, C2, C2D, CUSTOM, E2,
   * G2, G4, C4, C3, M2, M1, N1, N2_CUSTOM, N2, N2D
   *
   * @param self::RAM_TYPE_* $ramType
   */
  public function setRamType($ramType)
  {
    $this->ramType = $ramType;
  }
  /**
   * @return self::RAM_TYPE_*
   */
  public function getRamType()
  {
    return $this->ramType;
  }
  /**
   * Billing tracking labels. They do not contain any user data but only the
   * labels set by Vertex Core Infra itself. Tracking labels' keys are defined
   * with special format: goog-[\p{Ll}\p{N}]+ E.g. "key": "goog-k8s-cluster-
   * name","value": "us-east1-b4rk"
   *
   * @param string[] $trackingLabels
   */
  public function setTrackingLabels($trackingLabels)
  {
    $this->trackingLabels = $trackingLabels;
  }
  /**
   * @return string[]
   */
  public function getTrackingLabels()
  {
    return $this->trackingLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RamMetric::class, 'Google_Service_CloudNaturalLanguage_RamMetric');
