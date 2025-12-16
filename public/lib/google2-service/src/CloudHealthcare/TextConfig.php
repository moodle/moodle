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

namespace Google\Service\CloudHealthcare;

class TextConfig extends \Google\Collection
{
  protected $collection_key = 'transformations';
  protected $additionalTransformationsType = InfoTypeTransformation::class;
  protected $additionalTransformationsDataType = 'array';
  /**
   * Optional. InfoTypes to skip transforming, overriding
   * `additional_transformations`.
   *
   * @var string[]
   */
  public $excludeInfoTypes;
  protected $transformationsType = InfoTypeTransformation::class;
  protected $transformationsDataType = 'array';

  /**
   * Optional. Transformations to apply to the detected data, overridden by
   * `exclude_info_types`.
   *
   * @param InfoTypeTransformation[] $additionalTransformations
   */
  public function setAdditionalTransformations($additionalTransformations)
  {
    $this->additionalTransformations = $additionalTransformations;
  }
  /**
   * @return InfoTypeTransformation[]
   */
  public function getAdditionalTransformations()
  {
    return $this->additionalTransformations;
  }
  /**
   * Optional. InfoTypes to skip transforming, overriding
   * `additional_transformations`.
   *
   * @param string[] $excludeInfoTypes
   */
  public function setExcludeInfoTypes($excludeInfoTypes)
  {
    $this->excludeInfoTypes = $excludeInfoTypes;
  }
  /**
   * @return string[]
   */
  public function getExcludeInfoTypes()
  {
    return $this->excludeInfoTypes;
  }
  /**
   * Optional. The transformations to apply to the detected data. Deprecated.
   * Use `additional_transformations` instead.
   *
   * @param InfoTypeTransformation[] $transformations
   */
  public function setTransformations($transformations)
  {
    $this->transformations = $transformations;
  }
  /**
   * @return InfoTypeTransformation[]
   */
  public function getTransformations()
  {
    return $this->transformations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextConfig::class, 'Google_Service_CloudHealthcare_TextConfig');
