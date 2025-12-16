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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1AsyncQueryResultView extends \Google\Collection
{
  protected $collection_key = 'rows';
  /**
   * Error code when there is a failure.
   *
   * @var int
   */
  public $code;
  /**
   * Error message when there is a failure.
   *
   * @var string
   */
  public $error;
  protected $metadataType = GoogleCloudApigeeV1QueryMetadata::class;
  protected $metadataDataType = '';
  /**
   * Rows of query result. Each row is a JSON object. Example:
   * {sum(message_count): 1, developer_app: "(not set)",…}
   *
   * @var array[]
   */
  public $rows;
  /**
   * State of retrieving ResultView.
   *
   * @var string
   */
  public $state;

  /**
   * Error code when there is a failure.
   *
   * @param int $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return int
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Error message when there is a failure.
   *
   * @param string $error
   */
  public function setError($error)
  {
    $this->error = $error;
  }
  /**
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Metadata contains information like metrics, dimenstions etc of the
   * AsyncQuery.
   *
   * @param GoogleCloudApigeeV1QueryMetadata $metadata
   */
  public function setMetadata(GoogleCloudApigeeV1QueryMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudApigeeV1QueryMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Rows of query result. Each row is a JSON object. Example:
   * {sum(message_count): 1, developer_app: "(not set)",…}
   *
   * @param array[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return array[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * State of retrieving ResultView.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1AsyncQueryResultView::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AsyncQueryResultView');
