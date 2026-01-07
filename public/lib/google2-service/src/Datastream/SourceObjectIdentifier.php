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

class SourceObjectIdentifier extends \Google\Model
{
  protected $mongodbIdentifierType = MongodbObjectIdentifier::class;
  protected $mongodbIdentifierDataType = '';
  protected $mysqlIdentifierType = MysqlObjectIdentifier::class;
  protected $mysqlIdentifierDataType = '';
  protected $oracleIdentifierType = OracleObjectIdentifier::class;
  protected $oracleIdentifierDataType = '';
  protected $postgresqlIdentifierType = PostgresqlObjectIdentifier::class;
  protected $postgresqlIdentifierDataType = '';
  protected $salesforceIdentifierType = SalesforceObjectIdentifier::class;
  protected $salesforceIdentifierDataType = '';
  protected $sqlServerIdentifierType = SqlServerObjectIdentifier::class;
  protected $sqlServerIdentifierDataType = '';

  /**
   * MongoDB data source object identifier.
   *
   * @param MongodbObjectIdentifier $mongodbIdentifier
   */
  public function setMongodbIdentifier(MongodbObjectIdentifier $mongodbIdentifier)
  {
    $this->mongodbIdentifier = $mongodbIdentifier;
  }
  /**
   * @return MongodbObjectIdentifier
   */
  public function getMongodbIdentifier()
  {
    return $this->mongodbIdentifier;
  }
  /**
   * Mysql data source object identifier.
   *
   * @param MysqlObjectIdentifier $mysqlIdentifier
   */
  public function setMysqlIdentifier(MysqlObjectIdentifier $mysqlIdentifier)
  {
    $this->mysqlIdentifier = $mysqlIdentifier;
  }
  /**
   * @return MysqlObjectIdentifier
   */
  public function getMysqlIdentifier()
  {
    return $this->mysqlIdentifier;
  }
  /**
   * Oracle data source object identifier.
   *
   * @param OracleObjectIdentifier $oracleIdentifier
   */
  public function setOracleIdentifier(OracleObjectIdentifier $oracleIdentifier)
  {
    $this->oracleIdentifier = $oracleIdentifier;
  }
  /**
   * @return OracleObjectIdentifier
   */
  public function getOracleIdentifier()
  {
    return $this->oracleIdentifier;
  }
  /**
   * PostgreSQL data source object identifier.
   *
   * @param PostgresqlObjectIdentifier $postgresqlIdentifier
   */
  public function setPostgresqlIdentifier(PostgresqlObjectIdentifier $postgresqlIdentifier)
  {
    $this->postgresqlIdentifier = $postgresqlIdentifier;
  }
  /**
   * @return PostgresqlObjectIdentifier
   */
  public function getPostgresqlIdentifier()
  {
    return $this->postgresqlIdentifier;
  }
  /**
   * Salesforce data source object identifier.
   *
   * @param SalesforceObjectIdentifier $salesforceIdentifier
   */
  public function setSalesforceIdentifier(SalesforceObjectIdentifier $salesforceIdentifier)
  {
    $this->salesforceIdentifier = $salesforceIdentifier;
  }
  /**
   * @return SalesforceObjectIdentifier
   */
  public function getSalesforceIdentifier()
  {
    return $this->salesforceIdentifier;
  }
  /**
   * SQLServer data source object identifier.
   *
   * @param SqlServerObjectIdentifier $sqlServerIdentifier
   */
  public function setSqlServerIdentifier(SqlServerObjectIdentifier $sqlServerIdentifier)
  {
    $this->sqlServerIdentifier = $sqlServerIdentifier;
  }
  /**
   * @return SqlServerObjectIdentifier
   */
  public function getSqlServerIdentifier()
  {
    return $this->sqlServerIdentifier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceObjectIdentifier::class, 'Google_Service_Datastream_SourceObjectIdentifier');
