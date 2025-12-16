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

class WindowsApplication extends \Google\Model
{
  /**
   * The name of the application or product.
   *
   * @var string
   */
  public $displayName;
  /**
   * The version of the product or application in string format.
   *
   * @var string
   */
  public $displayVersion;
  /**
   * The internet address for technical support.
   *
   * @var string
   */
  public $helpLink;
  protected $installDateType = Date::class;
  protected $installDateDataType = '';
  /**
   * The name of the manufacturer for the product or application.
   *
   * @var string
   */
  public $publisher;

  /**
   * The name of the application or product.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The version of the product or application in string format.
   *
   * @param string $displayVersion
   */
  public function setDisplayVersion($displayVersion)
  {
    $this->displayVersion = $displayVersion;
  }
  /**
   * @return string
   */
  public function getDisplayVersion()
  {
    return $this->displayVersion;
  }
  /**
   * The internet address for technical support.
   *
   * @param string $helpLink
   */
  public function setHelpLink($helpLink)
  {
    $this->helpLink = $helpLink;
  }
  /**
   * @return string
   */
  public function getHelpLink()
  {
    return $this->helpLink;
  }
  /**
   * The last time this product received service. The value of this property is
   * replaced each time a patch is applied or removed from the product or the
   * command-line option is used to repair the product.
   *
   * @param Date $installDate
   */
  public function setInstallDate(Date $installDate)
  {
    $this->installDate = $installDate;
  }
  /**
   * @return Date
   */
  public function getInstallDate()
  {
    return $this->installDate;
  }
  /**
   * The name of the manufacturer for the product or application.
   *
   * @param string $publisher
   */
  public function setPublisher($publisher)
  {
    $this->publisher = $publisher;
  }
  /**
   * @return string
   */
  public function getPublisher()
  {
    return $this->publisher;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WindowsApplication::class, 'Google_Service_CloudAsset_WindowsApplication');
