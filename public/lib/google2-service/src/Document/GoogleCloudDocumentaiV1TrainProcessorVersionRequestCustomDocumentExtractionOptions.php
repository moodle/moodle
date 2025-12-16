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

class GoogleCloudDocumentaiV1TrainProcessorVersionRequestCustomDocumentExtractionOptions extends \Google\Model
{
  public const TRAINING_METHOD_TRAINING_METHOD_UNSPECIFIED = 'TRAINING_METHOD_UNSPECIFIED';
  public const TRAINING_METHOD_MODEL_BASED = 'MODEL_BASED';
  public const TRAINING_METHOD_TEMPLATE_BASED = 'TEMPLATE_BASED';
  /**
   * Optional. Training method to use for CDE training.
   *
   * @var string
   */
  public $trainingMethod;

  /**
   * Optional. Training method to use for CDE training.
   *
   * Accepted values: TRAINING_METHOD_UNSPECIFIED, MODEL_BASED, TEMPLATE_BASED
   *
   * @param self::TRAINING_METHOD_* $trainingMethod
   */
  public function setTrainingMethod($trainingMethod)
  {
    $this->trainingMethod = $trainingMethod;
  }
  /**
   * @return self::TRAINING_METHOD_*
   */
  public function getTrainingMethod()
  {
    return $this->trainingMethod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1TrainProcessorVersionRequestCustomDocumentExtractionOptions::class, 'Google_Service_Document_GoogleCloudDocumentaiV1TrainProcessorVersionRequestCustomDocumentExtractionOptions');
