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

class ExternalRuntimeOptions extends \Google\Model
{
  /**
   * Optional. Amount of CPU provisioned for a Python UDF container instance.
   * For more information, see [Configure container limits for Python
   * UDFs](https://cloud.google.com/bigquery/docs/user-defined-functions-
   * python#configure-container-limits)
   *
   * @var 
   */
  public $containerCpu;
  /**
   * Optional. Amount of memory provisioned for a Python UDF container instance.
   * Format: {number}{unit} where unit is one of "M", "G", "Mi" and "Gi" (e.g.
   * 1G, 512Mi). If not specified, the default value is 512Mi. For more
   * information, see [Configure container limits for Python
   * UDFs](https://cloud.google.com/bigquery/docs/user-defined-functions-
   * python#configure-container-limits)
   *
   * @var string
   */
  public $containerMemory;
  /**
   * Optional. Maximum number of rows in each batch sent to the external
   * runtime. If absent or if 0, BigQuery dynamically decides the number of rows
   * in a batch.
   *
   * @var string
   */
  public $maxBatchingRows;
  /**
   * Optional. Fully qualified name of the connection whose service account will
   * be used to execute the code in the container. Format: ```"projects/{project
   * _id}/locations/{location_id}/connections/{connection_id}"```
   *
   * @var string
   */
  public $runtimeConnection;
  /**
   * Optional. Language runtime version. Example: `python-3.11`.
   *
   * @var string
   */
  public $runtimeVersion;

  public function setContainerCpu($containerCpu)
  {
    $this->containerCpu = $containerCpu;
  }
  public function getContainerCpu()
  {
    return $this->containerCpu;
  }
  /**
   * Optional. Amount of memory provisioned for a Python UDF container instance.
   * Format: {number}{unit} where unit is one of "M", "G", "Mi" and "Gi" (e.g.
   * 1G, 512Mi). If not specified, the default value is 512Mi. For more
   * information, see [Configure container limits for Python
   * UDFs](https://cloud.google.com/bigquery/docs/user-defined-functions-
   * python#configure-container-limits)
   *
   * @param string $containerMemory
   */
  public function setContainerMemory($containerMemory)
  {
    $this->containerMemory = $containerMemory;
  }
  /**
   * @return string
   */
  public function getContainerMemory()
  {
    return $this->containerMemory;
  }
  /**
   * Optional. Maximum number of rows in each batch sent to the external
   * runtime. If absent or if 0, BigQuery dynamically decides the number of rows
   * in a batch.
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
   * Optional. Fully qualified name of the connection whose service account will
   * be used to execute the code in the container. Format: ```"projects/{project
   * _id}/locations/{location_id}/connections/{connection_id}"```
   *
   * @param string $runtimeConnection
   */
  public function setRuntimeConnection($runtimeConnection)
  {
    $this->runtimeConnection = $runtimeConnection;
  }
  /**
   * @return string
   */
  public function getRuntimeConnection()
  {
    return $this->runtimeConnection;
  }
  /**
   * Optional. Language runtime version. Example: `python-3.11`.
   *
   * @param string $runtimeVersion
   */
  public function setRuntimeVersion($runtimeVersion)
  {
    $this->runtimeVersion = $runtimeVersion;
  }
  /**
   * @return string
   */
  public function getRuntimeVersion()
  {
    return $this->runtimeVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalRuntimeOptions::class, 'Google_Service_Bigquery_ExternalRuntimeOptions');
