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

namespace Google\Service\Datastream;

class BackfillAllStrategy extends \Google\Model
{
  protected $mongodbExcludedObjectsType = MongodbCluster::class;
  protected $mongodbExcludedObjectsDataType = '';
  protected $mysqlExcludedObjectsType = MysqlRdbms::class;
  protected $mysqlExcludedObjectsDataType = '';
  protected $oracleExcludedObjectsType = OracleRdbms::class;
  protected $oracleExcludedObjectsDataType = '';
  protected $postgresqlExcludedObjectsType = PostgresqlRdbms::class;
  protected $postgresqlExcludedObjectsDataType = '';
  protected $salesforceExcludedObjectsType = SalesforceOrg::class;
  protected $salesforceExcludedObjectsDataType = '';
  protected $sqlServerExcludedObjectsType = SqlServerRdbms::class;
  protected $sqlServerExcludedObjectsDataType = '';

  /**
   * MongoDB data source objects to avoid backfilling
   *
   * @param MongodbCluster $mongodbExcludedObjects
   */
  public function setMongodbExcludedObjects(MongodbCluster $mongodbExcludedObjects)
  {
    $this->mongodbExcludedObjects = $mongodbExcludedObjects;
  }
  /**
   * @return MongodbCluster
   */
  public function getMongodbExcludedObjects()
  {
    return $this->mongodbExcludedObjects;
  }
  /**
   * MySQL data source objects to avoid backfilling.
   *
   * @param MysqlRdbms $mysqlExcludedObjects
   */
  public function setMysqlExcludedObjects(MysqlRdbms $mysqlExcludedObjects)
  {
    $this->mysqlExcludedObjects = $mysqlExcludedObjects;
  }
  /**
   * @return MysqlRdbms
   */
  public function getMysqlExcludedObjects()
  {
    return $this->mysqlExcludedObjects;
  }
  /**
   * Oracle data source objects to avoid backfilling.
   *
   * @param OracleRdbms $oracleExcludedObjects
   */
  public function setOracleExcludedObjects(OracleRdbms $oracleExcludedObjects)
  {
    $this->oracleExcludedObjects = $oracleExcludedObjects;
  }
  /**
   * @return OracleRdbms
   */
  public function getOracleExcludedObjects()
  {
    return $this->oracleExcludedObjects;
  }
  /**
   * PostgreSQL data source objects to avoid backfilling.
   *
   * @param PostgresqlRdbms $postgresqlExcludedObjects
   */
  public function setPostgresqlExcludedObjects(PostgresqlRdbms $postgresqlExcludedObjects)
  {
    $this->postgresqlExcludedObjects = $postgresqlExcludedObjects;
  }
  /**
   * @return PostgresqlRdbms
   */
  public function getPostgresqlExcludedObjects()
  {
    return $this->postgresqlExcludedObjects;
  }
  /**
   * Salesforce data source objects to avoid backfilling
   *
   * @param SalesforceOrg $salesforceExcludedObjects
   */
  public function setSalesforceExcludedObjects(SalesforceOrg $salesforceExcludedObjects)
  {
    $this->salesforceExcludedObjects = $salesforceExcludedObjects;
  }
  /**
   * @return SalesforceOrg
   */
  public function getSalesforceExcludedObjects()
  {
    return $this->salesforceExcludedObjects;
  }
  /**
   * SQLServer data source objects to avoid backfilling
   *
   * @param SqlServerRdbms $sqlServerExcludedObjects
   */
  public function setSqlServerExcludedObjects(SqlServerRdbms $sqlServerExcludedObjects)
  {
    $this->sqlServerExcludedObjects = $sqlServerExcludedObjects;
  }
  /**
   * @return SqlServerRdbms
   */
  public function getSqlServerExcludedObjects()
  {
    return $this->sqlServerExcludedObjects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackfillAllStrategy::class, 'Google_Service_Datastream_BackfillAllStrategy');
