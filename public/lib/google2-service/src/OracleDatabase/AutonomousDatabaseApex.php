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

namespace Google\Service\OracleDatabase;

class AutonomousDatabaseApex extends \Google\Model
{
  /**
   * Output only. The Oracle APEX Application Development version.
   *
   * @var string
   */
  public $apexVersion;
  /**
   * Output only. The Oracle REST Data Services (ORDS) version.
   *
   * @var string
   */
  public $ordsVersion;

  /**
   * Output only. The Oracle APEX Application Development version.
   *
   * @param string $apexVersion
   */
  public function setApexVersion($apexVersion)
  {
    $this->apexVersion = $apexVersion;
  }
  /**
   * @return string
   */
  public function getApexVersion()
  {
    return $this->apexVersion;
  }
  /**
   * Output only. The Oracle REST Data Services (ORDS) version.
   *
   * @param string $ordsVersion
   */
  public function setOrdsVersion($ordsVersion)
  {
    $this->ordsVersion = $ordsVersion;
  }
  /**
   * @return string
   */
  public function getOrdsVersion()
  {
    return $this->ordsVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutonomousDatabaseApex::class, 'Google_Service_OracleDatabase_AutonomousDatabaseApex');
