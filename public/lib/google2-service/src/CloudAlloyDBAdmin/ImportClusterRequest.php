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

class ImportClusterRequest extends \Google\Model
{
  protected $csvImportOptionsType = CsvImportOptions::class;
  protected $csvImportOptionsDataType = '';
  /**
   * Optional. Name of the database to which the import will be done. For import
   * from SQL file, this is required only if the file does not specify a
   * database. Note - Value provided should be the same as expected from `SELECT
   * current_database();` and NOT as a resource reference.
   *
   * @var string
   */
  public $database;
  /**
   * Required. The path to the file in Google Cloud Storage where the source
   * file for import will be stored. The URI is in the form
   * `gs://bucketName/fileName`.
   *
   * @var string
   */
  public $gcsUri;
  protected $sqlImportOptionsType = SqlImportOptions::class;
  protected $sqlImportOptionsDataType = '';
  /**
   * Optional. Database user to be used for importing the data. Note - Value
   * provided should be the same as expected from `SELECT current_user;` and NOT
   * as a resource reference.
   *
   * @var string
   */
  public $user;

  /**
   * Options for importing data in CSV format.
   *
   * @param CsvImportOptions $csvImportOptions
   */
  public function setCsvImportOptions(CsvImportOptions $csvImportOptions)
  {
    $this->csvImportOptions = $csvImportOptions;
  }
  /**
   * @return CsvImportOptions
   */
  public function getCsvImportOptions()
  {
    return $this->csvImportOptions;
  }
  /**
   * Optional. Name of the database to which the import will be done. For import
   * from SQL file, this is required only if the file does not specify a
   * database. Note - Value provided should be the same as expected from `SELECT
   * current_database();` and NOT as a resource reference.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Required. The path to the file in Google Cloud Storage where the source
   * file for import will be stored. The URI is in the form
   * `gs://bucketName/fileName`.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
  /**
   * Options for importing data in SQL format.
   *
   * @param SqlImportOptions $sqlImportOptions
   */
  public function setSqlImportOptions(SqlImportOptions $sqlImportOptions)
  {
    $this->sqlImportOptions = $sqlImportOptions;
  }
  /**
   * @return SqlImportOptions
   */
  public function getSqlImportOptions()
  {
    return $this->sqlImportOptions;
  }
  /**
   * Optional. Database user to be used for importing the data. Note - Value
   * provided should be the same as expected from `SELECT current_user;` and NOT
   * as a resource reference.
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportClusterRequest::class, 'Google_Service_CloudAlloyDBAdmin_ImportClusterRequest');
