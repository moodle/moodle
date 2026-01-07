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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1Analysis extends \Google\Model
{
  protected $analysisResultType = GoogleCloudContactcenterinsightsV1AnalysisResult::class;
  protected $analysisResultDataType = '';
  protected $annotatorSelectorType = GoogleCloudContactcenterinsightsV1AnnotatorSelector::class;
  protected $annotatorSelectorDataType = '';
  /**
   * Output only. The time at which the analysis was created, which occurs when
   * the long-running operation completes.
   *
   * @var string
   */
  public $createTime;
  /**
   * Immutable. The resource name of the analysis. Format: projects/{project}/lo
   * cations/{location}/conversations/{conversation}/analyses/{analysis}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time at which the analysis was requested.
   *
   * @var string
   */
  public $requestTime;

  /**
   * Output only. The result of the analysis, which is populated when the
   * analysis finishes.
   *
   * @param GoogleCloudContactcenterinsightsV1AnalysisResult $analysisResult
   */
  public function setAnalysisResult(GoogleCloudContactcenterinsightsV1AnalysisResult $analysisResult)
  {
    $this->analysisResult = $analysisResult;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnalysisResult
   */
  public function getAnalysisResult()
  {
    return $this->analysisResult;
  }
  /**
   * To select the annotators to run and the phrase matchers to use (if any). If
   * not specified, all annotators will be run.
   *
   * @param GoogleCloudContactcenterinsightsV1AnnotatorSelector $annotatorSelector
   */
  public function setAnnotatorSelector(GoogleCloudContactcenterinsightsV1AnnotatorSelector $annotatorSelector)
  {
    $this->annotatorSelector = $annotatorSelector;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AnnotatorSelector
   */
  public function getAnnotatorSelector()
  {
    return $this->annotatorSelector;
  }
  /**
   * Output only. The time at which the analysis was created, which occurs when
   * the long-running operation completes.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Immutable. The resource name of the analysis. Format: projects/{project}/lo
   * cations/{location}/conversations/{conversation}/analyses/{analysis}
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
   * Output only. The time at which the analysis was requested.
   *
   * @param string $requestTime
   */
  public function setRequestTime($requestTime)
  {
    $this->requestTime = $requestTime;
  }
  /**
   * @return string
   */
  public function getRequestTime()
  {
    return $this->requestTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1Analysis::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1Analysis');
