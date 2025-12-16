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

namespace Google\Service\ServiceUsage;

class GoogleApiServiceusageV2betaAnalysis extends \Google\Model
{
  /**
   * Unspecified analysis type. Do not use.
   */
  public const ANALYSIS_TYPE_ANALYSIS_TYPE_UNSPECIFIED = 'ANALYSIS_TYPE_UNSPECIFIED';
  /**
   * The analysis of service dependencies.
   */
  public const ANALYSIS_TYPE_ANALYSIS_TYPE_DEPENDENCY = 'ANALYSIS_TYPE_DEPENDENCY';
  /**
   * The analysis of service resource usage.
   */
  public const ANALYSIS_TYPE_ANALYSIS_TYPE_RESOURCE_USAGE = 'ANALYSIS_TYPE_RESOURCE_USAGE';
  protected $analysisResultType = GoogleApiServiceusageV2betaAnalysisResult::class;
  protected $analysisResultDataType = '';
  /**
   * Output only. The type of analysis.
   *
   * @var string
   */
  public $analysisType;
  /**
   * Output only. The user friendly display name of the analysis type. E.g.
   * service dependency analysis, service resource usage analysis, etc.
   *
   * @var string
   */
  public $displayName;
  /**
   * The names of the service that has analysis result of warnings or blockers.
   * Example: `services/storage.googleapis.com`.
   *
   * @var string
   */
  public $service;

  /**
   * Output only. Analysis result of updating a policy.
   *
   * @param GoogleApiServiceusageV2betaAnalysisResult $analysisResult
   */
  public function setAnalysisResult(GoogleApiServiceusageV2betaAnalysisResult $analysisResult)
  {
    $this->analysisResult = $analysisResult;
  }
  /**
   * @return GoogleApiServiceusageV2betaAnalysisResult
   */
  public function getAnalysisResult()
  {
    return $this->analysisResult;
  }
  /**
   * Output only. The type of analysis.
   *
   * Accepted values: ANALYSIS_TYPE_UNSPECIFIED, ANALYSIS_TYPE_DEPENDENCY,
   * ANALYSIS_TYPE_RESOURCE_USAGE
   *
   * @param self::ANALYSIS_TYPE_* $analysisType
   */
  public function setAnalysisType($analysisType)
  {
    $this->analysisType = $analysisType;
  }
  /**
   * @return self::ANALYSIS_TYPE_*
   */
  public function getAnalysisType()
  {
    return $this->analysisType;
  }
  /**
   * Output only. The user friendly display name of the analysis type. E.g.
   * service dependency analysis, service resource usage analysis, etc.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The names of the service that has analysis result of warnings or blockers.
   * Example: `services/storage.googleapis.com`.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiServiceusageV2betaAnalysis::class, 'Google_Service_ServiceUsage_GoogleApiServiceusageV2betaAnalysis');
