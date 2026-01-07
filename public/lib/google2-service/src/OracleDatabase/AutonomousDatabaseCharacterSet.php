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

class AutonomousDatabaseCharacterSet extends \Google\Model
{
  /**
   * Character set type is not specified.
   */
  public const CHARACTER_SET_TYPE_CHARACTER_SET_TYPE_UNSPECIFIED = 'CHARACTER_SET_TYPE_UNSPECIFIED';
  /**
   * Character set type is set to database.
   */
  public const CHARACTER_SET_TYPE_DATABASE = 'DATABASE';
  /**
   * Character set type is set to national.
   */
  public const CHARACTER_SET_TYPE_NATIONAL = 'NATIONAL';
  /**
   * Output only. The character set name for the Autonomous Database which is
   * the ID in the resource name.
   *
   * @var string
   */
  public $characterSet;
  /**
   * Output only. The character set type for the Autonomous Database.
   *
   * @var string
   */
  public $characterSetType;
  /**
   * Identifier. The name of the Autonomous Database Character Set resource in
   * the following format: projects/{project}/locations/{region}/autonomousDatab
   * aseCharacterSets/{autonomous_database_character_set}
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The character set name for the Autonomous Database which is
   * the ID in the resource name.
   *
   * @param string $characterSet
   */
  public function setCharacterSet($characterSet)
  {
    $this->characterSet = $characterSet;
  }
  /**
   * @return string
   */
  public function getCharacterSet()
  {
    return $this->characterSet;
  }
  /**
   * Output only. The character set type for the Autonomous Database.
   *
   * Accepted values: CHARACTER_SET_TYPE_UNSPECIFIED, DATABASE, NATIONAL
   *
   * @param self::CHARACTER_SET_TYPE_* $characterSetType
   */
  public function setCharacterSetType($characterSetType)
  {
    $this->characterSetType = $characterSetType;
  }
  /**
   * @return self::CHARACTER_SET_TYPE_*
   */
  public function getCharacterSetType()
  {
    return $this->characterSetType;
  }
  /**
   * Identifier. The name of the Autonomous Database Character Set resource in
   * the following format: projects/{project}/locations/{region}/autonomousDatab
   * aseCharacterSets/{autonomous_database_character_set}
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
class_alias(AutonomousDatabaseCharacterSet::class, 'Google_Service_OracleDatabase_AutonomousDatabaseCharacterSet');
