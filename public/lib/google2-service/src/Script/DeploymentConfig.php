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

namespace Google\Service\Script;

class DeploymentConfig extends \Google\Model
{
  /**
   * The description for this deployment.
   *
   * @var string
   */
  public $description;
  /**
   * The manifest file name for this deployment.
   *
   * @var string
   */
  public $manifestFileName;
  /**
   * The script project's Drive ID.
   *
   * @var string
   */
  public $scriptId;
  /**
   * The version number on which this deployment is based.
   *
   * @var int
   */
  public $versionNumber;

  /**
   * The description for this deployment.
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
   * The manifest file name for this deployment.
   *
   * @param string $manifestFileName
   */
  public function setManifestFileName($manifestFileName)
  {
    $this->manifestFileName = $manifestFileName;
  }
  /**
   * @return string
   */
  public function getManifestFileName()
  {
    return $this->manifestFileName;
  }
  /**
   * The script project's Drive ID.
   *
   * @param string $scriptId
   */
  public function setScriptId($scriptId)
  {
    $this->scriptId = $scriptId;
  }
  /**
   * @return string
   */
  public function getScriptId()
  {
    return $this->scriptId;
  }
  /**
   * The version number on which this deployment is based.
   *
   * @param int $versionNumber
   */
  public function setVersionNumber($versionNumber)
  {
    $this->versionNumber = $versionNumber;
  }
  /**
   * @return int
   */
  public function getVersionNumber()
  {
    return $this->versionNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeploymentConfig::class, 'Google_Service_Script_DeploymentConfig');
