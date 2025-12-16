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

class UpdateDatabaseDdlRequest extends \Google\Collection
{
  protected $collection_key = 'statements';
  /**
   * If empty, the new update request is assigned an automatically-generated
   * operation ID. Otherwise, `operation_id` is used to construct the name of
   * the resulting Operation. Specifying an explicit operation ID simplifies
   * determining whether the statements were executed in the event that the
   * UpdateDatabaseDdl call is replayed, or the return value is otherwise lost:
   * the database and `operation_id` fields can be combined to form the `name`
   * of the resulting longrunning.Operation: `/operations/`. `operation_id`
   * should be unique within the database, and must be a valid identifier:
   * `a-z*`. Note that automatically-generated operation IDs always begin with
   * an underscore. If the named operation already exists, UpdateDatabaseDdl
   * returns `ALREADY_EXISTS`.
   *
   * @var string
   */
  public $operationId;
  /**
   * Optional. Proto descriptors used by CREATE/ALTER PROTO BUNDLE statements.
   * Contains a protobuf-serialized [google.protobuf.FileDescriptorSet](https://
   * github.com/protocolbuffers/protobuf/blob/main/src/google/protobuf/descripto
   * r.proto). To generate it, [install](https://grpc.io/docs/protoc-
   * installation/) and run `protoc` with --include_imports and
   * --descriptor_set_out. For example, to generate for moon/shot/app.proto, run
   * ``` $protoc --proto_path=/app_path --proto_path=/lib_path \
   * --include_imports \ --descriptor_set_out=descriptors.data \
   * moon/shot/app.proto ``` For more details, see protobuffer [self
   * description](https://developers.google.com/protocol-
   * buffers/docs/techniques#self-description).
   *
   * @var string
   */
  public $protoDescriptors;
  /**
   * Required. DDL statements to be applied to the database.
   *
   * @var string[]
   */
  public $statements;

  /**
   * If empty, the new update request is assigned an automatically-generated
   * operation ID. Otherwise, `operation_id` is used to construct the name of
   * the resulting Operation. Specifying an explicit operation ID simplifies
   * determining whether the statements were executed in the event that the
   * UpdateDatabaseDdl call is replayed, or the return value is otherwise lost:
   * the database and `operation_id` fields can be combined to form the `name`
   * of the resulting longrunning.Operation: `/operations/`. `operation_id`
   * should be unique within the database, and must be a valid identifier:
   * `a-z*`. Note that automatically-generated operation IDs always begin with
   * an underscore. If the named operation already exists, UpdateDatabaseDdl
   * returns `ALREADY_EXISTS`.
   *
   * @param string $operationId
   */
  public function setOperationId($operationId)
  {
    $this->operationId = $operationId;
  }
  /**
   * @return string
   */
  public function getOperationId()
  {
    return $this->operationId;
  }
  /**
   * Optional. Proto descriptors used by CREATE/ALTER PROTO BUNDLE statements.
   * Contains a protobuf-serialized [google.protobuf.FileDescriptorSet](https://
   * github.com/protocolbuffers/protobuf/blob/main/src/google/protobuf/descripto
   * r.proto). To generate it, [install](https://grpc.io/docs/protoc-
   * installation/) and run `protoc` with --include_imports and
   * --descriptor_set_out. For example, to generate for moon/shot/app.proto, run
   * ``` $protoc --proto_path=/app_path --proto_path=/lib_path \
   * --include_imports \ --descriptor_set_out=descriptors.data \
   * moon/shot/app.proto ``` For more details, see protobuffer [self
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
  /**
   * Required. DDL statements to be applied to the database.
   *
   * @param string[] $statements
   */
  public function setStatements($statements)
  {
    $this->statements = $statements;
  }
  /**
   * @return string[]
   */
  public function getStatements()
  {
    return $this->statements;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateDatabaseDdlRequest::class, 'Google_Service_Spanner_UpdateDatabaseDdlRequest');
