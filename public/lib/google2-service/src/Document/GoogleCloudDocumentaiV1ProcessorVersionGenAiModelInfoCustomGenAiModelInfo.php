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

class GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoCustomGenAiModelInfo extends \Google\Model
{
  /**
   * The model type is unspecified.
   */
  public const CUSTOM_MODEL_TYPE_CUSTOM_MODEL_TYPE_UNSPECIFIED = 'CUSTOM_MODEL_TYPE_UNSPECIFIED';
  /**
   * The model is a versioned foundation model.
   */
  public const CUSTOM_MODEL_TYPE_VERSIONED_FOUNDATION = 'VERSIONED_FOUNDATION';
  /**
   * The model is a finetuned foundation model.
   */
  public const CUSTOM_MODEL_TYPE_FINE_TUNED = 'FINE_TUNED';
  /**
   * The base processor version ID for the custom model.
   *
   * @var string
   */
  public $baseProcessorVersionId;
  /**
   * The type of custom model created by the user.
   *
   * @var string
   */
  public $customModelType;

  /**
   * The base processor version ID for the custom model.
   *
   * @param string $baseProcessorVersionId
   */
  public function setBaseProcessorVersionId($baseProcessorVersionId)
  {
    $this->baseProcessorVersionId = $baseProcessorVersionId;
  }
  /**
   * @return string
   */
  public function getBaseProcessorVersionId()
  {
    return $this->baseProcessorVersionId;
  }
  /**
   * The type of custom model created by the user.
   *
   * Accepted values: CUSTOM_MODEL_TYPE_UNSPECIFIED, VERSIONED_FOUNDATION,
   * FINE_TUNED
   *
   * @param self::CUSTOM_MODEL_TYPE_* $customModelType
   */
  public function setCustomModelType($customModelType)
  {
    $this->customModelType = $customModelType;
  }
  /**
   * @return self::CUSTOM_MODEL_TYPE_*
   */
  public function getCustomModelType()
  {
    return $this->customModelType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoCustomGenAiModelInfo::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfoCustomGenAiModelInfo');
