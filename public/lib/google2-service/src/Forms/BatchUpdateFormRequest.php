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

namespace Google\Service\Forms;

class BatchUpdateFormRequest extends \Google\Collection
{
  protected $collection_key = 'requests';
  /**
   * Whether to return an updated version of the model in the response.
   *
   * @var bool
   */
  public $includeFormInResponse;
  protected $requestsType = Request::class;
  protected $requestsDataType = 'array';
  protected $writeControlType = WriteControl::class;
  protected $writeControlDataType = '';

  /**
   * Whether to return an updated version of the model in the response.
   *
   * @param bool $includeFormInResponse
   */
  public function setIncludeFormInResponse($includeFormInResponse)
  {
    $this->includeFormInResponse = $includeFormInResponse;
  }
  /**
   * @return bool
   */
  public function getIncludeFormInResponse()
  {
    return $this->includeFormInResponse;
  }
  /**
   * Required. The update requests of this batch.
   *
   * @param Request[] $requests
   */
  public function setRequests($requests)
  {
    $this->requests = $requests;
  }
  /**
   * @return Request[]
   */
  public function getRequests()
  {
    return $this->requests;
  }
  /**
   * Provides control over how write requests are executed.
   *
   * @param WriteControl $writeControl
   */
  public function setWriteControl(WriteControl $writeControl)
  {
    $this->writeControl = $writeControl;
  }
  /**
   * @return WriteControl
   */
  public function getWriteControl()
  {
    return $this->writeControl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUpdateFormRequest::class, 'Google_Service_Forms_BatchUpdateFormRequest');
