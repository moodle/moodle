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

namespace Google\Service\Eventarc;

class GoogleCloudEventarcV1PipelineMessagePayloadFormat extends \Google\Model
{
  protected $avroType = GoogleCloudEventarcV1PipelineMessagePayloadFormatAvroFormat::class;
  protected $avroDataType = '';
  protected $jsonType = GoogleCloudEventarcV1PipelineMessagePayloadFormatJsonFormat::class;
  protected $jsonDataType = '';
  protected $protobufType = GoogleCloudEventarcV1PipelineMessagePayloadFormatProtobufFormat::class;
  protected $protobufDataType = '';

  /**
   * Optional. Avro format.
   *
   * @param GoogleCloudEventarcV1PipelineMessagePayloadFormatAvroFormat $avro
   */
  public function setAvro(GoogleCloudEventarcV1PipelineMessagePayloadFormatAvroFormat $avro)
  {
    $this->avro = $avro;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineMessagePayloadFormatAvroFormat
   */
  public function getAvro()
  {
    return $this->avro;
  }
  /**
   * Optional. JSON format.
   *
   * @param GoogleCloudEventarcV1PipelineMessagePayloadFormatJsonFormat $json
   */
  public function setJson(GoogleCloudEventarcV1PipelineMessagePayloadFormatJsonFormat $json)
  {
    $this->json = $json;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineMessagePayloadFormatJsonFormat
   */
  public function getJson()
  {
    return $this->json;
  }
  /**
   * Optional. Protobuf format.
   *
   * @param GoogleCloudEventarcV1PipelineMessagePayloadFormatProtobufFormat $protobuf
   */
  public function setProtobuf(GoogleCloudEventarcV1PipelineMessagePayloadFormatProtobufFormat $protobuf)
  {
    $this->protobuf = $protobuf;
  }
  /**
   * @return GoogleCloudEventarcV1PipelineMessagePayloadFormatProtobufFormat
   */
  public function getProtobuf()
  {
    return $this->protobuf;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudEventarcV1PipelineMessagePayloadFormat::class, 'Google_Service_Eventarc_GoogleCloudEventarcV1PipelineMessagePayloadFormat');
