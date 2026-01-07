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

namespace Google\Service\BigtableAdmin;

class ProtoSchema extends \Google\Model
{
  /**
   * Required. Contains a protobuf-serialized [google.protobuf.FileDescriptorSet
   * ](https://github.com/protocolbuffers/protobuf/blob/main/src/google/protobuf
   * /descriptor.proto), which could include multiple proto files. To generate
   * it, [install](https://grpc.io/docs/protoc-installation/) and run `protoc`
   * with `--include_imports` and `--descriptor_set_out`. For example, to
   * generate for moon/shot/app.proto, run ``` $protoc --proto_path=/app_path
   * --proto_path=/lib_path \ --include_imports \
   * --descriptor_set_out=descriptors.pb \ moon/shot/app.proto ``` For more
   * details, see protobuffer [self
   * description](https://developers.google.com/protocol-
   * buffers/docs/techniques#self-description).
   *
   * @var string
   */
  public $protoDescriptors;

  /**
   * Required. Contains a protobuf-serialized [google.protobuf.FileDescriptorSet
   * ](https://github.com/protocolbuffers/protobuf/blob/main/src/google/protobuf
   * /descriptor.proto), which could include multiple proto files. To generate
   * it, [install](https://grpc.io/docs/protoc-installation/) and run `protoc`
   * with `--include_imports` and `--descriptor_set_out`. For example, to
   * generate for moon/shot/app.proto, run ``` $protoc --proto_path=/app_path
   * --proto_path=/lib_path \ --include_imports \
   * --descriptor_set_out=descriptors.pb \ moon/shot/app.proto ``` For more
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
class_alias(ProtoSchema::class, 'Google_Service_BigtableAdmin_ProtoSchema');
