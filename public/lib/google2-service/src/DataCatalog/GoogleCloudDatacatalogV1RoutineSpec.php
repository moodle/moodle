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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1RoutineSpec extends \Google\Collection
{
  /**
   * Unspecified type.
   */
  public const ROUTINE_TYPE_ROUTINE_TYPE_UNSPECIFIED = 'ROUTINE_TYPE_UNSPECIFIED';
  /**
   * Non-builtin permanent scalar function.
   */
  public const ROUTINE_TYPE_SCALAR_FUNCTION = 'SCALAR_FUNCTION';
  /**
   * Stored procedure.
   */
  public const ROUTINE_TYPE_PROCEDURE = 'PROCEDURE';
  protected $collection_key = 'routineArguments';
  protected $bigqueryRoutineSpecType = GoogleCloudDatacatalogV1BigQueryRoutineSpec::class;
  protected $bigqueryRoutineSpecDataType = '';
  /**
   * The body of the routine.
   *
   * @var string
   */
  public $definitionBody;
  /**
   * The language the routine is written in. The exact value depends on the
   * source system. For BigQuery routines, possible values are: * `SQL` *
   * `JAVASCRIPT`
   *
   * @var string
   */
  public $language;
  /**
   * Return type of the argument. The exact value depends on the source system
   * and the language.
   *
   * @var string
   */
  public $returnType;
  protected $routineArgumentsType = GoogleCloudDatacatalogV1RoutineSpecArgument::class;
  protected $routineArgumentsDataType = 'array';
  /**
   * The type of the routine.
   *
   * @var string
   */
  public $routineType;

  /**
   * Fields specific for BigQuery routines.
   *
   * @param GoogleCloudDatacatalogV1BigQueryRoutineSpec $bigqueryRoutineSpec
   */
  public function setBigqueryRoutineSpec(GoogleCloudDatacatalogV1BigQueryRoutineSpec $bigqueryRoutineSpec)
  {
    $this->bigqueryRoutineSpec = $bigqueryRoutineSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1BigQueryRoutineSpec
   */
  public function getBigqueryRoutineSpec()
  {
    return $this->bigqueryRoutineSpec;
  }
  /**
   * The body of the routine.
   *
   * @param string $definitionBody
   */
  public function setDefinitionBody($definitionBody)
  {
    $this->definitionBody = $definitionBody;
  }
  /**
   * @return string
   */
  public function getDefinitionBody()
  {
    return $this->definitionBody;
  }
  /**
   * The language the routine is written in. The exact value depends on the
   * source system. For BigQuery routines, possible values are: * `SQL` *
   * `JAVASCRIPT`
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * Return type of the argument. The exact value depends on the source system
   * and the language.
   *
   * @param string $returnType
   */
  public function setReturnType($returnType)
  {
    $this->returnType = $returnType;
  }
  /**
   * @return string
   */
  public function getReturnType()
  {
    return $this->returnType;
  }
  /**
   * Arguments of the routine.
   *
   * @param GoogleCloudDatacatalogV1RoutineSpecArgument[] $routineArguments
   */
  public function setRoutineArguments($routineArguments)
  {
    $this->routineArguments = $routineArguments;
  }
  /**
   * @return GoogleCloudDatacatalogV1RoutineSpecArgument[]
   */
  public function getRoutineArguments()
  {
    return $this->routineArguments;
  }
  /**
   * The type of the routine.
   *
   * Accepted values: ROUTINE_TYPE_UNSPECIFIED, SCALAR_FUNCTION, PROCEDURE
   *
   * @param self::ROUTINE_TYPE_* $routineType
   */
  public function setRoutineType($routineType)
  {
    $this->routineType = $routineType;
  }
  /**
   * @return self::ROUTINE_TYPE_*
   */
  public function getRoutineType()
  {
    return $this->routineType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1RoutineSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1RoutineSpec');
