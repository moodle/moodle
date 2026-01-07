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

namespace Google\Service\Dataform;

class CodeCompilationConfig extends \Google\Model
{
  /**
   * Optional. The default schema (BigQuery dataset ID) for assertions.
   *
   * @var string
   */
  public $assertionSchema;
  /**
   * Optional. The prefix to prepend to built-in assertion names.
   *
   * @var string
   */
  public $builtinAssertionNamePrefix;
  /**
   * Optional. The suffix that should be appended to all database (Google Cloud
   * project ID) names.
   *
   * @var string
   */
  public $databaseSuffix;
  /**
   * Optional. The default database (Google Cloud project ID).
   *
   * @var string
   */
  public $defaultDatabase;
  /**
   * Optional. The default BigQuery location to use. Defaults to "US". See the
   * BigQuery docs for a full list of locations:
   * https://cloud.google.com/bigquery/docs/locations.
   *
   * @var string
   */
  public $defaultLocation;
  protected $defaultNotebookRuntimeOptionsType = NotebookRuntimeOptions::class;
  protected $defaultNotebookRuntimeOptionsDataType = '';
  /**
   * Optional. The default schema (BigQuery dataset ID).
   *
   * @var string
   */
  public $defaultSchema;
  /**
   * Optional. The suffix that should be appended to all schema (BigQuery
   * dataset ID) names.
   *
   * @var string
   */
  public $schemaSuffix;
  /**
   * Optional. The prefix that should be prepended to all table names.
   *
   * @var string
   */
  public $tablePrefix;
  /**
   * Optional. User-defined variables that are made available to project code
   * during compilation.
   *
   * @var string[]
   */
  public $vars;

  /**
   * Optional. The default schema (BigQuery dataset ID) for assertions.
   *
   * @param string $assertionSchema
   */
  public function setAssertionSchema($assertionSchema)
  {
    $this->assertionSchema = $assertionSchema;
  }
  /**
   * @return string
   */
  public function getAssertionSchema()
  {
    return $this->assertionSchema;
  }
  /**
   * Optional. The prefix to prepend to built-in assertion names.
   *
   * @param string $builtinAssertionNamePrefix
   */
  public function setBuiltinAssertionNamePrefix($builtinAssertionNamePrefix)
  {
    $this->builtinAssertionNamePrefix = $builtinAssertionNamePrefix;
  }
  /**
   * @return string
   */
  public function getBuiltinAssertionNamePrefix()
  {
    return $this->builtinAssertionNamePrefix;
  }
  /**
   * Optional. The suffix that should be appended to all database (Google Cloud
   * project ID) names.
   *
   * @param string $databaseSuffix
   */
  public function setDatabaseSuffix($databaseSuffix)
  {
    $this->databaseSuffix = $databaseSuffix;
  }
  /**
   * @return string
   */
  public function getDatabaseSuffix()
  {
    return $this->databaseSuffix;
  }
  /**
   * Optional. The default database (Google Cloud project ID).
   *
   * @param string $defaultDatabase
   */
  public function setDefaultDatabase($defaultDatabase)
  {
    $this->defaultDatabase = $defaultDatabase;
  }
  /**
   * @return string
   */
  public function getDefaultDatabase()
  {
    return $this->defaultDatabase;
  }
  /**
   * Optional. The default BigQuery location to use. Defaults to "US". See the
   * BigQuery docs for a full list of locations:
   * https://cloud.google.com/bigquery/docs/locations.
   *
   * @param string $defaultLocation
   */
  public function setDefaultLocation($defaultLocation)
  {
    $this->defaultLocation = $defaultLocation;
  }
  /**
   * @return string
   */
  public function getDefaultLocation()
  {
    return $this->defaultLocation;
  }
  /**
   * Optional. The default notebook runtime options.
   *
   * @param NotebookRuntimeOptions $defaultNotebookRuntimeOptions
   */
  public function setDefaultNotebookRuntimeOptions(NotebookRuntimeOptions $defaultNotebookRuntimeOptions)
  {
    $this->defaultNotebookRuntimeOptions = $defaultNotebookRuntimeOptions;
  }
  /**
   * @return NotebookRuntimeOptions
   */
  public function getDefaultNotebookRuntimeOptions()
  {
    return $this->defaultNotebookRuntimeOptions;
  }
  /**
   * Optional. The default schema (BigQuery dataset ID).
   *
   * @param string $defaultSchema
   */
  public function setDefaultSchema($defaultSchema)
  {
    $this->defaultSchema = $defaultSchema;
  }
  /**
   * @return string
   */
  public function getDefaultSchema()
  {
    return $this->defaultSchema;
  }
  /**
   * Optional. The suffix that should be appended to all schema (BigQuery
   * dataset ID) names.
   *
   * @param string $schemaSuffix
   */
  public function setSchemaSuffix($schemaSuffix)
  {
    $this->schemaSuffix = $schemaSuffix;
  }
  /**
   * @return string
   */
  public function getSchemaSuffix()
  {
    return $this->schemaSuffix;
  }
  /**
   * Optional. The prefix that should be prepended to all table names.
   *
   * @param string $tablePrefix
   */
  public function setTablePrefix($tablePrefix)
  {
    $this->tablePrefix = $tablePrefix;
  }
  /**
   * @return string
   */
  public function getTablePrefix()
  {
    return $this->tablePrefix;
  }
  /**
   * Optional. User-defined variables that are made available to project code
   * during compilation.
   *
   * @param string[] $vars
   */
  public function setVars($vars)
  {
    $this->vars = $vars;
  }
  /**
   * @return string[]
   */
  public function getVars()
  {
    return $this->vars;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CodeCompilationConfig::class, 'Google_Service_Dataform_CodeCompilationConfig');
