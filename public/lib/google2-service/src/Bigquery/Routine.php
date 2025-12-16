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

namespace Google\Service\Bigquery;

class Routine extends \Google\Collection
{
  /**
   * The data governance type is unspecified.
   */
  public const DATA_GOVERNANCE_TYPE_DATA_GOVERNANCE_TYPE_UNSPECIFIED = 'DATA_GOVERNANCE_TYPE_UNSPECIFIED';
  /**
   * The data governance type is data masking.
   */
  public const DATA_GOVERNANCE_TYPE_DATA_MASKING = 'DATA_MASKING';
  /**
   * The determinism of the UDF is unspecified.
   */
  public const DETERMINISM_LEVEL_DETERMINISM_LEVEL_UNSPECIFIED = 'DETERMINISM_LEVEL_UNSPECIFIED';
  /**
   * The UDF is deterministic, meaning that 2 function calls with the same
   * inputs always produce the same result, even across 2 query runs.
   */
  public const DETERMINISM_LEVEL_DETERMINISTIC = 'DETERMINISTIC';
  /**
   * The UDF is not deterministic.
   */
  public const DETERMINISM_LEVEL_NOT_DETERMINISTIC = 'NOT_DETERMINISTIC';
  /**
   * Default value.
   */
  public const LANGUAGE_LANGUAGE_UNSPECIFIED = 'LANGUAGE_UNSPECIFIED';
  /**
   * SQL language.
   */
  public const LANGUAGE_SQL = 'SQL';
  /**
   * JavaScript language.
   */
  public const LANGUAGE_JAVASCRIPT = 'JAVASCRIPT';
  /**
   * Python language.
   */
  public const LANGUAGE_PYTHON = 'PYTHON';
  /**
   * Java language.
   */
  public const LANGUAGE_JAVA = 'JAVA';
  /**
   * Scala language.
   */
  public const LANGUAGE_SCALA = 'SCALA';
  /**
   * Default value.
   */
  public const ROUTINE_TYPE_ROUTINE_TYPE_UNSPECIFIED = 'ROUTINE_TYPE_UNSPECIFIED';
  /**
   * Non-built-in persistent scalar function.
   */
  public const ROUTINE_TYPE_SCALAR_FUNCTION = 'SCALAR_FUNCTION';
  /**
   * Stored procedure.
   */
  public const ROUTINE_TYPE_PROCEDURE = 'PROCEDURE';
  /**
   * Non-built-in persistent TVF.
   */
  public const ROUTINE_TYPE_TABLE_VALUED_FUNCTION = 'TABLE_VALUED_FUNCTION';
  /**
   * Non-built-in persistent aggregate function.
   */
  public const ROUTINE_TYPE_AGGREGATE_FUNCTION = 'AGGREGATE_FUNCTION';
  /**
   * The security mode of the routine is unspecified.
   */
  public const SECURITY_MODE_SECURITY_MODE_UNSPECIFIED = 'SECURITY_MODE_UNSPECIFIED';
  /**
   * The routine is to be executed with the privileges of the user who defines
   * it.
   */
  public const SECURITY_MODE_DEFINER = 'DEFINER';
  /**
   * The routine is to be executed with the privileges of the user who invokes
   * it.
   */
  public const SECURITY_MODE_INVOKER = 'INVOKER';
  protected $collection_key = 'importedLibraries';
  protected $argumentsType = Argument::class;
  protected $argumentsDataType = 'array';
  /**
   * Output only. The time when this routine was created, in milliseconds since
   * the epoch.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Optional. If set to `DATA_MASKING`, the function is validated and made
   * available as a masking function. For more information, see [Create custom
   * masking routines](https://cloud.google.com/bigquery/docs/user-defined-
   * functions#custom-mask).
   *
   * @var string
   */
  public $dataGovernanceType;
  /**
   * Required. The body of the routine. For functions, this is the expression in
   * the AS clause. If `language = "SQL"`, it is the substring inside (but
   * excluding) the parentheses. For example, for the function created with the
   * following statement: `CREATE FUNCTION JoinLines(x string, y string) as
   * (concat(x, "\n", y))` The definition_body is `concat(x, "\n", y)` (\n is
   * not replaced with linebreak). If `language="JAVASCRIPT"`, it is the
   * evaluated string in the AS clause. For example, for the function created
   * with the following statement: `CREATE FUNCTION f() RETURNS STRING LANGUAGE
   * js AS 'return "\n";\n'` The definition_body is `return "\n";\n` Note that
   * both \n are replaced with linebreaks. If `definition_body` references
   * another routine, then that routine must be fully qualified with its project
   * ID.
   *
   * @var string
   */
  public $definitionBody;
  /**
   * Optional. The description of the routine, if defined.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. The determinism level of the JavaScript UDF, if defined.
   *
   * @var string
   */
  public $determinismLevel;
  /**
   * Output only. A hash of this resource.
   *
   * @var string
   */
  public $etag;
  protected $externalRuntimeOptionsType = ExternalRuntimeOptions::class;
  protected $externalRuntimeOptionsDataType = '';
  /**
   * Optional. If language = "JAVASCRIPT", this field stores the path of the
   * imported JAVASCRIPT libraries.
   *
   * @var string[]
   */
  public $importedLibraries;
  /**
   * Optional. Defaults to "SQL" if remote_function_options field is absent, not
   * set otherwise.
   *
   * @var string
   */
  public $language;
  /**
   * Output only. The time when this routine was last modified, in milliseconds
   * since the epoch.
   *
   * @var string
   */
  public $lastModifiedTime;
  protected $pythonOptionsType = PythonOptions::class;
  protected $pythonOptionsDataType = '';
  protected $remoteFunctionOptionsType = RemoteFunctionOptions::class;
  protected $remoteFunctionOptionsDataType = '';
  protected $returnTableTypeType = StandardSqlTableType::class;
  protected $returnTableTypeDataType = '';
  protected $returnTypeType = StandardSqlDataType::class;
  protected $returnTypeDataType = '';
  protected $routineReferenceType = RoutineReference::class;
  protected $routineReferenceDataType = '';
  /**
   * Required. The type of routine.
   *
   * @var string
   */
  public $routineType;
  /**
   * Optional. The security mode of the routine, if defined. If not defined, the
   * security mode is automatically determined from the routine's configuration.
   *
   * @var string
   */
  public $securityMode;
  protected $sparkOptionsType = SparkOptions::class;
  protected $sparkOptionsDataType = '';
  /**
   * Optional. Use this option to catch many common errors. Error checking is
   * not exhaustive, and successfully creating a procedure doesn't guarantee
   * that the procedure will successfully execute at runtime. If `strictMode` is
   * set to `TRUE`, the procedure body is further checked for errors such as
   * non-existent tables or columns. The `CREATE PROCEDURE` statement fails if
   * the body fails any of these checks. If `strictMode` is set to `FALSE`, the
   * procedure body is checked only for syntax. For procedures that invoke
   * themselves recursively, specify `strictMode=FALSE` to avoid non-existent
   * procedure errors during validation. Default value is `TRUE`.
   *
   * @var bool
   */
  public $strictMode;

  /**
   * Optional.
   *
   * @param Argument[] $arguments
   */
  public function setArguments($arguments)
  {
    $this->arguments = $arguments;
  }
  /**
   * @return Argument[]
   */
  public function getArguments()
  {
    return $this->arguments;
  }
  /**
   * Output only. The time when this routine was created, in milliseconds since
   * the epoch.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Optional. If set to `DATA_MASKING`, the function is validated and made
   * available as a masking function. For more information, see [Create custom
   * masking routines](https://cloud.google.com/bigquery/docs/user-defined-
   * functions#custom-mask).
   *
   * Accepted values: DATA_GOVERNANCE_TYPE_UNSPECIFIED, DATA_MASKING
   *
   * @param self::DATA_GOVERNANCE_TYPE_* $dataGovernanceType
   */
  public function setDataGovernanceType($dataGovernanceType)
  {
    $this->dataGovernanceType = $dataGovernanceType;
  }
  /**
   * @return self::DATA_GOVERNANCE_TYPE_*
   */
  public function getDataGovernanceType()
  {
    return $this->dataGovernanceType;
  }
  /**
   * Required. The body of the routine. For functions, this is the expression in
   * the AS clause. If `language = "SQL"`, it is the substring inside (but
   * excluding) the parentheses. For example, for the function created with the
   * following statement: `CREATE FUNCTION JoinLines(x string, y string) as
   * (concat(x, "\n", y))` The definition_body is `concat(x, "\n", y)` (\n is
   * not replaced with linebreak). If `language="JAVASCRIPT"`, it is the
   * evaluated string in the AS clause. For example, for the function created
   * with the following statement: `CREATE FUNCTION f() RETURNS STRING LANGUAGE
   * js AS 'return "\n";\n'` The definition_body is `return "\n";\n` Note that
   * both \n are replaced with linebreaks. If `definition_body` references
   * another routine, then that routine must be fully qualified with its project
   * ID.
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
   * Optional. The description of the routine, if defined.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. The determinism level of the JavaScript UDF, if defined.
   *
   * Accepted values: DETERMINISM_LEVEL_UNSPECIFIED, DETERMINISTIC,
   * NOT_DETERMINISTIC
   *
   * @param self::DETERMINISM_LEVEL_* $determinismLevel
   */
  public function setDeterminismLevel($determinismLevel)
  {
    $this->determinismLevel = $determinismLevel;
  }
  /**
   * @return self::DETERMINISM_LEVEL_*
   */
  public function getDeterminismLevel()
  {
    return $this->determinismLevel;
  }
  /**
   * Output only. A hash of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Options for the runtime of the external system executing the
   * routine. This field is only applicable for Python UDFs.
   * [Preview](https://cloud.google.com/products/#product-launch-stages)
   *
   * @param ExternalRuntimeOptions $externalRuntimeOptions
   */
  public function setExternalRuntimeOptions(ExternalRuntimeOptions $externalRuntimeOptions)
  {
    $this->externalRuntimeOptions = $externalRuntimeOptions;
  }
  /**
   * @return ExternalRuntimeOptions
   */
  public function getExternalRuntimeOptions()
  {
    return $this->externalRuntimeOptions;
  }
  /**
   * Optional. If language = "JAVASCRIPT", this field stores the path of the
   * imported JAVASCRIPT libraries.
   *
   * @param string[] $importedLibraries
   */
  public function setImportedLibraries($importedLibraries)
  {
    $this->importedLibraries = $importedLibraries;
  }
  /**
   * @return string[]
   */
  public function getImportedLibraries()
  {
    return $this->importedLibraries;
  }
  /**
   * Optional. Defaults to "SQL" if remote_function_options field is absent, not
   * set otherwise.
   *
   * Accepted values: LANGUAGE_UNSPECIFIED, SQL, JAVASCRIPT, PYTHON, JAVA, SCALA
   *
   * @param self::LANGUAGE_* $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return self::LANGUAGE_*
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * Output only. The time when this routine was last modified, in milliseconds
   * since the epoch.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * Optional. Options for the Python UDF.
   * [Preview](https://cloud.google.com/products/#product-launch-stages)
   *
   * @param PythonOptions $pythonOptions
   */
  public function setPythonOptions(PythonOptions $pythonOptions)
  {
    $this->pythonOptions = $pythonOptions;
  }
  /**
   * @return PythonOptions
   */
  public function getPythonOptions()
  {
    return $this->pythonOptions;
  }
  /**
   * Optional. Remote function specific options.
   *
   * @param RemoteFunctionOptions $remoteFunctionOptions
   */
  public function setRemoteFunctionOptions(RemoteFunctionOptions $remoteFunctionOptions)
  {
    $this->remoteFunctionOptions = $remoteFunctionOptions;
  }
  /**
   * @return RemoteFunctionOptions
   */
  public function getRemoteFunctionOptions()
  {
    return $this->remoteFunctionOptions;
  }
  /**
   * Optional. Can be set only if routine_type = "TABLE_VALUED_FUNCTION". If
   * absent, the return table type is inferred from definition_body at query
   * time in each query that references this routine. If present, then the
   * columns in the evaluated table result will be cast to match the column
   * types specified in return table type, at query time.
   *
   * @param StandardSqlTableType $returnTableType
   */
  public function setReturnTableType(StandardSqlTableType $returnTableType)
  {
    $this->returnTableType = $returnTableType;
  }
  /**
   * @return StandardSqlTableType
   */
  public function getReturnTableType()
  {
    return $this->returnTableType;
  }
  /**
   * Optional if language = "SQL"; required otherwise. Cannot be set if
   * routine_type = "TABLE_VALUED_FUNCTION". If absent, the return type is
   * inferred from definition_body at query time in each query that references
   * this routine. If present, then the evaluated result will be cast to the
   * specified returned type at query time. For example, for the functions
   * created with the following statements: * `CREATE FUNCTION Add(x FLOAT64, y
   * FLOAT64) RETURNS FLOAT64 AS (x + y);` * `CREATE FUNCTION Increment(x
   * FLOAT64) AS (Add(x, 1));` * `CREATE FUNCTION Decrement(x FLOAT64) RETURNS
   * FLOAT64 AS (Add(x, -1));` The return_type is `{type_kind: "FLOAT64"}` for
   * `Add` and `Decrement`, and is absent for `Increment` (inferred as FLOAT64
   * at query time). Suppose the function `Add` is replaced by `CREATE OR
   * REPLACE FUNCTION Add(x INT64, y INT64) AS (x + y);` Then the inferred
   * return type of `Increment` is automatically changed to INT64 at query time,
   * while the return type of `Decrement` remains FLOAT64.
   *
   * @param StandardSqlDataType $returnType
   */
  public function setReturnType(StandardSqlDataType $returnType)
  {
    $this->returnType = $returnType;
  }
  /**
   * @return StandardSqlDataType
   */
  public function getReturnType()
  {
    return $this->returnType;
  }
  /**
   * Required. Reference describing the ID of this routine.
   *
   * @param RoutineReference $routineReference
   */
  public function setRoutineReference(RoutineReference $routineReference)
  {
    $this->routineReference = $routineReference;
  }
  /**
   * @return RoutineReference
   */
  public function getRoutineReference()
  {
    return $this->routineReference;
  }
  /**
   * Required. The type of routine.
   *
   * Accepted values: ROUTINE_TYPE_UNSPECIFIED, SCALAR_FUNCTION, PROCEDURE,
   * TABLE_VALUED_FUNCTION, AGGREGATE_FUNCTION
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
  /**
   * Optional. The security mode of the routine, if defined. If not defined, the
   * security mode is automatically determined from the routine's configuration.
   *
   * Accepted values: SECURITY_MODE_UNSPECIFIED, DEFINER, INVOKER
   *
   * @param self::SECURITY_MODE_* $securityMode
   */
  public function setSecurityMode($securityMode)
  {
    $this->securityMode = $securityMode;
  }
  /**
   * @return self::SECURITY_MODE_*
   */
  public function getSecurityMode()
  {
    return $this->securityMode;
  }
  /**
   * Optional. Spark specific options.
   *
   * @param SparkOptions $sparkOptions
   */
  public function setSparkOptions(SparkOptions $sparkOptions)
  {
    $this->sparkOptions = $sparkOptions;
  }
  /**
   * @return SparkOptions
   */
  public function getSparkOptions()
  {
    return $this->sparkOptions;
  }
  /**
   * Optional. Use this option to catch many common errors. Error checking is
   * not exhaustive, and successfully creating a procedure doesn't guarantee
   * that the procedure will successfully execute at runtime. If `strictMode` is
   * set to `TRUE`, the procedure body is further checked for errors such as
   * non-existent tables or columns. The `CREATE PROCEDURE` statement fails if
   * the body fails any of these checks. If `strictMode` is set to `FALSE`, the
   * procedure body is checked only for syntax. For procedures that invoke
   * themselves recursively, specify `strictMode=FALSE` to avoid non-existent
   * procedure errors during validation. Default value is `TRUE`.
   *
   * @param bool $strictMode
   */
  public function setStrictMode($strictMode)
  {
    $this->strictMode = $strictMode;
  }
  /**
   * @return bool
   */
  public function getStrictMode()
  {
    return $this->strictMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Routine::class, 'Google_Service_Bigquery_Routine');
