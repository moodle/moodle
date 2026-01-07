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

namespace Google\Service\CloudNaturalLanguage;

class XPSResponseExplanationMetadataOutputMetadata extends \Google\Model
{
  /**
   * Name of the output tensor. Only needed in train response.
   *
   * @var string
   */
  public $outputTensorName;

  /**
   * Name of the output tensor. Only needed in train response.
   *
   * @param string $outputTensorName
   */
  public function setOutputTensorName($outputTensorName)
  {
    $this->outputTensorName = $outputTensorName;
  }
  /**
   * @return string
   */
  public function getOutputTensorName()
  {
    return $this->outputTensorName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSResponseExplanationMetadataOutputMetadata::class, 'Google_Service_CloudNaturalLanguage_XPSResponseExplanationMetadataOutputMetadata');
