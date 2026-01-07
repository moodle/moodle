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

namespace Google\Service\FirebaseRealtimeDatabase;

class DatabaseInstance extends \Google\Model
{
  /**
   * Unspecified state, likely the result of an error on the backend. This is
   * only used for distinguishing unset values.
   */
  public const STATE_LIFECYCLE_STATE_UNSPECIFIED = 'LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * The normal and active state.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The database is in a disabled state. It can be re-enabled later.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The database is in a deleted state.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * Unknown state, likely the result of an error on the backend. This is only
   * used for distinguishing unset values.
   */
  public const TYPE_DATABASE_INSTANCE_TYPE_UNSPECIFIED = 'DATABASE_INSTANCE_TYPE_UNSPECIFIED';
  /**
   * The default database that is provisioned when a project is created.
   */
  public const TYPE_DEFAULT_DATABASE = 'DEFAULT_DATABASE';
  /**
   * A database that the user created.
   */
  public const TYPE_USER_DATABASE = 'USER_DATABASE';
  /**
   * Output only. Output Only. The globally unique hostname of the database.
   *
   * @var string
   */
  public $databaseUrl;
  /**
   * The fully qualified resource name of the database instance, in the form:
   * `projects/{project-number}/locations/{location-id}/instances/{database-
   * id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource name of the project this instance belongs to. For
   * example: `projects/{project-number}`.
   *
   * @var string
   */
  public $project;
  /**
   * Output only. The database's lifecycle state. Read-only.
   *
   * @var string
   */
  public $state;
  /**
   * Immutable. The database instance type. On creation only USER_DATABASE is
   * allowed, which is also the default when omitted.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Output Only. The globally unique hostname of the database.
   *
   * @param string $databaseUrl
   */
  public function setDatabaseUrl($databaseUrl)
  {
    $this->databaseUrl = $databaseUrl;
  }
  /**
   * @return string
   */
  public function getDatabaseUrl()
  {
    return $this->databaseUrl;
  }
  /**
   * The fully qualified resource name of the database instance, in the form:
   * `projects/{project-number}/locations/{location-id}/instances/{database-
   * id}`.
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
   * Output only. The resource name of the project this instance belongs to. For
   * example: `projects/{project-number}`.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * Output only. The database's lifecycle state. Read-only.
   *
   * Accepted values: LIFECYCLE_STATE_UNSPECIFIED, ACTIVE, DISABLED, DELETED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Immutable. The database instance type. On creation only USER_DATABASE is
   * allowed, which is also the default when omitted.
   *
   * Accepted values: DATABASE_INSTANCE_TYPE_UNSPECIFIED, DEFAULT_DATABASE,
   * USER_DATABASE
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
class_alias(DatabaseInstance::class, 'Google_Service_FirebaseRealtimeDatabase_DatabaseInstance');
