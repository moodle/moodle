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

class GoogleChecksRepoScanV1alphaGenerateScanRequest extends \Google\Model
{
  protected $cliAnalysisType = GoogleChecksRepoScanV1alphaCliAnalysis::class;
  protected $cliAnalysisDataType = '';
  /**
   * Required. CLI version.
   *
   * @var string
   */
  public $cliVersion;
  /**
   * Required. Local scan path.
   *
   * @var string
   */
  public $localScanPath;
  protected $scmMetadataType = GoogleChecksRepoScanV1alphaScmMetadata::class;
  protected $scmMetadataDataType = '';

  /**
   * Required. CLI analysis results.
   *
   * @param GoogleChecksRepoScanV1alphaCliAnalysis $cliAnalysis
   */
  public function setCliAnalysis(GoogleChecksRepoScanV1alphaCliAnalysis $cliAnalysis)
  {
    $this->cliAnalysis = $cliAnalysis;
  }
  /**
   * @return GoogleChecksRepoScanV1alphaCliAnalysis
   */
  public function getCliAnalysis()
  {
    return $this->cliAnalysis;
  }
  /**
   * Required. CLI version.
   *
   * @param string $cliVersion
   */
  public function setCliVersion($cliVersion)
  {
    $this->cliVersion = $cliVersion;
  }
  /**
   * @return string
   */
  public function getCliVersion()
  {
    return $this->cliVersion;
  }
  /**
   * Required. Local scan path.
   *
   * @param string $localScanPath
   */
  public function setLocalScanPath($localScanPath)
  {
    $this->localScanPath = $localScanPath;
  }
  /**
   * @return string
   */
  public function getLocalScanPath()
  {
    return $this->localScanPath;
  }
  /**
   * Required. SCM metadata.
   *
   * @param GoogleChecksRepoScanV1alphaScmMetadata $scmMetadata
   */
  public function setScmMetadata(GoogleChecksRepoScanV1alphaScmMetadata $scmMetadata)
  {
    $this->scmMetadata = $scmMetadata;
  }
  /**
   * @return GoogleChecksRepoScanV1alphaScmMetadata
   */
  public function getScmMetadata()
  {
    return $this->scmMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksRepoScanV1alphaGenerateScanRequest::class, 'Google_Service_ChecksService_GoogleChecksRepoScanV1alphaGenerateScanRequest');
