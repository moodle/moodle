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

class WorkspaceCompilationOverrides extends \Google\Model
{
  /**
   * Optional. The default database (Google Cloud project ID).
   *
   * @var string
   */
  public $defaultDatabase;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkspaceCompilationOverrides::class, 'Google_Service_Dataform_WorkspaceCompilationOverrides');
