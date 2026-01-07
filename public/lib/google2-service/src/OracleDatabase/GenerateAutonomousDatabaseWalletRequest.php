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

class GenerateAutonomousDatabaseWalletRequest extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const TYPE_GENERATE_TYPE_UNSPECIFIED = 'GENERATE_TYPE_UNSPECIFIED';
  /**
   * Used to generate wallet for all databases in the region.
   */
  public const TYPE_ALL = 'ALL';
  /**
   * Used to generate wallet for a single database.
   */
  public const TYPE_SINGLE = 'SINGLE';
  /**
   * Optional. True when requesting regional connection strings in PDB connect
   * info, applicable to cross-region Data Guard only.
   *
   * @var bool
   */
  public $isRegional;
  /**
   * Required. The password used to encrypt the keys inside the wallet. The
   * password must be a minimum of 8 characters.
   *
   * @var string
   */
  public $password;
  /**
   * Optional. The type of wallet generation for the Autonomous Database. The
   * default value is SINGLE.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. True when requesting regional connection strings in PDB connect
   * info, applicable to cross-region Data Guard only.
   *
   * @param bool $isRegional
   */
  public function setIsRegional($isRegional)
  {
    $this->isRegional = $isRegional;
  }
  /**
   * @return bool
   */
  public function getIsRegional()
  {
    return $this->isRegional;
  }
  /**
   * Required. The password used to encrypt the keys inside the wallet. The
   * password must be a minimum of 8 characters.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Optional. The type of wallet generation for the Autonomous Database. The
   * default value is SINGLE.
   *
   * Accepted values: GENERATE_TYPE_UNSPECIFIED, ALL, SINGLE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateAutonomousDatabaseWalletRequest::class, 'Google_Service_OracleDatabase_GenerateAutonomousDatabaseWalletRequest');
