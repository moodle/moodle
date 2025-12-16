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

namespace Google\Service\Dfareporting;

class Browser extends \Google\Model
{
  /**
   * ID referring to this grouping of browser and version numbers. This is the
   * ID used for targeting.
   *
   * @var string
   */
  public $browserVersionId;
  /**
   * DART ID of this browser. This is the ID used when generating reports.
   *
   * @var string
   */
  public $dartId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#browser".
   *
   * @var string
   */
  public $kind;
  /**
   * Major version number (leftmost number) of this browser. For example, for
   * Chrome 5.0.376.86 beta, this field should be set to 5. An asterisk (*) may
   * be used to target any version number, and a question mark (?) may be used
   * to target cases where the version number cannot be identified. For example,
   * Chrome *.* targets any version of Chrome: 1.2, 2.5, 3.5, and so on. Chrome
   * 3.* targets Chrome 3.1, 3.5, but not 4.0. Firefox ?.? targets cases where
   * the ad server knows the browser is Firefox but can't tell which version it
   * is.
   *
   * @var string
   */
  public $majorVersion;
  /**
   * Minor version number (number after first dot on left) of this browser. For
   * example, for Chrome 5.0.375.86 beta, this field should be set to 0. An
   * asterisk (*) may be used to target any version number, and a question mark
   * (?) may be used to target cases where the version number cannot be
   * identified. For example, Chrome *.* targets any version of Chrome: 1.2,
   * 2.5, 3.5, and so on. Chrome 3.* targets Chrome 3.1, 3.5, but not 4.0.
   * Firefox ?.? targets cases where the ad server knows the browser is Firefox
   * but can't tell which version it is.
   *
   * @var string
   */
  public $minorVersion;
  /**
   * Name of this browser.
   *
   * @var string
   */
  public $name;

  /**
   * ID referring to this grouping of browser and version numbers. This is the
   * ID used for targeting.
   *
   * @param string $browserVersionId
   */
  public function setBrowserVersionId($browserVersionId)
  {
    $this->browserVersionId = $browserVersionId;
  }
  /**
   * @return string
   */
  public function getBrowserVersionId()
  {
    return $this->browserVersionId;
  }
  /**
   * DART ID of this browser. This is the ID used when generating reports.
   *
   * @param string $dartId
   */
  public function setDartId($dartId)
  {
    $this->dartId = $dartId;
  }
  /**
   * @return string
   */
  public function getDartId()
  {
    return $this->dartId;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#browser".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Major version number (leftmost number) of this browser. For example, for
   * Chrome 5.0.376.86 beta, this field should be set to 5. An asterisk (*) may
   * be used to target any version number, and a question mark (?) may be used
   * to target cases where the version number cannot be identified. For example,
   * Chrome *.* targets any version of Chrome: 1.2, 2.5, 3.5, and so on. Chrome
   * 3.* targets Chrome 3.1, 3.5, but not 4.0. Firefox ?.? targets cases where
   * the ad server knows the browser is Firefox but can't tell which version it
   * is.
   *
   * @param string $majorVersion
   */
  public function setMajorVersion($majorVersion)
  {
    $this->majorVersion = $majorVersion;
  }
  /**
   * @return string
   */
  public function getMajorVersion()
  {
    return $this->majorVersion;
  }
  /**
   * Minor version number (number after first dot on left) of this browser. For
   * example, for Chrome 5.0.375.86 beta, this field should be set to 0. An
   * asterisk (*) may be used to target any version number, and a question mark
   * (?) may be used to target cases where the version number cannot be
   * identified. For example, Chrome *.* targets any version of Chrome: 1.2,
   * 2.5, 3.5, and so on. Chrome 3.* targets Chrome 3.1, 3.5, but not 4.0.
   * Firefox ?.? targets cases where the ad server knows the browser is Firefox
   * but can't tell which version it is.
   *
   * @param string $minorVersion
   */
  public function setMinorVersion($minorVersion)
  {
    $this->minorVersion = $minorVersion;
  }
  /**
   * @return string
   */
  public function getMinorVersion()
  {
    return $this->minorVersion;
  }
  /**
   * Name of this browser.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Browser::class, 'Google_Service_Dfareporting_Browser');
