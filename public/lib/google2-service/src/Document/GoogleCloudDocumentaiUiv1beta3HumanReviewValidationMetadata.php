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

namespace Google\Service\Document;

class GoogleCloudDocumentaiUiv1beta3HumanReviewValidationMetadata extends \Google\Model
{
  /**
   * The confidence threshold if human review validation is enabled.
   *
   * @var float
   */
  public $confidenceThreshold;
  /**
   * Whether to enable human review validation.
   *
   * @var bool
   */
  public $enableValidation;

  /**
   * The confidence threshold if human review validation is enabled.
   *
   * @param float $confidenceThreshold
   */
  public function setConfidenceThreshold($confidenceThreshold)
  {
    $this->confidenceThreshold = $confidenceThreshold;
  }
  /**
   * @return float
   */
  public function getConfidenceThreshold()
  {
    return $this->confidenceThreshold;
  }
  /**
   * Whether to enable human review validation.
   *
   * @param bool $enableValidation
   */
  public function setEnableValidation($enableValidation)
  {
    $this->enableValidation = $enableValidation;
  }
  /**
   * @return bool
   */
  public function getEnableValidation()
  {
    return $this->enableValidation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3HumanReviewValidationMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3HumanReviewValidationMetadata');
