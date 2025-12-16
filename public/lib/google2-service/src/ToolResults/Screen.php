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

namespace Google\Service\ToolResults;

class Screen extends \Google\Model
{
  /**
   * File reference of the png file. Required.
   *
   * @var string
   */
  public $fileReference;
  /**
   * Locale of the device that the screenshot was taken on. Required.
   *
   * @var string
   */
  public $locale;
  /**
   * Model of the device that the screenshot was taken on. Required.
   *
   * @var string
   */
  public $model;
  /**
   * OS version of the device that the screenshot was taken on. Required.
   *
   * @var string
   */
  public $version;

  /**
   * File reference of the png file. Required.
   *
   * @param string $fileReference
   */
  public function setFileReference($fileReference)
  {
    $this->fileReference = $fileReference;
  }
  /**
   * @return string
   */
  public function getFileReference()
  {
    return $this->fileReference;
  }
  /**
   * Locale of the device that the screenshot was taken on. Required.
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * Model of the device that the screenshot was taken on. Required.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * OS version of the device that the screenshot was taken on. Required.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Screen::class, 'Google_Service_ToolResults_Screen');
