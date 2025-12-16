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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1MetadataJobEvent extends \Google\Model
{
  protected $importResultType = GoogleCloudDataplexV1MetadataJobEventImportResult::class;
  protected $importResultDataType = '';
  /**
   * @var string
   */
  public $message;
  /**
   * @var string
   */
  public $resource;

  /**
   * @param GoogleCloudDataplexV1MetadataJobEventImportResult
   */
  public function setImportResult(GoogleCloudDataplexV1MetadataJobEventImportResult $importResult)
  {
    $this->importResult = $importResult;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJobEventImportResult
   */
  public function getImportResult()
  {
    return $this->importResult;
  }
  /**
   * @param string
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * @param string
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1MetadataJobEvent::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1MetadataJobEvent');
