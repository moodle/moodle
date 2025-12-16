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

namespace Google\Service\CloudSearch;

class ImapSessionContext extends \Google\Model
{
  /**
   * @var string
   */
  public $app;
  /**
   * @var string
   */
  public $deviceType;
  /**
   * @var string
   */
  public $guidFingerprint;
  /**
   * @var string
   */
  public $os;
  protected $osVersionType = OsVersion::class;
  protected $osVersionDataType = '';
  protected $possiblyTrimmedModelType = PossiblyTrimmedModel::class;
  protected $possiblyTrimmedModelDataType = '';

  /**
   * @param string
   */
  public function setApp($app)
  {
    $this->app = $app;
  }
  /**
   * @return string
   */
  public function getApp()
  {
    return $this->app;
  }
  /**
   * @param string
   */
  public function setDeviceType($deviceType)
  {
    $this->deviceType = $deviceType;
  }
  /**
   * @return string
   */
  public function getDeviceType()
  {
    return $this->deviceType;
  }
  /**
   * @param string
   */
  public function setGuidFingerprint($guidFingerprint)
  {
    $this->guidFingerprint = $guidFingerprint;
  }
  /**
   * @return string
   */
  public function getGuidFingerprint()
  {
    return $this->guidFingerprint;
  }
  /**
   * @param string
   */
  public function setOs($os)
  {
    $this->os = $os;
  }
  /**
   * @return string
   */
  public function getOs()
  {
    return $this->os;
  }
  /**
   * @param OsVersion
   */
  public function setOsVersion(OsVersion $osVersion)
  {
    $this->osVersion = $osVersion;
  }
  /**
   * @return OsVersion
   */
  public function getOsVersion()
  {
    return $this->osVersion;
  }
  /**
   * @param PossiblyTrimmedModel
   */
  public function setPossiblyTrimmedModel(PossiblyTrimmedModel $possiblyTrimmedModel)
  {
    $this->possiblyTrimmedModel = $possiblyTrimmedModel;
  }
  /**
   * @return PossiblyTrimmedModel
   */
  public function getPossiblyTrimmedModel()
  {
    return $this->possiblyTrimmedModel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImapSessionContext::class, 'Google_Service_CloudSearch_ImapSessionContext');
