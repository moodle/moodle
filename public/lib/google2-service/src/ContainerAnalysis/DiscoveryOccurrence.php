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

namespace Google\Service\ContainerAnalysis;

class DiscoveryOccurrence extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const ANALYSIS_STATUS_ANALYSIS_STATUS_UNSPECIFIED = 'ANALYSIS_STATUS_UNSPECIFIED';
  /**
   * Resource is known but no action has been taken yet.
   */
  public const ANALYSIS_STATUS_PENDING = 'PENDING';
  /**
   * Resource is being analyzed.
   */
  public const ANALYSIS_STATUS_SCANNING = 'SCANNING';
  /**
   * Analysis has finished successfully.
   */
  public const ANALYSIS_STATUS_FINISHED_SUCCESS = 'FINISHED_SUCCESS';
  /**
   * Analysis has completed.
   */
  public const ANALYSIS_STATUS_COMPLETE = 'COMPLETE';
  /**
   * Analysis has finished unsuccessfully, the analysis itself is in a bad
   * state.
   */
  public const ANALYSIS_STATUS_FINISHED_FAILED = 'FINISHED_FAILED';
  /**
   * The resource is known not to be supported.
   */
  public const ANALYSIS_STATUS_FINISHED_UNSUPPORTED = 'FINISHED_UNSUPPORTED';
  /**
   * Unknown.
   */
  public const CONTINUOUS_ANALYSIS_CONTINUOUS_ANALYSIS_UNSPECIFIED = 'CONTINUOUS_ANALYSIS_UNSPECIFIED';
  /**
   * The resource is continuously analyzed.
   */
  public const CONTINUOUS_ANALYSIS_ACTIVE = 'ACTIVE';
  /**
   * The resource is ignored for continuous analysis.
   */
  public const CONTINUOUS_ANALYSIS_INACTIVE = 'INACTIVE';
  protected $collection_key = 'files';
  protected $analysisCompletedType = AnalysisCompleted::class;
  protected $analysisCompletedDataType = '';
  protected $analysisErrorType = Status::class;
  protected $analysisErrorDataType = 'array';
  /**
   * The status of discovery for the resource.
   *
   * @var string
   */
  public $analysisStatus;
  protected $analysisStatusErrorType = Status::class;
  protected $analysisStatusErrorDataType = '';
  /**
   * Output only. The time occurrences related to this discovery occurrence were
   * archived.
   *
   * @var string
   */
  public $archiveTime;
  /**
   * Whether the resource is continuously analyzed.
   *
   * @var string
   */
  public $continuousAnalysis;
  /**
   * The CPE of the resource being scanned.
   *
   * @var string
   */
  public $cpe;
  protected $filesType = ContaineranalysisFile::class;
  protected $filesDataType = 'array';
  /**
   * The last time this resource was scanned.
   *
   * @var string
   */
  public $lastScanTime;
  protected $sbomStatusType = SBOMStatus::class;
  protected $sbomStatusDataType = '';

  /**
   * @param AnalysisCompleted $analysisCompleted
   */
  public function setAnalysisCompleted(AnalysisCompleted $analysisCompleted)
  {
    $this->analysisCompleted = $analysisCompleted;
  }
  /**
   * @return AnalysisCompleted
   */
  public function getAnalysisCompleted()
  {
    return $this->analysisCompleted;
  }
  /**
   * Indicates any errors encountered during analysis of a resource. There could
   * be 0 or more of these errors.
   *
   * @param Status[] $analysisError
   */
  public function setAnalysisError($analysisError)
  {
    $this->analysisError = $analysisError;
  }
  /**
   * @return Status[]
   */
  public function getAnalysisError()
  {
    return $this->analysisError;
  }
  /**
   * The status of discovery for the resource.
   *
   * Accepted values: ANALYSIS_STATUS_UNSPECIFIED, PENDING, SCANNING,
   * FINISHED_SUCCESS, COMPLETE, FINISHED_FAILED, FINISHED_UNSUPPORTED
   *
   * @param self::ANALYSIS_STATUS_* $analysisStatus
   */
  public function setAnalysisStatus($analysisStatus)
  {
    $this->analysisStatus = $analysisStatus;
  }
  /**
   * @return self::ANALYSIS_STATUS_*
   */
  public function getAnalysisStatus()
  {
    return $this->analysisStatus;
  }
  /**
   * When an error is encountered this will contain a LocalizedMessage under
   * details to show to the user. The LocalizedMessage is output only and
   * populated by the API.
   *
   * @param Status $analysisStatusError
   */
  public function setAnalysisStatusError(Status $analysisStatusError)
  {
    $this->analysisStatusError = $analysisStatusError;
  }
  /**
   * @return Status
   */
  public function getAnalysisStatusError()
  {
    return $this->analysisStatusError;
  }
  /**
   * Output only. The time occurrences related to this discovery occurrence were
   * archived.
   *
   * @param string $archiveTime
   */
  public function setArchiveTime($archiveTime)
  {
    $this->archiveTime = $archiveTime;
  }
  /**
   * @return string
   */
  public function getArchiveTime()
  {
    return $this->archiveTime;
  }
  /**
   * Whether the resource is continuously analyzed.
   *
   * Accepted values: CONTINUOUS_ANALYSIS_UNSPECIFIED, ACTIVE, INACTIVE
   *
   * @param self::CONTINUOUS_ANALYSIS_* $continuousAnalysis
   */
  public function setContinuousAnalysis($continuousAnalysis)
  {
    $this->continuousAnalysis = $continuousAnalysis;
  }
  /**
   * @return self::CONTINUOUS_ANALYSIS_*
   */
  public function getContinuousAnalysis()
  {
    return $this->continuousAnalysis;
  }
  /**
   * The CPE of the resource being scanned.
   *
   * @param string $cpe
   */
  public function setCpe($cpe)
  {
    $this->cpe = $cpe;
  }
  /**
   * @return string
   */
  public function getCpe()
  {
    return $this->cpe;
  }
  /**
   * Files that make up the resource described by the occurrence.
   *
   * @param ContaineranalysisFile[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return ContaineranalysisFile[]
   */
  public function getFiles()
  {
    return $this->files;
  }
  /**
   * The last time this resource was scanned.
   *
   * @param string $lastScanTime
   */
  public function setLastScanTime($lastScanTime)
  {
    $this->lastScanTime = $lastScanTime;
  }
  /**
   * @return string
   */
  public function getLastScanTime()
  {
    return $this->lastScanTime;
  }
  /**
   * The status of an SBOM generation.
   *
   * @param SBOMStatus $sbomStatus
   */
  public function setSbomStatus(SBOMStatus $sbomStatus)
  {
    $this->sbomStatus = $sbomStatus;
  }
  /**
   * @return SBOMStatus
   */
  public function getSbomStatus()
  {
    return $this->sbomStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoveryOccurrence::class, 'Google_Service_ContainerAnalysis_DiscoveryOccurrence');
