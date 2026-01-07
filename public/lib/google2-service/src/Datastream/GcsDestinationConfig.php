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

class GcsDestinationConfig extends \Google\Model
{
  protected $avroFileFormatType = AvroFileFormat::class;
  protected $avroFileFormatDataType = '';
  /**
   * The maximum duration for which new events are added before a file is closed
   * and a new file is created. Values within the range of 15-60 seconds are
   * allowed.
   *
   * @var string
   */
  public $fileRotationInterval;
  /**
   * The maximum file size to be saved in the bucket.
   *
   * @var int
   */
  public $fileRotationMb;
  protected $jsonFileFormatType = JsonFileFormat::class;
  protected $jsonFileFormatDataType = '';
  /**
   * Path inside the Cloud Storage bucket to write data to.
   *
   * @var string
   */
  public $path;

  /**
   * AVRO file format configuration.
   *
   * @param AvroFileFormat $avroFileFormat
   */
  public function setAvroFileFormat(AvroFileFormat $avroFileFormat)
  {
    $this->avroFileFormat = $avroFileFormat;
  }
  /**
   * @return AvroFileFormat
   */
  public function getAvroFileFormat()
  {
    return $this->avroFileFormat;
  }
  /**
   * The maximum duration for which new events are added before a file is closed
   * and a new file is created. Values within the range of 15-60 seconds are
   * allowed.
   *
   * @param string $fileRotationInterval
   */
  public function setFileRotationInterval($fileRotationInterval)
  {
    $this->fileRotationInterval = $fileRotationInterval;
  }
  /**
   * @return string
   */
  public function getFileRotationInterval()
  {
    return $this->fileRotationInterval;
  }
  /**
   * The maximum file size to be saved in the bucket.
   *
   * @param int $fileRotationMb
   */
  public function setFileRotationMb($fileRotationMb)
  {
    $this->fileRotationMb = $fileRotationMb;
  }
  /**
   * @return int
   */
  public function getFileRotationMb()
  {
    return $this->fileRotationMb;
  }
  /**
   * JSON file format configuration.
   *
   * @param JsonFileFormat $jsonFileFormat
   */
  public function setJsonFileFormat(JsonFileFormat $jsonFileFormat)
  {
    $this->jsonFileFormat = $jsonFileFormat;
  }
  /**
   * @return JsonFileFormat
   */
  public function getJsonFileFormat()
  {
    return $this->jsonFileFormat;
  }
  /**
   * Path inside the Cloud Storage bucket to write data to.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcsDestinationConfig::class, 'Google_Service_Datastream_GcsDestinationConfig');
