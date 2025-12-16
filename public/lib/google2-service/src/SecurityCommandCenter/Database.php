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

namespace Google\Service\SecurityCommandCenter;

class Database extends \Google\Collection
{
  protected $collection_key = 'grantees';
  /**
   * The human-readable name of the database that the user connected to.
   *
   * @var string
   */
  public $displayName;
  /**
   * The target usernames, roles, or groups of an SQL privilege grant, which is
   * not an IAM policy change.
   *
   * @var string[]
   */
  public $grantees;
  /**
   * Some database resources may not have the [full resource
   * name](https://google.aip.dev/122#full-resource-names) populated because
   * these resource types are not yet supported by Cloud Asset Inventory (e.g.
   * Cloud SQL databases). In these cases only the display name will be
   * provided. The [full resource name](https://google.aip.dev/122#full-
   * resource-names) of the database that the user connected to, if it is
   * supported by Cloud Asset Inventory.
   *
   * @var string
   */
  public $name;
  /**
   * The SQL statement that is associated with the database access.
   *
   * @var string
   */
  public $query;
  /**
   * The username used to connect to the database. The username might not be an
   * IAM principal and does not have a set format.
   *
   * @var string
   */
  public $userName;
  /**
   * The version of the database, for example, POSTGRES_14. See [the complete
   * list](https://cloud.google.com/sql/docs/mysql/admin-
   * api/rest/v1/SqlDatabaseVersion).
   *
   * @var string
   */
  public $version;

  /**
   * The human-readable name of the database that the user connected to.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The target usernames, roles, or groups of an SQL privilege grant, which is
   * not an IAM policy change.
   *
   * @param string[] $grantees
   */
  public function setGrantees($grantees)
  {
    $this->grantees = $grantees;
  }
  /**
   * @return string[]
   */
  public function getGrantees()
  {
    return $this->grantees;
  }
  /**
   * Some database resources may not have the [full resource
   * name](https://google.aip.dev/122#full-resource-names) populated because
   * these resource types are not yet supported by Cloud Asset Inventory (e.g.
   * Cloud SQL databases). In these cases only the display name will be
   * provided. The [full resource name](https://google.aip.dev/122#full-
   * resource-names) of the database that the user connected to, if it is
   * supported by Cloud Asset Inventory.
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
  /**
   * The SQL statement that is associated with the database access.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The username used to connect to the database. The username might not be an
   * IAM principal and does not have a set format.
   *
   * @param string $userName
   */
  public function setUserName($userName)
  {
    $this->userName = $userName;
  }
  /**
   * @return string
   */
  public function getUserName()
  {
    return $this->userName;
  }
  /**
   * The version of the database, for example, POSTGRES_14. See [the complete
   * list](https://cloud.google.com/sql/docs/mysql/admin-
   * api/rest/v1/SqlDatabaseVersion).
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Database::class, 'Google_Service_SecurityCommandCenter_Database');
