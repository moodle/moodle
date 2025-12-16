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

class GoogleCloudAiplatformV1ModelSourceInfo extends \Google\Model
{
  /**
   * Should not be used.
   */
  public const SOURCE_TYPE_MODEL_SOURCE_TYPE_UNSPECIFIED = 'MODEL_SOURCE_TYPE_UNSPECIFIED';
  /**
   * The Model is uploaded by automl training pipeline.
   */
  public const SOURCE_TYPE_AUTOML = 'AUTOML';
  /**
   * The Model is uploaded by user or custom training pipeline.
   */
  public const SOURCE_TYPE_CUSTOM = 'CUSTOM';
  /**
   * The Model is registered and sync'ed from BigQuery ML.
   */
  public const SOURCE_TYPE_BQML = 'BQML';
  /**
   * The Model is saved or tuned from Model Garden.
   */
  public const SOURCE_TYPE_MODEL_GARDEN = 'MODEL_GARDEN';
  /**
   * The Model is saved or tuned from Genie.
   */
  public const SOURCE_TYPE_GENIE = 'GENIE';
  /**
   * The Model is uploaded by text embedding finetuning pipeline.
   */
  public const SOURCE_TYPE_CUSTOM_TEXT_EMBEDDING = 'CUSTOM_TEXT_EMBEDDING';
  /**
   * The Model is saved or tuned from Marketplace.
   */
  public const SOURCE_TYPE_MARKETPLACE = 'MARKETPLACE';
  /**
   * If this Model is copy of another Model. If true then source_type pertains
   * to the original.
   *
   * @var bool
   */
  public $copy;
  /**
   * Type of the model source.
   *
   * @var string
   */
  public $sourceType;

  /**
   * If this Model is copy of another Model. If true then source_type pertains
   * to the original.
   *
   * @param bool $copy
   */
  public function setCopy($copy)
  {
    $this->copy = $copy;
  }
  /**
   * @return bool
   */
  public function getCopy()
  {
    return $this->copy;
  }
  /**
   * Type of the model source.
   *
   * Accepted values: MODEL_SOURCE_TYPE_UNSPECIFIED, AUTOML, CUSTOM, BQML,
   * MODEL_GARDEN, GENIE, CUSTOM_TEXT_EMBEDDING, MARKETPLACE
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelSourceInfo::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelSourceInfo');
