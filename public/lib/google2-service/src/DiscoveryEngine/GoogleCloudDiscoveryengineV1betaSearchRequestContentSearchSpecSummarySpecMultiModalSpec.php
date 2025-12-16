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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecMultiModalSpec extends \Google\Model
{
  /**
   * Unspecified image source (multimodal feature is disabled by default).
   */
  public const IMAGE_SOURCE_IMAGE_SOURCE_UNSPECIFIED = 'IMAGE_SOURCE_UNSPECIFIED';
  /**
   * Behavior when service determines the pick from all available sources.
   */
  public const IMAGE_SOURCE_ALL_AVAILABLE_SOURCES = 'ALL_AVAILABLE_SOURCES';
  /**
   * Includes image from corpus in the answer.
   */
  public const IMAGE_SOURCE_CORPUS_IMAGE_ONLY = 'CORPUS_IMAGE_ONLY';
  /**
   * Triggers figure generation in the answer.
   */
  public const IMAGE_SOURCE_FIGURE_GENERATION_ONLY = 'FIGURE_GENERATION_ONLY';
  /**
   * Optional. Source of image returned in the answer.
   *
   * @var string
   */
  public $imageSource;

  /**
   * Optional. Source of image returned in the answer.
   *
   * Accepted values: IMAGE_SOURCE_UNSPECIFIED, ALL_AVAILABLE_SOURCES,
   * CORPUS_IMAGE_ONLY, FIGURE_GENERATION_ONLY
   *
   * @param self::IMAGE_SOURCE_* $imageSource
   */
  public function setImageSource($imageSource)
  {
    $this->imageSource = $imageSource;
  }
  /**
   * @return self::IMAGE_SOURCE_*
   */
  public function getImageSource()
  {
    return $this->imageSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecMultiModalSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaSearchRequestContentSearchSpecSummarySpecMultiModalSpec');
