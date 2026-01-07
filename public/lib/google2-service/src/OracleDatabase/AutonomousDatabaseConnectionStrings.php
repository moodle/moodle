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

class AutonomousDatabaseConnectionStrings extends \Google\Collection
{
  protected $collection_key = 'profiles';
  protected $allConnectionStringsType = AllConnectionStrings::class;
  protected $allConnectionStringsDataType = '';
  /**
   * Output only. The database service provides the least level of resources to
   * each SQL statement, but supports the most number of concurrent SQL
   * statements.
   *
   * @var string
   */
  public $dedicated;
  /**
   * Output only. The database service provides the highest level of resources
   * to each SQL statement.
   *
   * @var string
   */
  public $high;
  /**
   * Output only. The database service provides the least level of resources to
   * each SQL statement.
   *
   * @var string
   */
  public $low;
  /**
   * Output only. The database service provides a lower level of resources to
   * each SQL statement.
   *
   * @var string
   */
  public $medium;
  protected $profilesType = DatabaseConnectionStringProfile::class;
  protected $profilesDataType = 'array';

  /**
   * Output only. Returns all connection strings that can be used to connect to
   * the Autonomous Database.
   *
   * @param AllConnectionStrings $allConnectionStrings
   */
  public function setAllConnectionStrings(AllConnectionStrings $allConnectionStrings)
  {
    $this->allConnectionStrings = $allConnectionStrings;
  }
  /**
   * @return AllConnectionStrings
   */
  public function getAllConnectionStrings()
  {
    return $this->allConnectionStrings;
  }
  /**
   * Output only. The database service provides the least level of resources to
   * each SQL statement, but supports the most number of concurrent SQL
   * statements.
   *
   * @param string $dedicated
   */
  public function setDedicated($dedicated)
  {
    $this->dedicated = $dedicated;
  }
  /**
   * @return string
   */
  public function getDedicated()
  {
    return $this->dedicated;
  }
  /**
   * Output only. The database service provides the highest level of resources
   * to each SQL statement.
   *
   * @param string $high
   */
  public function setHigh($high)
  {
    $this->high = $high;
  }
  /**
   * @return string
   */
  public function getHigh()
  {
    return $this->high;
  }
  /**
   * Output only. The database service provides the least level of resources to
   * each SQL statement.
   *
   * @param string $low
   */
  public function setLow($low)
  {
    $this->low = $low;
  }
  /**
   * @return string
   */
  public function getLow()
  {
    return $this->low;
  }
  /**
   * Output only. The database service provides a lower level of resources to
   * each SQL statement.
   *
   * @param string $medium
   */
  public function setMedium($medium)
  {
    $this->medium = $medium;
  }
  /**
   * @return string
   */
  public function getMedium()
  {
    return $this->medium;
  }
  /**
   * Output only. A list of connection string profiles to allow clients to
   * group, filter, and select values based on the structured metadata.
   *
   * @param DatabaseConnectionStringProfile[] $profiles
   */
  public function setProfiles($profiles)
  {
    $this->profiles = $profiles;
  }
  /**
   * @return DatabaseConnectionStringProfile[]
   */
  public function getProfiles()
  {
    return $this->profiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutonomousDatabaseConnectionStrings::class, 'Google_Service_OracleDatabase_AutonomousDatabaseConnectionStrings');
