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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1PairwiseMetricSpec extends \Google\Model
{
  /**
   * Optional. The field name of the baseline response.
   *
   * @var string
   */
  public $baselineResponseFieldName;
  /**
   * Optional. The field name of the candidate response.
   *
   * @var string
   */
  public $candidateResponseFieldName;
  protected $customOutputFormatConfigType = GoogleCloudAiplatformV1CustomOutputFormatConfig::class;
  protected $customOutputFormatConfigDataType = '';
  /**
   * Required. Metric prompt template for pairwise metric.
   *
   * @var string
   */
  public $metricPromptTemplate;
  /**
   * Optional. System instructions for pairwise metric.
   *
   * @var string
   */
  public $systemInstruction;

  /**
   * Optional. The field name of the baseline response.
   *
   * @param string $baselineResponseFieldName
   */
  public function setBaselineResponseFieldName($baselineResponseFieldName)
  {
    $this->baselineResponseFieldName = $baselineResponseFieldName;
  }
  /**
   * @return string
   */
  public function getBaselineResponseFieldName()
  {
    return $this->baselineResponseFieldName;
  }
  /**
   * Optional. The field name of the candidate response.
   *
   * @param string $candidateResponseFieldName
   */
  public function setCandidateResponseFieldName($candidateResponseFieldName)
  {
    $this->candidateResponseFieldName = $candidateResponseFieldName;
  }
  /**
   * @return string
   */
  public function getCandidateResponseFieldName()
  {
    return $this->candidateResponseFieldName;
  }
  /**
   * Optional. CustomOutputFormatConfig allows customization of metric output.
   * When this config is set, the default output is replaced with the raw output
   * string. If a custom format is chosen, the `pairwise_choice` and
   * `explanation` fields in the corresponding metric result will be empty.
   *
   * @param GoogleCloudAiplatformV1CustomOutputFormatConfig $customOutputFormatConfig
   */
  public function setCustomOutputFormatConfig(GoogleCloudAiplatformV1CustomOutputFormatConfig $customOutputFormatConfig)
  {
    $this->customOutputFormatConfig = $customOutputFormatConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1CustomOutputFormatConfig
   */
  public function getCustomOutputFormatConfig()
  {
    return $this->customOutputFormatConfig;
  }
  /**
   * Required. Metric prompt template for pairwise metric.
   *
   * @param string $metricPromptTemplate
   */
  public function setMetricPromptTemplate($metricPromptTemplate)
  {
    $this->metricPromptTemplate = $metricPromptTemplate;
  }
  /**
   * @return string
   */
  public function getMetricPromptTemplate()
  {
    return $this->metricPromptTemplate;
  }
  /**
   * Optional. System instructions for pairwise metric.
   *
   * @param string $systemInstruction
   */
  public function setSystemInstruction($systemInstruction)
  {
    $this->systemInstruction = $systemInstruction;
  }
  /**
   * @return string
   */
  public function getSystemInstruction()
  {
    return $this->systemInstruction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PairwiseMetricSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PairwiseMetricSpec');
