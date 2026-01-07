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

namespace Google\Service\Dataproc;

class SparkSqlBatch extends \Google\Collection
{
  protected $collection_key = 'jarFileUris';
  /**
   * Optional. HCFS URIs of jar files to be added to the Spark CLASSPATH.
   *
   * @var string[]
   */
  public $jarFileUris;
  /**
   * Required. The HCFS URI of the script that contains Spark SQL queries to
   * execute.
   *
   * @var string
   */
  public $queryFileUri;
  /**
   * Optional. Mapping of query variable names to values (equivalent to the
   * Spark SQL command: SET name="value";).
   *
   * @var string[]
   */
  public $queryVariables;

  /**
   * Optional. HCFS URIs of jar files to be added to the Spark CLASSPATH.
   *
   * @param string[] $jarFileUris
   */
  public function setJarFileUris($jarFileUris)
  {
    $this->jarFileUris = $jarFileUris;
  }
  /**
   * @return string[]
   */
  public function getJarFileUris()
  {
    return $this->jarFileUris;
  }
  /**
   * Required. The HCFS URI of the script that contains Spark SQL queries to
   * execute.
   *
   * @param string $queryFileUri
   */
  public function setQueryFileUri($queryFileUri)
  {
    $this->queryFileUri = $queryFileUri;
  }
  /**
   * @return string
   */
  public function getQueryFileUri()
  {
    return $this->queryFileUri;
  }
  /**
   * Optional. Mapping of query variable names to values (equivalent to the
   * Spark SQL command: SET name="value";).
   *
   * @param string[] $queryVariables
   */
  public function setQueryVariables($queryVariables)
  {
    $this->queryVariables = $queryVariables;
  }
  /**
   * @return string[]
   */
  public function getQueryVariables()
  {
    return $this->queryVariables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkSqlBatch::class, 'Google_Service_Dataproc_SparkSqlBatch');
