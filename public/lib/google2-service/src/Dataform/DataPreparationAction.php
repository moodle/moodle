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

class DataPreparationAction extends \Google\Model
{
  protected $contentsSqlType = ActionSqlDefinition::class;
  protected $contentsSqlDataType = '';
  /**
   * Output only. YAML representing the contents of the data preparation. Can be
   * used to show the customer what the input was to their workflow.
   *
   * @var string
   */
  public $contentsYaml;
  /**
   * Output only. The generated BigQuery SQL script that will be executed. For
   * reference only.
   *
   * @var string
   */
  public $generatedSql;
  /**
   * Output only. The ID of the BigQuery job that executed the SQL in
   * sql_script. Only set once the job has started to run.
   *
   * @var string
   */
  public $jobId;

  /**
   * SQL definition for a Data Preparation. Contains a SQL query and additional
   * context information.
   *
   * @param ActionSqlDefinition $contentsSql
   */
  public function setContentsSql(ActionSqlDefinition $contentsSql)
  {
    $this->contentsSql = $contentsSql;
  }
  /**
   * @return ActionSqlDefinition
   */
  public function getContentsSql()
  {
    return $this->contentsSql;
  }
  /**
   * Output only. YAML representing the contents of the data preparation. Can be
   * used to show the customer what the input was to their workflow.
   *
   * @param string $contentsYaml
   */
  public function setContentsYaml($contentsYaml)
  {
    $this->contentsYaml = $contentsYaml;
  }
  /**
   * @return string
   */
  public function getContentsYaml()
  {
    return $this->contentsYaml;
  }
  /**
   * Output only. The generated BigQuery SQL script that will be executed. For
   * reference only.
   *
   * @param string $generatedSql
   */
  public function setGeneratedSql($generatedSql)
  {
    $this->generatedSql = $generatedSql;
  }
  /**
   * @return string
   */
  public function getGeneratedSql()
  {
    return $this->generatedSql;
  }
  /**
   * Output only. The ID of the BigQuery job that executed the SQL in
   * sql_script. Only set once the job has started to run.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataPreparationAction::class, 'Google_Service_Dataform_DataPreparationAction');
