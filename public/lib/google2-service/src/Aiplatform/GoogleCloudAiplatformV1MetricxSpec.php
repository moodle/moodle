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

class GoogleCloudAiplatformV1MetricxSpec extends \Google\Model
{
  /**
   * MetricX version unspecified.
   */
  public const VERSION_METRICX_VERSION_UNSPECIFIED = 'METRICX_VERSION_UNSPECIFIED';
  /**
   * MetricX 2024 (2.6) for translation + reference (reference-based).
   */
  public const VERSION_METRICX_24_REF = 'METRICX_24_REF';
  /**
   * MetricX 2024 (2.6) for translation + source (QE).
   */
  public const VERSION_METRICX_24_SRC = 'METRICX_24_SRC';
  /**
   * MetricX 2024 (2.6) for translation + source + reference (source-reference-
   * combined).
   */
  public const VERSION_METRICX_24_SRC_REF = 'METRICX_24_SRC_REF';
  /**
   * Optional. Source language in BCP-47 format.
   *
   * @var string
   */
  public $sourceLanguage;
  /**
   * Optional. Target language in BCP-47 format. Covers both prediction and
   * reference.
   *
   * @var string
   */
  public $targetLanguage;
  /**
   * Required. Which version to use for evaluation.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. Source language in BCP-47 format.
   *
   * @param string $sourceLanguage
   */
  public function setSourceLanguage($sourceLanguage)
  {
    $this->sourceLanguage = $sourceLanguage;
  }
  /**
   * @return string
   */
  public function getSourceLanguage()
  {
    return $this->sourceLanguage;
  }
  /**
   * Optional. Target language in BCP-47 format. Covers both prediction and
   * reference.
   *
   * @param string $targetLanguage
   */
  public function setTargetLanguage($targetLanguage)
  {
    $this->targetLanguage = $targetLanguage;
  }
  /**
   * @return string
   */
  public function getTargetLanguage()
  {
    return $this->targetLanguage;
  }
  /**
   * Required. Which version to use for evaluation.
   *
   * Accepted values: METRICX_VERSION_UNSPECIFIED, METRICX_24_REF,
   * METRICX_24_SRC, METRICX_24_SRC_REF
   *
   * @param self::VERSION_* $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return self::VERSION_*
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MetricxSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MetricxSpec');
