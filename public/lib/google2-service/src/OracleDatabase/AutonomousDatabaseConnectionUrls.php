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

namespace Google\Service\OracleDatabase;

class AutonomousDatabaseConnectionUrls extends \Google\Model
{
  /**
   * Output only. Oracle Application Express (APEX) URL.
   *
   * @var string
   */
  public $apexUri;
  /**
   * Output only. The URL of the Database Transforms for the Autonomous
   * Database.
   *
   * @var string
   */
  public $databaseTransformsUri;
  /**
   * Output only. The URL of the Graph Studio for the Autonomous Database.
   *
   * @var string
   */
  public $graphStudioUri;
  /**
   * Output only. The URL of the Oracle Machine Learning (OML) Notebook for the
   * Autonomous Database.
   *
   * @var string
   */
  public $machineLearningNotebookUri;
  /**
   * Output only. The URL of Machine Learning user management the Autonomous
   * Database.
   *
   * @var string
   */
  public $machineLearningUserManagementUri;
  /**
   * Output only. The URL of the MongoDB API for the Autonomous Database.
   *
   * @var string
   */
  public $mongoDbUri;
  /**
   * Output only. The Oracle REST Data Services (ORDS) URL of the Web Access for
   * the Autonomous Database.
   *
   * @var string
   */
  public $ordsUri;
  /**
   * Output only. The URL of the Oracle SQL Developer Web for the Autonomous
   * Database.
   *
   * @var string
   */
  public $sqlDevWebUri;

  /**
   * Output only. Oracle Application Express (APEX) URL.
   *
   * @param string $apexUri
   */
  public function setApexUri($apexUri)
  {
    $this->apexUri = $apexUri;
  }
  /**
   * @return string
   */
  public function getApexUri()
  {
    return $this->apexUri;
  }
  /**
   * Output only. The URL of the Database Transforms for the Autonomous
   * Database.
   *
   * @param string $databaseTransformsUri
   */
  public function setDatabaseTransformsUri($databaseTransformsUri)
  {
    $this->databaseTransformsUri = $databaseTransformsUri;
  }
  /**
   * @return string
   */
  public function getDatabaseTransformsUri()
  {
    return $this->databaseTransformsUri;
  }
  /**
   * Output only. The URL of the Graph Studio for the Autonomous Database.
   *
   * @param string $graphStudioUri
   */
  public function setGraphStudioUri($graphStudioUri)
  {
    $this->graphStudioUri = $graphStudioUri;
  }
  /**
   * @return string
   */
  public function getGraphStudioUri()
  {
    return $this->graphStudioUri;
  }
  /**
   * Output only. The URL of the Oracle Machine Learning (OML) Notebook for the
   * Autonomous Database.
   *
   * @param string $machineLearningNotebookUri
   */
  public function setMachineLearningNotebookUri($machineLearningNotebookUri)
  {
    $this->machineLearningNotebookUri = $machineLearningNotebookUri;
  }
  /**
   * @return string
   */
  public function getMachineLearningNotebookUri()
  {
    return $this->machineLearningNotebookUri;
  }
  /**
   * Output only. The URL of Machine Learning user management the Autonomous
   * Database.
   *
   * @param string $machineLearningUserManagementUri
   */
  public function setMachineLearningUserManagementUri($machineLearningUserManagementUri)
  {
    $this->machineLearningUserManagementUri = $machineLearningUserManagementUri;
  }
  /**
   * @return string
   */
  public function getMachineLearningUserManagementUri()
  {
    return $this->machineLearningUserManagementUri;
  }
  /**
   * Output only. The URL of the MongoDB API for the Autonomous Database.
   *
   * @param string $mongoDbUri
   */
  public function setMongoDbUri($mongoDbUri)
  {
    $this->mongoDbUri = $mongoDbUri;
  }
  /**
   * @return string
   */
  public function getMongoDbUri()
  {
    return $this->mongoDbUri;
  }
  /**
   * Output only. The Oracle REST Data Services (ORDS) URL of the Web Access for
   * the Autonomous Database.
   *
   * @param string $ordsUri
   */
  public function setOrdsUri($ordsUri)
  {
    $this->ordsUri = $ordsUri;
  }
  /**
   * @return string
   */
  public function getOrdsUri()
  {
    return $this->ordsUri;
  }
  /**
   * Output only. The URL of the Oracle SQL Developer Web for the Autonomous
   * Database.
   *
   * @param string $sqlDevWebUri
   */
  public function setSqlDevWebUri($sqlDevWebUri)
  {
    $this->sqlDevWebUri = $sqlDevWebUri;
  }
  /**
   * @return string
   */
  public function getSqlDevWebUri()
  {
    return $this->sqlDevWebUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutonomousDatabaseConnectionUrls::class, 'Google_Service_OracleDatabase_AutonomousDatabaseConnectionUrls');
