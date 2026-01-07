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

namespace Google\Service\CloudRedis;

class Product extends \Google\Model
{
  /**
   * UNSPECIFIED means engine type is not known or available.
   */
  public const ENGINE_ENGINE_UNSPECIFIED = 'ENGINE_UNSPECIFIED';
  /**
   * MySQL binary running as an engine in the database instance.
   */
  public const ENGINE_ENGINE_MYSQL = 'ENGINE_MYSQL';
  /**
   * MySQL binary running as engine in database instance.
   *
   * @deprecated
   */
  public const ENGINE_MYSQL = 'MYSQL';
  /**
   * Postgres binary running as engine in database instance.
   */
  public const ENGINE_ENGINE_POSTGRES = 'ENGINE_POSTGRES';
  /**
   * Postgres binary running as engine in database instance.
   *
   * @deprecated
   */
  public const ENGINE_POSTGRES = 'POSTGRES';
  /**
   * SQLServer binary running as engine in database instance.
   */
  public const ENGINE_ENGINE_SQL_SERVER = 'ENGINE_SQL_SERVER';
  /**
   * SQLServer binary running as engine in database instance.
   *
   * @deprecated
   */
  public const ENGINE_SQL_SERVER = 'SQL_SERVER';
  /**
   * Native database binary running as engine in instance.
   */
  public const ENGINE_ENGINE_NATIVE = 'ENGINE_NATIVE';
  /**
   * Native database binary running as engine in instance.
   *
   * @deprecated
   */
  public const ENGINE_NATIVE = 'NATIVE';
  /**
   * Cloud Spanner with PostgreSQL dialect.
   */
  public const ENGINE_ENGINE_CLOUD_SPANNER_WITH_POSTGRES_DIALECT = 'ENGINE_CLOUD_SPANNER_WITH_POSTGRES_DIALECT';
  /**
   * Cloud Spanner with Google SQL dialect.
   */
  public const ENGINE_ENGINE_CLOUD_SPANNER_WITH_GOOGLESQL_DIALECT = 'ENGINE_CLOUD_SPANNER_WITH_GOOGLESQL_DIALECT';
  /**
   * Memorystore with Redis dialect.
   */
  public const ENGINE_ENGINE_MEMORYSTORE_FOR_REDIS = 'ENGINE_MEMORYSTORE_FOR_REDIS';
  /**
   * Memorystore with Redis cluster dialect.
   */
  public const ENGINE_ENGINE_MEMORYSTORE_FOR_REDIS_CLUSTER = 'ENGINE_MEMORYSTORE_FOR_REDIS_CLUSTER';
  /**
   * Other refers to rest of other database engine. This is to be when engine is
   * known, but it is not present in this enum.
   */
  public const ENGINE_ENGINE_OTHER = 'ENGINE_OTHER';
  /**
   * Firestore with native mode.
   */
  public const ENGINE_ENGINE_FIRESTORE_WITH_NATIVE_MODE = 'ENGINE_FIRESTORE_WITH_NATIVE_MODE';
  /**
   * Firestore with datastore mode.
   */
  public const ENGINE_ENGINE_FIRESTORE_WITH_DATASTORE_MODE = 'ENGINE_FIRESTORE_WITH_DATASTORE_MODE';
  /**
   * Firestore with MongoDB compatibility mode.
   */
  public const ENGINE_ENGINE_FIRESTORE_WITH_MONGODB_COMPATIBILITY_MODE = 'ENGINE_FIRESTORE_WITH_MONGODB_COMPATIBILITY_MODE';
  /**
   * Oracle Exadata engine.
   */
  public const ENGINE_ENGINE_EXADATA_ORACLE = 'ENGINE_EXADATA_ORACLE';
  /**
   * Oracle Autonomous DB Serverless engine.
   */
  public const ENGINE_ENGINE_ADB_SERVERLESS_ORACLE = 'ENGINE_ADB_SERVERLESS_ORACLE';
  /**
   * UNSPECIFIED means product type is not known or available.
   */
  public const TYPE_PRODUCT_TYPE_UNSPECIFIED = 'PRODUCT_TYPE_UNSPECIFIED';
  /**
   * Cloud SQL product area in GCP
   */
  public const TYPE_PRODUCT_TYPE_CLOUD_SQL = 'PRODUCT_TYPE_CLOUD_SQL';
  /**
   * Cloud SQL product area in GCP
   *
   * @deprecated
   */
  public const TYPE_CLOUD_SQL = 'CLOUD_SQL';
  /**
   * AlloyDB product area in GCP
   */
  public const TYPE_PRODUCT_TYPE_ALLOYDB = 'PRODUCT_TYPE_ALLOYDB';
  /**
   * AlloyDB product area in GCP
   *
   * @deprecated
   */
  public const TYPE_ALLOYDB = 'ALLOYDB';
  /**
   * Spanner product area in GCP
   */
  public const TYPE_PRODUCT_TYPE_SPANNER = 'PRODUCT_TYPE_SPANNER';
  /**
   * On premises database product.
   */
  public const TYPE_PRODUCT_TYPE_ON_PREM = 'PRODUCT_TYPE_ON_PREM';
  /**
   * On premises database product.
   *
   * @deprecated
   */
  public const TYPE_ON_PREM = 'ON_PREM';
  /**
   * Memorystore product area in GCP
   */
  public const TYPE_PRODUCT_TYPE_MEMORYSTORE = 'PRODUCT_TYPE_MEMORYSTORE';
  /**
   * Bigtable product area in GCP
   */
  public const TYPE_PRODUCT_TYPE_BIGTABLE = 'PRODUCT_TYPE_BIGTABLE';
  /**
   * Firestore product area in GCP.
   */
  public const TYPE_PRODUCT_TYPE_FIRESTORE = 'PRODUCT_TYPE_FIRESTORE';
  /**
   * Compute Engine self managed databases
   */
  public const TYPE_PRODUCT_TYPE_COMPUTE_ENGINE = 'PRODUCT_TYPE_COMPUTE_ENGINE';
  /**
   * Oracle product area in GCP
   */
  public const TYPE_PRODUCT_TYPE_ORACLE_ON_GCP = 'PRODUCT_TYPE_ORACLE_ON_GCP';
  /**
   * BigQuery product area in GCP
   */
  public const TYPE_PRODUCT_TYPE_BIGQUERY = 'PRODUCT_TYPE_BIGQUERY';
  /**
   * Other refers to rest of other product type. This is to be when product type
   * is known, but it is not present in this enum.
   */
  public const TYPE_PRODUCT_TYPE_OTHER = 'PRODUCT_TYPE_OTHER';
  /**
   * The specific engine that the underlying database is running.
   *
   * @var string
   */
  public $engine;
  /**
   * Minor version of the underlying database engine. Example values: For MySQL,
   * it could be "8.0.32", "5.7.32" etc.. For Postgres, it could be "14.3",
   * "15.3" etc..
   *
   * @var string
   */
  public $minorVersion;
  /**
   * Type of specific database product. It could be CloudSQL, AlloyDB etc..
   *
   * @var string
   */
  public $type;
  /**
   * Version of the underlying database engine. Example values: For MySQL, it
   * could be "8.0", "5.7" etc.. For Postgres, it could be "14", "15" etc..
   *
   * @var string
   */
  public $version;

  /**
   * The specific engine that the underlying database is running.
   *
   * Accepted values: ENGINE_UNSPECIFIED, ENGINE_MYSQL, MYSQL, ENGINE_POSTGRES,
   * POSTGRES, ENGINE_SQL_SERVER, SQL_SERVER, ENGINE_NATIVE, NATIVE,
   * ENGINE_CLOUD_SPANNER_WITH_POSTGRES_DIALECT,
   * ENGINE_CLOUD_SPANNER_WITH_GOOGLESQL_DIALECT, ENGINE_MEMORYSTORE_FOR_REDIS,
   * ENGINE_MEMORYSTORE_FOR_REDIS_CLUSTER, ENGINE_OTHER,
   * ENGINE_FIRESTORE_WITH_NATIVE_MODE, ENGINE_FIRESTORE_WITH_DATASTORE_MODE,
   * ENGINE_FIRESTORE_WITH_MONGODB_COMPATIBILITY_MODE, ENGINE_EXADATA_ORACLE,
   * ENGINE_ADB_SERVERLESS_ORACLE
   *
   * @param self::ENGINE_* $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return self::ENGINE_*
   */
  public function getEngine()
  {
    return $this->engine;
  }
  /**
   * Minor version of the underlying database engine. Example values: For MySQL,
   * it could be "8.0.32", "5.7.32" etc.. For Postgres, it could be "14.3",
   * "15.3" etc..
   *
   * @param string $minorVersion
   */
  public function setMinorVersion($minorVersion)
  {
    $this->minorVersion = $minorVersion;
  }
  /**
   * @return string
   */
  public function getMinorVersion()
  {
    return $this->minorVersion;
  }
  /**
   * Type of specific database product. It could be CloudSQL, AlloyDB etc..
   *
   * Accepted values: PRODUCT_TYPE_UNSPECIFIED, PRODUCT_TYPE_CLOUD_SQL,
   * CLOUD_SQL, PRODUCT_TYPE_ALLOYDB, ALLOYDB, PRODUCT_TYPE_SPANNER,
   * PRODUCT_TYPE_ON_PREM, ON_PREM, PRODUCT_TYPE_MEMORYSTORE,
   * PRODUCT_TYPE_BIGTABLE, PRODUCT_TYPE_FIRESTORE, PRODUCT_TYPE_COMPUTE_ENGINE,
   * PRODUCT_TYPE_ORACLE_ON_GCP, PRODUCT_TYPE_BIGQUERY, PRODUCT_TYPE_OTHER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Version of the underlying database engine. Example values: For MySQL, it
   * could be "8.0", "5.7" etc.. For Postgres, it could be "14", "15" etc..
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Product::class, 'Google_Service_CloudRedis_Product');
