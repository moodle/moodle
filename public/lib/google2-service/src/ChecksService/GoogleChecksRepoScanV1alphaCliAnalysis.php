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

namespace Google\Service\ChecksService;

class GoogleChecksRepoScanV1alphaCliAnalysis extends \Google\Collection
{
  protected $collection_key = 'sources';
  protected $codeScansType = GoogleChecksRepoScanV1alphaCodeScan::class;
  protected $codeScansDataType = 'array';
  protected $sourcesType = GoogleChecksRepoScanV1alphaSource::class;
  protected $sourcesDataType = 'array';

  /**
   * Optional. Requested code scans resulting from preliminary CLI analysis.
   *
   * @param GoogleChecksRepoScanV1alphaCodeScan[] $codeScans
   */
  public function setCodeScans($codeScans)
  {
    $this->codeScans = $codeScans;
  }
  /**
   * @return GoogleChecksRepoScanV1alphaCodeScan[]
   */
  public function getCodeScans()
  {
    return $this->codeScans;
  }
  /**
   * Optional. Data sources detected in the scan.
   *
   * @param GoogleChecksRepoScanV1alphaSource[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return GoogleChecksRepoScanV1alphaSource[]
   */
  public function getSources()
  {
    return $this->sources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksRepoScanV1alphaCliAnalysis::class, 'Google_Service_ChecksService_GoogleChecksRepoScanV1alphaCliAnalysis');
