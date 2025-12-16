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

namespace Google\Service\Dataflow;

class SendDebugCaptureRequest extends \Google\Model
{
  /**
   * Format unspecified, parsing is determined based upon page type and legacy
   * encoding. (go/protodosdonts#do-include-an-unspecified-value-in-an-enum)
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Raw HTML string.
   */
  public const DATA_FORMAT_RAW = 'RAW';
  /**
   * JSON-encoded string.
   */
  public const DATA_FORMAT_JSON = 'JSON';
  /**
   * Websafe encoded zlib-compressed string.
   */
  public const DATA_FORMAT_ZLIB = 'ZLIB';
  /**
   * Websafe encoded brotli-compressed string.
   */
  public const DATA_FORMAT_BROTLI = 'BROTLI';
  /**
   * The internal component id for which debug information is sent.
   *
   * @var string
   */
  public $componentId;
  /**
   * The encoded debug information.
   *
   * @var string
   */
  public $data;
  /**
   * Format for the data field above (id=5).
   *
   * @var string
   */
  public $dataFormat;
  /**
   * The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) that
   * contains the job specified by job_id.
   *
   * @var string
   */
  public $location;
  /**
   * The worker id, i.e., VM hostname.
   *
   * @var string
   */
  public $workerId;

  /**
   * The internal component id for which debug information is sent.
   *
   * @param string $componentId
   */
  public function setComponentId($componentId)
  {
    $this->componentId = $componentId;
  }
  /**
   * @return string
   */
  public function getComponentId()
  {
    return $this->componentId;
  }
  /**
   * The encoded debug information.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Format for the data field above (id=5).
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, RAW, JSON, ZLIB, BROTLI
   *
   * @param self::DATA_FORMAT_* $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return self::DATA_FORMAT_*
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) that
   * contains the job specified by job_id.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The worker id, i.e., VM hostname.
   *
   * @param string $workerId
   */
  public function setWorkerId($workerId)
  {
    $this->workerId = $workerId;
  }
  /**
   * @return string
   */
  public function getWorkerId()
  {
    return $this->workerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SendDebugCaptureRequest::class, 'Google_Service_Dataflow_SendDebugCaptureRequest');
