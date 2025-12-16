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

namespace Google\Service\Spanner;

class CreateDatabaseRequest extends \Google\Collection
{
  /**
   * Default value. This value will create a database with the
   * GOOGLE_STANDARD_SQL dialect.
   */
  public const DATABASE_DIALECT_DATABASE_DIALECT_UNSPECIFIED = 'DATABASE_DIALECT_UNSPECIFIED';
  /**
   * GoogleSQL supported SQL.
   */
  public const DATABASE_DIALECT_GOOGLE_STANDARD_SQL = 'GOOGLE_STANDARD_SQL';
  /**
   * PostgreSQL supported SQL.
   */
  public const DATABASE_DIALECT_POSTGRESQL = 'POSTGRESQL';
  protected $collection_key = 'extraStatements';
  /**
   * Required. A `CREATE DATABASE` statement, which specifies the ID of the new
   * database. The database ID must conform to the regular expression
   * `a-z*[a-z0-9]` and be between 2 and 30 characters in length. If the
   * database ID is a reserved word or if it contains a hyphen, the database ID
   * must be enclosed in backticks (`` ` ``).
   *
   * @var string
   */
  public $createStatement;
  /**
   * Optional. The dialect of the Cloud Spanner Database.
   *
   * @var string
   */
  public $databaseDialect;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Optional. A list of DDL statements to run inside the newly created
   * database. Statements can create tables, indexes, etc. These statements
   * execute atomically with the creation of the database: if there is an error
   * in any statement, the database is not created.
   *
   * @var string[]
   */
  public $extraStatements;
  /**
   * Optional. Proto descriptors used by `CREATE/ALTER PROTO BUNDLE` statements
   * in 'extra_statements'. Contains a protobuf-serialized [`google.protobuf.Fil
   * eDescriptorSet`](https://github.com/protocolbuffers/protobuf/blob/main/src/
   * google/protobuf/descriptor.proto) descriptor set. To generate it,
   * [install](https://grpc.io/docs/protoc-installation/) and run `protoc` with
   * --include_imports and --descriptor_set_out. For example, to generate for
   * moon/shot/app.proto, run ``` $protoc --proto_path=/app_path
   * --proto_path=/lib_path \ --include_imports \
   * --descriptor_set_out=descriptors.data \ moon/shot/app.proto ``` For more
   * details, see protobuffer [self
   * description](https://developers.google.com/protocol-
   * buffers/docs/techniques#self-description).
   *
   * @var string
   */
  public $protoDescriptors;

  /**
   * Required. A `CREATE DATABASE` statement, which specifies the ID of the new
   * database. The database ID must conform to the regular expression
   * `a-z*[a-z0-9]` and be between 2 and 30 characters in length. If the
   * database ID is a reserved word or if it contains a hyphen, the database ID
   * must be enclosed in backticks (`` ` ``).
   *
   * @param string $createStatement
   */
  public function setCreateStatement($createStatement)
  {
    $this->createStatement = $createStatement;
  }
  /**
   * @return string
   */
  public function getCreateStatement()
  {
    return $this->createStatement;
  }
  /**
   * Optional. The dialect of the Cloud Spanner Database.
   *
   * Accepted values: DATABASE_DIALECT_UNSPECIFIED, GOOGLE_STANDARD_SQL,
   * POSTGRESQL
   *
   * @param self::DATABASE_DIALECT_* $databaseDialect
   */
  public function setDatabaseDialect($databaseDialect)
  {
    $this->databaseDialect = $databaseDialect;
  }
  /**
   * @return self::DATABASE_DIALECT_*
   */
  public function getDatabaseDialect()
  {
    return $this->databaseDialect;
  }
  /**
   * Optional. The encryption configuration for the database. If this field is
   * not specified, Cloud Spanner will encrypt/decrypt all data at rest using
   * Google default encryption.
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Optional. A list of DDL statements to run inside the newly created
   * database. Statements can create tables, indexes, etc. These statements
   * execute atomically with the creation of the database: if there is an error
   * in any statement, the database is not created.
   *
   * @param string[] $extraStatements
   */
  public function setExtraStatements($extraStatements)
  {
    $this->extraStatements = $extraStatements;
  }
  /**
   * @return string[]
   */
  public function getExtraStatements()
  {
    return $this->extraStatements;
  }
  /**
   * Optional. Proto descriptors used by `CREATE/ALTER PROTO BUNDLE` statements
   * in 'extra_statements'. Contains a protobuf-serialized [`google.protobuf.Fil
   * eDescriptorSet`](https://github.com/protocolbuffers/protobuf/blob/main/src/
   * google/protobuf/descriptor.proto) descriptor set. To generate it,
   * [install](https://grpc.io/docs/protoc-installation/) and run `protoc` with
   * --include_imports and --descriptor_set_out. For example, to generate for
   * moon/shot/app.proto, run ``` $protoc --proto_path=/app_path
   * --proto_path=/lib_path \ --include_imports \
   * --descriptor_set_out=descriptors.data \ moon/shot/app.proto ``` For more
   * details, see protobuffer [self
   * description](https://developers.google.com/protocol-
   * buffers/docs/techniques#self-description).
   *
   * @param string $protoDescriptors
   */
  public function setProtoDescriptors($protoDescriptors)
  {
    $this->protoDescriptors = $protoDescriptors;
  }
  /**
   * @return string
   */
  public function getProtoDescriptors()
  {
    return $this->protoDescriptors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateDatabaseRequest::class, 'Google_Service_Spanner_CreateDatabaseRequest');
