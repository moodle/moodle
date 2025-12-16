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

namespace Google\Service\CloudAsset;

class WindowsQuickFixEngineeringPackage extends \Google\Model
{
  /**
   * A short textual description of the QFE update.
   *
   * @var string
   */
  public $caption;
  /**
   * A textual description of the QFE update.
   *
   * @var string
   */
  public $description;
  /**
   * Unique identifier associated with a particular QFE update.
   *
   * @var string
   */
  public $hotFixId;
  /**
   * Date that the QFE update was installed. Mapped from installed_on field.
   *
   * @var string
   */
  public $installTime;

  /**
   * A short textual description of the QFE update.
   *
   * @param string $caption
   */
  public function setCaption($caption)
  {
    $this->caption = $caption;
  }
  /**
   * @return string
   */
  public function getCaption()
  {
    return $this->caption;
  }
  /**
   * A textual description of the QFE update.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Unique identifier associated with a particular QFE update.
   *
   * @param string $hotFixId
   */
  public function setHotFixId($hotFixId)
  {
    $this->hotFixId = $hotFixId;
  }
  /**
   * @return string
   */
  public function getHotFixId()
  {
    return $this->hotFixId;
  }
  /**
   * Date that the QFE update was installed. Mapped from installed_on field.
   *
   * @param string $installTime
   */
  public function setInstallTime($installTime)
  {
    $this->installTime = $installTime;
  }
  /**
   * @return string
   */
  public function getInstallTime()
  {
    return $this->installTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WindowsQuickFixEngineeringPackage::class, 'Google_Service_CloudAsset_WindowsQuickFixEngineeringPackage');
