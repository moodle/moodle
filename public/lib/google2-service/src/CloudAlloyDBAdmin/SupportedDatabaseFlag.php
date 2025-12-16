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

namespace Google\Service\CloudAlloyDBAdmin;

class SupportedDatabaseFlag extends \Google\Collection
{
  /**
   * The scope of the flag is not specified. Default is DATABASE.
   */
  public const SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  /**
   * The flag is a database flag.
   */
  public const SCOPE_DATABASE = 'DATABASE';
  /**
   * The flag is a connection pool flag.
   */
  public const SCOPE_CONNECTION_POOL = 'CONNECTION_POOL';
  /**
   * This is an unknown flag type.
   */
  public const VALUE_TYPE_VALUE_TYPE_UNSPECIFIED = 'VALUE_TYPE_UNSPECIFIED';
  /**
   * String type flag.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * Integer type flag.
   */
  public const VALUE_TYPE_INTEGER = 'INTEGER';
  /**
   * Float type flag.
   */
  public const VALUE_TYPE_FLOAT = 'FLOAT';
  /**
   * Denotes that the flag does not accept any values.
   */
  public const VALUE_TYPE_NONE = 'NONE';
  protected $collection_key = 'supportedDbVersions';
  /**
   * Whether the database flag accepts multiple values. If true, a comma-
   * separated list of stringified values may be specified.
   *
   * @var bool
   */
  public $acceptsMultipleValues;
  /**
   * The name of the database flag, e.g. "max_allowed_packets". The is a
   * possibly key for the Instance.database_flags map field.
   *
   * @var string
   */
  public $flagName;
  protected $integerRestrictionsType = IntegerRestrictions::class;
  protected $integerRestrictionsDataType = '';
  /**
   * The name of the flag resource, following Google Cloud conventions, e.g.: *
   * projects/{project}/locations/{location}/flags/{flag} This field currently
   * has no semantic meaning.
   *
   * @var string
   */
  public $name;
  /**
   * The recommended value for an INTEGER flag.
   *
   * @var string
   */
  public $recommendedIntegerValue;
  /**
   * The recommended value for a STRING flag.
   *
   * @var string
   */
  public $recommendedStringValue;
  /**
   * Whether setting or updating this flag on an Instance requires a database
   * restart. If a flag that requires database restart is set, the backend will
   * automatically restart the database (making sure to satisfy any availability
   * SLO's).
   *
   * @var bool
   */
  public $requiresDbRestart;
  /**
   * The scope of the flag.
   *
   * @var string
   */
  public $scope;
  protected $stringRestrictionsType = StringRestrictions::class;
  protected $stringRestrictionsDataType = '';
  /**
   * Major database engine versions for which this flag is supported.
   *
   * @var string[]
   */
  public $supportedDbVersions;
  /**
   * @var string
   */
  public $valueType;

  /**
   * Whether the database flag accepts multiple values. If true, a comma-
   * separated list of stringified values may be specified.
   *
   * @param bool $acceptsMultipleValues
   */
  public function setAcceptsMultipleValues($acceptsMultipleValues)
  {
    $this->acceptsMultipleValues = $acceptsMultipleValues;
  }
  /**
   * @return bool
   */
  public function getAcceptsMultipleValues()
  {
    return $this->acceptsMultipleValues;
  }
  /**
   * The name of the database flag, e.g. "max_allowed_packets". The is a
   * possibly key for the Instance.database_flags map field.
   *
   * @param string $flagName
   */
  public function setFlagName($flagName)
  {
    $this->flagName = $flagName;
  }
  /**
   * @return string
   */
  public function getFlagName()
  {
    return $this->flagName;
  }
  /**
   * Restriction on INTEGER type value.
   *
   * @param IntegerRestrictions $integerRestrictions
   */
  public function setIntegerRestrictions(IntegerRestrictions $integerRestrictions)
  {
    $this->integerRestrictions = $integerRestrictions;
  }
  /**
   * @return IntegerRestrictions
   */
  public function getIntegerRestrictions()
  {
    return $this->integerRestrictions;
  }
  /**
   * The name of the flag resource, following Google Cloud conventions, e.g.: *
   * projects/{project}/locations/{location}/flags/{flag} This field currently
   * has no semantic meaning.
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
   * The recommended value for an INTEGER flag.
   *
   * @param string $recommendedIntegerValue
   */
  public function setRecommendedIntegerValue($recommendedIntegerValue)
  {
    $this->recommendedIntegerValue = $recommendedIntegerValue;
  }
  /**
   * @return string
   */
  public function getRecommendedIntegerValue()
  {
    return $this->recommendedIntegerValue;
  }
  /**
   * The recommended value for a STRING flag.
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
   * Whether setting or updating this flag on an Instance requires a database
   * restart. If a flag that requires database restart is set, the backend will
   * automatically restart the database (making sure to satisfy any availability
   * SLO's).
   *
   * @param bool $requiresDbRestart
   */
  public function setRequiresDbRestart($requiresDbRestart)
  {
    $this->requiresDbRestart = $requiresDbRestart;
  }
  /**
   * @return bool
   */
  public function getRequiresDbRestart()
  {
    return $this->requiresDbRestart;
  }
  /**
   * The scope of the flag.
   *
   * Accepted values: SCOPE_UNSPECIFIED, DATABASE, CONNECTION_POOL
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Restriction on STRING type value.
   *
   * @param StringRestrictions $stringRestrictions
   */
  public function setStringRestrictions(StringRestrictions $stringRestrictions)
  {
    $this->stringRestrictions = $stringRestrictions;
  }
  /**
   * @return StringRestrictions
   */
  public function getStringRestrictions()
  {
    return $this->stringRestrictions;
  }
  /**
   * Major database engine versions for which this flag is supported.
   *
   * @param string[] $supportedDbVersions
   */
  public function setSupportedDbVersions($supportedDbVersions)
  {
    $this->supportedDbVersions = $supportedDbVersions;
  }
  /**
   * @return string[]
   */
  public function getSupportedDbVersions()
  {
    return $this->supportedDbVersions;
  }
  /**
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SupportedDatabaseFlag::class, 'Google_Service_CloudAlloyDBAdmin_SupportedDatabaseFlag');
