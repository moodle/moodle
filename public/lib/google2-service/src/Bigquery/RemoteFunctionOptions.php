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

namespace Google\Service\Bigquery;

class RemoteFunctionOptions extends \Google\Model
{
  /**
   * Fully qualified name of the user-provided connection object which holds the
   * authentication information to send requests to the remote service. Format:
   * ```"projects/{projectId}/locations/{locationId}/connections/{connectionId}"
   * ```
   *
   * @var string
   */
  public $connection;
  /**
   * Endpoint of the user-provided remote service, e.g. ```https://us-
   * east1-my_gcf_project.cloudfunctions.net/remote_add```
   *
   * @var string
   */
  public $endpoint;
  /**
   * Max number of rows in each batch sent to the remote service. If absent or
   * if 0, BigQuery dynamically decides the number of rows in a batch.
   *
   * @var string
   */
  public $maxBatchingRows;
  /**
   * User-defined context as a set of key/value pairs, which will be sent as
   * function invocation context together with batched arguments in the requests
   * to the remote service. The total number of bytes of keys and values must be
   * less than 8KB.
   *
   * @var string[]
   */
  public $userDefinedContext;

  /**
   * Fully qualified name of the user-provided connection object which holds the
   * authentication information to send requests to the remote service. Format:
   * ```"projects/{projectId}/locations/{locationId}/connections/{connectionId}"
   * ```
   *
   * @param string $connection
   */
  public function setConnection($connection)
  {
    $this->connection = $connection;
  }
  /**
   * @return string
   */
  public function getConnection()
  {
    return $this->connection;
  }
  /**
   * Endpoint of the user-provided remote service, e.g. ```https://us-
   * east1-my_gcf_project.cloudfunctions.net/remote_add```
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Max number of rows in each batch sent to the remote service. If absent or
   * if 0, BigQuery dynamically decides the number of rows in a batch.
   *
   * @param string $maxBatchingRows
   */
  public function setMaxBatchingRows($maxBatchingRows)
  {
    $this->maxBatchingRows = $maxBatchingRows;
  }
  /**
   * @return string
   */
  public function getMaxBatchingRows()
  {
    return $this->maxBatchingRows;
  }
  /**
   * User-defined context as a set of key/value pairs, which will be sent as
   * function invocation context together with batched arguments in the requests
   * to the remote service. The total number of bytes of keys and values must be
   * less than 8KB.
   *
   * @param string[] $userDefinedContext
   */
  public function setUserDefinedContext($userDefinedContext)
  {
    $this->userDefinedContext = $userDefinedContext;
  }
  /**
   * @return string[]
   */
  public function getUserDefinedContext()
  {
    return $this->userDefinedContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoteFunctionOptions::class, 'Google_Service_Bigquery_RemoteFunctionOptions');
