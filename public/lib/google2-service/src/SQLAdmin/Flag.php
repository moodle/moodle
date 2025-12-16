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

namespace Google\Service\SQLAdmin;

class Flag extends \Google\Collection
{
  /**
   * Assume database flags if unspecified
   */
  public const FLAG_SCOPE_SQL_FLAG_SCOPE_UNSPECIFIED = 'SQL_FLAG_SCOPE_UNSPECIFIED';
  /**
   * database flags
   */
  public const FLAG_SCOPE_SQL_FLAG_SCOPE_DATABASE = 'SQL_FLAG_SCOPE_DATABASE';
  /**
   * connection pool configuration flags
   */
  public const FLAG_SCOPE_SQL_FLAG_SCOPE_CONNECTION_POOL = 'SQL_FLAG_SCOPE_CONNECTION_POOL';
  /**
   * This is an unknown flag type.
   */
  public const TYPE_SQL_FLAG_TYPE_UNSPECIFIED = 'SQL_FLAG_TYPE_UNSPECIFIED';
  /**
   * Boolean type flag.
   */
  public const TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * String type flag.
   */
  public const TYPE_STRING = 'STRING';
  /**
   * Integer type flag.
   */
  public const TYPE_INTEGER = 'INTEGER';
  /**
   * Flag type used for a server startup option.
   */
  public const TYPE_NONE = 'NONE';
  /**
   * Type introduced specially for MySQL TimeZone offset. Accept a string value
   * with the format [-12:59, 13:00].
   */
  public const TYPE_MYSQL_TIMEZONE_OFFSET = 'MYSQL_TIMEZONE_OFFSET';
  /**
   * Float type flag.
   */
  public const TYPE_FLOAT = 'FLOAT';
  /**
   * Comma-separated list of the strings in a SqlFlagType enum.
   */
  public const TYPE_REPEATED_STRING = 'REPEATED_STRING';
  protected $collection_key = 'appliesTo';
  /**
   * Use this field if only certain integers are accepted. Can be combined with
   * min_value and max_value to add additional values.
   *
   * @var string[]
   */
  public $allowedIntValues;
  /**
   * For `STRING` flags, a list of strings that the value can be set to.
   *
   * @var string[]
   */
  public $allowedStringValues;
  /**
   * The database version this flag applies to. Can be MySQL instances:
   * `MYSQL_8_0`, `MYSQL_8_0_18`, `MYSQL_8_0_26`, `MYSQL_5_7`, or `MYSQL_5_6`.
   * PostgreSQL instances: `POSTGRES_9_6`, `POSTGRES_10`, `POSTGRES_11` or
   * `POSTGRES_12`. SQL Server instances: `SQLSERVER_2017_STANDARD`,
   * `SQLSERVER_2017_ENTERPRISE`, `SQLSERVER_2017_EXPRESS`,
   * `SQLSERVER_2017_WEB`, `SQLSERVER_2019_STANDARD`,
   * `SQLSERVER_2019_ENTERPRISE`, `SQLSERVER_2019_EXPRESS`, or
   * `SQLSERVER_2019_WEB`. See [the complete list](/sql/docs/mysql/admin-
   * api/rest/v1/SqlDatabaseVersion).
   *
   * @var string[]
   */
  public $appliesTo;
  /**
   * Scope of flag.
   *
   * @var string
   */
  public $flagScope;
  /**
   * Whether or not the flag is considered in beta.
   *
   * @var bool
   */
  public $inBeta;
  /**
   * This is always `sql#flag`.
   *
   * @var string
   */
  public $kind;
  /**
   * For `INTEGER` flags, the maximum allowed value.
   *
   * @var string
   */
  public $maxValue;
  /**
   * For `INTEGER` flags, the minimum allowed value.
   *
   * @var string
   */
  public $minValue;
  /**
   * This is the name of the flag. Flag names always use underscores, not
   * hyphens, for example: `max_allowed_packet`
   *
   * @var string
   */
  public $name;
  /**
   * Recommended int value in integer format for UI display.
   *
   * @var string
   */
  public $recommendedIntValue;
  /**
   * Recommended string value in string format for UI display.
   *
   * @var string
   */
  public $recommendedStringValue;
  /**
   * Indicates whether changing this flag will trigger a database restart. Only
   * applicable to Second Generation instances.
   *
   * @var bool
   */
  public $requiresRestart;
  /**
   * The type of the flag. Flags are typed to being `BOOLEAN`, `STRING`,
   * `INTEGER` or `NONE`. `NONE` is used for flags that do not take a value,
   * such as `skip_grant_tables`.
   *
   * @var string
   */
  public $type;

  /**
   * Use this field if only certain integers are accepted. Can be combined with
   * min_value and max_value to add additional values.
   *
   * @param string[] $allowedIntValues
   */
  public function setAllowedIntValues($allowedIntValues)
  {
    $this->allowedIntValues = $allowedIntValues;
  }
  /**
   * @return string[]
   */
  public function getAllowedIntValues()
  {
    return $this->allowedIntValues;
  }
  /**
   * For `STRING` flags, a list of strings that the value can be set to.
   *
   * @param string[] $allowedStringValues
   */
  public function setAllowedStringValues($allowedStringValues)
  {
    $this->allowedStringValues = $allowedStringValues;
  }
  /**
   * @return string[]
   */
  public function getAllowedStringValues()
  {
    return $this->allowedStringValues;
  }
  /**
   * The database version this flag applies to. Can be MySQL instances:
   * `MYSQL_8_0`, `MYSQL_8_0_18`, `MYSQL_8_0_26`, `MYSQL_5_7`, or `MYSQL_5_6`.
   * PostgreSQL instances: `POSTGRES_9_6`, `POSTGRES_10`, `POSTGRES_11` or
   * `POSTGRES_12`. SQL Server instances: `SQLSERVER_2017_STANDARD`,
   * `SQLSERVER_2017_ENTERPRISE`, `SQLSERVER_2017_EXPRESS`,
   * `SQLSERVER_2017_WEB`, `SQLSERVER_2019_STANDARD`,
   * `SQLSERVER_2019_ENTERPRISE`, `SQLSERVER_2019_EXPRESS`, or
   * `SQLSERVER_2019_WEB`. See [the complete list](/sql/docs/mysql/admin-
   * api/rest/v1/SqlDatabaseVersion).
   *
   * @param string[] $appliesTo
   */
  public function setAppliesTo($appliesTo)
  {
    $this->appliesTo = $appliesTo;
  }
  /**
   * @return string[]
   */
  public function getAppliesTo()
  {
    return $this->appliesTo;
  }
  /**
   * Scope of flag.
   *
   * Accepted values: SQL_FLAG_SCOPE_UNSPECIFIED, SQL_FLAG_SCOPE_DATABASE,
   * SQL_FLAG_SCOPE_CONNECTION_POOL
   *
   * @param self::FLAG_SCOPE_* $flagScope
   */
  public function setFlagScope($flagScope)
  {
    $this->flagScope = $flagScope;
  }
  /**
   * @return self::FLAG_SCOPE_*
   */
  public function getFlagScope()
  {
    return $this->flagScope;
  }
  /**
   * Whether or not the flag is considered in beta.
   *
   * @param bool $inBeta
   */
  public function setInBeta($inBeta)
  {
    $this->inBeta = $inBeta;
  }
  /**
   * @return bool
   */
  public function getInBeta()
  {
    return $this->inBeta;
  }
  /**
   * This is always `sql#flag`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * For `INTEGER` flags, the maximum allowed value.
   *
   * @param string $maxValue
   */
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return string
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * For `INTEGER` flags, the minimum allowed value.
   *
   * @param string $minValue
   */
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return string
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * This is the name of the flag. Flag names always use underscores, not
   * hyphens, for example: `max_allowed_packet`
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
   * Recommended int value in integer format for UI display.
   *
   * @param string $recommendedIntValue
   */
  public function setRecommendedIntValue($recommendedIntValue)
  {
    $this->recommendedIntValue = $recommendedIntValue;
  }
  /**
   * @return string
   */
  public function getRecommendedIntValue()
  {
    return $this->recommendedIntValue;
  }
  /**
   * Recommended string value in string format for UI display.
   *
   * @param string $recommendedStringValue
   */
  public function setRecommendedStringValue($recommendedStringValue)
  {
    $this->recommendedStringValue = $recommendedStringValue;
  }
  /**
   * @return string
   */
  public function getRecommendedStringValue()
  {
    return $this->recommendedStringValue;
  }
  /**
   * Indicates whether changing this flag will trigger a database restart. Only
   * applicable to Second Generation instances.
   *
   * @param bool $requiresRestart
   */
  public function setRequiresRestart($requiresRestart)
  {
    $this->requiresRestart = $requiresRestart;
  }
  /**
   * @return bool
   */
  public function getRequiresRestart()
  {
    return $this->requiresRestart;
  }
  /**
   * The type of the flag. Flags are typed to being `BOOLEAN`, `STRING`,
   * `INTEGER` or `NONE`. `NONE` is used for flags that do not take a value,
   * such as `skip_grant_tables`.
   *
   * Accepted values: SQL_FLAG_TYPE_UNSPECIFIED, BOOLEAN, STRING, INTEGER, NONE,
   * MYSQL_TIMEZONE_OFFSET, FLOAT, REPEATED_STRING
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
class_alias(Flag::class, 'Google_Service_SQLAdmin_Flag');
