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

namespace Google\Service\ServiceNetworking;

class PythonSettings extends \Google\Model
{
  protected $commonType = CommonLanguageSettings::class;
  protected $commonDataType = '';
  protected $experimentalFeaturesType = ExperimentalFeatures::class;
  protected $experimentalFeaturesDataType = '';

  /**
   * Some settings.
   *
   * @param CommonLanguageSettings $common
   */
  public function setCommon(CommonLanguageSettings $common)
  {
    $this->common = $common;
  }
  /**
   * @return CommonLanguageSettings
   */
  public function getCommon()
  {
    return $this->common;
  }
  /**
   * Experimental features to be included during client library generation.
   *
   * @param ExperimentalFeatures $experimentalFeatures
   */
  public function setExperimentalFeatures(ExperimentalFeatures $experimentalFeatures)
  {
    $this->experimentalFeatures = $experimentalFeatures;
  }
  /**
   * @return ExperimentalFeatures
   */
  public function getExperimentalFeatures()
  {
    return $this->experimentalFeatures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PythonSettings::class, 'Google_Service_ServiceNetworking_PythonSettings');
