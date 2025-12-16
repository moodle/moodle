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

class PackageEntity extends \Google\Model
{
  /**
   * Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  /**
   * The SQL code which creates the package body. If the package specification
   * has cursors or subprograms, then the package body is mandatory.
   *
   * @var string
   */
  public $packageBody;
  /**
   * The SQL code which creates the package.
   *
   * @var string
   */
  public $packageSqlCode;

  /**
   * Custom engine specific features.
   *
   * @param array[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return array[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * The SQL code which creates the package body. If the package specification
   * has cursors or subprograms, then the package body is mandatory.
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
   * The SQL code which creates the package.
   *
   * @param string $packageSqlCode
   */
  public function setPackageSqlCode($packageSqlCode)
  {
    $this->packageSqlCode = $packageSqlCode;
  }
  /**
   * @return string
   */
  public function getPackageSqlCode()
  {
    return $this->packageSqlCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PackageEntity::class, 'Google_Service_DatabaseMigrationService_PackageEntity');
