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

namespace Google\Service\DatabaseMigrationService;

class SinglePackageChange extends \Google\Model
{
  /**
   * Optional. Sql code for package body
   *
   * @var string
   */
  public $packageBody;
  /**
   * Optional. Sql code for package description
   *
   * @var string
   */
  public $packageDescription;

  /**
   * Optional. Sql code for package body
   *
   * @param string $packageBody
   */
  public function setPackageBody($packageBody)
  {
    $this->packageBody = $packageBody;
  }
  /**
   * @return string
   */
  public function getPackageBody()
  {
    return $this->packageBody;
  }
  /**
   * Optional. Sql code for package description
   *
   * @param string $packageDescription
   */
  public function setPackageDescription($packageDescription)
  {
    $this->packageDescription = $packageDescription;
  }
  /**
   * @return string
   */
  public function getPackageDescription()
  {
    return $this->packageDescription;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SinglePackageChange::class, 'Google_Service_DatabaseMigrationService_SinglePackageChange');
