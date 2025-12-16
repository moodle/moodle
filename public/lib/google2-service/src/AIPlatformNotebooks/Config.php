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

namespace Google\Service\AIPlatformNotebooks;

class Config extends \Google\Collection
{
  protected $collection_key = 'availableImages';
  protected $availableImagesType = ImageRelease::class;
  protected $availableImagesDataType = 'array';
  protected $defaultValuesType = DefaultValues::class;
  protected $defaultValuesDataType = '';
  /**
   * Output only. Flag to disable the creation of legacy Workbench notebooks
   * (User-managed notebooks and Google-managed notebooks).
   *
   * @var bool
   */
  public $disableWorkbenchLegacyCreation;
  protected $supportedValuesType = SupportedValues::class;
  protected $supportedValuesDataType = '';

  /**
   * Output only. The list of available images to create a WbI.
   *
   * @param ImageRelease[] $availableImages
   */
  public function setAvailableImages($availableImages)
  {
    $this->availableImages = $availableImages;
  }
  /**
   * @return ImageRelease[]
   */
  public function getAvailableImages()
  {
    return $this->availableImages;
  }
  /**
   * Output only. The default values for configuration.
   *
   * @param DefaultValues $defaultValues
   */
  public function setDefaultValues(DefaultValues $defaultValues)
  {
    $this->defaultValues = $defaultValues;
  }
  /**
   * @return DefaultValues
   */
  public function getDefaultValues()
  {
    return $this->defaultValues;
  }
  /**
   * Output only. Flag to disable the creation of legacy Workbench notebooks
   * (User-managed notebooks and Google-managed notebooks).
   *
   * @param bool $disableWorkbenchLegacyCreation
   */
  public function setDisableWorkbenchLegacyCreation($disableWorkbenchLegacyCreation)
  {
    $this->disableWorkbenchLegacyCreation = $disableWorkbenchLegacyCreation;
  }
  /**
   * @return bool
   */
  public function getDisableWorkbenchLegacyCreation()
  {
    return $this->disableWorkbenchLegacyCreation;
  }
  /**
   * Output only. The supported values for configuration.
   *
   * @param SupportedValues $supportedValues
   */
  public function setSupportedValues(SupportedValues $supportedValues)
  {
    $this->supportedValues = $supportedValues;
  }
  /**
   * @return SupportedValues
   */
  public function getSupportedValues()
  {
    return $this->supportedValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Config::class, 'Google_Service_AIPlatformNotebooks_Config');
