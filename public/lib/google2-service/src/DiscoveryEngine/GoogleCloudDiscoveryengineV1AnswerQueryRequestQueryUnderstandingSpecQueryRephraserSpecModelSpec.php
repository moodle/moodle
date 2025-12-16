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

class GoogleCloudDiscoveryengineV1AnswerQueryRequestQueryUnderstandingSpecQueryRephraserSpecModelSpec extends \Google\Model
{
  /**
   * Unspecified model type.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * Small query rephraser model. Gemini 1.0 XS model.
   */
  public const MODEL_TYPE_SMALL = 'SMALL';
  /**
   * Large query rephraser model. Gemini 1.0 Pro model.
   */
  public const MODEL_TYPE_LARGE = 'LARGE';
  /**
   * Optional. Enabled query rephraser model type. If not set, it will use LARGE
   * by default.
   *
   * @var string
   */
  public $modelType;

  /**
   * Optional. Enabled query rephraser model type. If not set, it will use LARGE
   * by default.
   *
   * Accepted values: MODEL_TYPE_UNSPECIFIED, SMALL, LARGE
   *
   * @param self::MODEL_TYPE_* $modelType
   */
  public function setModelType($modelType)
  {
    $this->modelType = $modelType;
  }
  /**
   * @return self::MODEL_TYPE_*
   */
  public function getModelType()
  {
    return $this->modelType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestQueryUnderstandingSpecQueryRephraserSpecModelSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestQueryUnderstandingSpecQueryRephraserSpecModelSpec');
