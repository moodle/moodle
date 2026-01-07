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

namespace Google\Service\CloudComposer;

class ImageVersion extends \Google\Collection
{
  protected $collection_key = 'supportedPythonVersions';
  /**
   * Whether it is impossible to create an environment with the image version.
   *
   * @var bool
   */
  public $creationDisabled;
  /**
   * The string identifier of the ImageVersion, in the form:
   * "composer-x.y.z-airflow-a.b.c"
   *
   * @var string
   */
  public $imageVersionId;
  /**
   * Whether this is the default ImageVersion used by Composer during
   * environment creation if no input ImageVersion is specified.
   *
   * @var bool
   */
  public $isDefault;
  protected $releaseDateType = Date::class;
  protected $releaseDateDataType = '';
  /**
   * supported python versions
   *
   * @var string[]
   */
  public $supportedPythonVersions;
  /**
   * Whether it is impossible to upgrade an environment running with the image
   * version.
   *
   * @var bool
   */
  public $upgradeDisabled;

  /**
   * Whether it is impossible to create an environment with the image version.
   *
   * @param bool $creationDisabled
   */
  public function setCreationDisabled($creationDisabled)
  {
    $this->creationDisabled = $creationDisabled;
  }
  /**
   * @return bool
   */
  public function getCreationDisabled()
  {
    return $this->creationDisabled;
  }
  /**
   * The string identifier of the ImageVersion, in the form:
   * "composer-x.y.z-airflow-a.b.c"
   *
   * @param string $imageVersionId
   */
  public function setImageVersionId($imageVersionId)
  {
    $this->imageVersionId = $imageVersionId;
  }
  /**
   * @return string
   */
  public function getImageVersionId()
  {
    return $this->imageVersionId;
  }
  /**
   * Whether this is the default ImageVersion used by Composer during
   * environment creation if no input ImageVersion is specified.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * The date of the version release.
   *
   * @param Date $releaseDate
   */
  public function setReleaseDate(Date $releaseDate)
  {
    $this->releaseDate = $releaseDate;
  }
  /**
   * @return Date
   */
  public function getReleaseDate()
  {
    return $this->releaseDate;
  }
  /**
   * supported python versions
   *
   * @param string[] $supportedPythonVersions
   */
  public function setSupportedPythonVersions($supportedPythonVersions)
  {
    $this->supportedPythonVersions = $supportedPythonVersions;
  }
  /**
   * @return string[]
   */
  public function getSupportedPythonVersions()
  {
    return $this->supportedPythonVersions;
  }
  /**
   * Whether it is impossible to upgrade an environment running with the image
   * version.
   *
   * @param bool $upgradeDisabled
   */
  public function setUpgradeDisabled($upgradeDisabled)
  {
    $this->upgradeDisabled = $upgradeDisabled;
  }
  /**
   * @return bool
   */
  public function getUpgradeDisabled()
  {
    return $this->upgradeDisabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageVersion::class, 'Google_Service_CloudComposer_ImageVersion');
