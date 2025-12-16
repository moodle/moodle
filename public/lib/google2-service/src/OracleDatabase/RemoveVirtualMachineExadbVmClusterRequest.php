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

namespace Google\Service\OracleDatabase;

class RemoveVirtualMachineExadbVmClusterRequest extends \Google\Collection
{
  protected $collection_key = 'hostnames';
  /**
   * Required. The list of host names of db nodes to be removed from the
   * ExadbVmCluster.
   *
   * @var string[]
   */
  public $hostnames;
  /**
   * Optional. An optional ID to identify the request. This value is used to
   * identify duplicate requests. If you make a request with the same request ID
   * and the original request is still in progress or completed, the server
   * ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with
   * the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;

  /**
   * Required. The list of host names of db nodes to be removed from the
   * ExadbVmCluster.
   *
   * @param string[] $hostnames
   */
  public function setHostnames($hostnames)
  {
    $this->hostnames = $hostnames;
  }
  /**
   * @return string[]
   */
  public function getHostnames()
  {
    return $this->hostnames;
  }
  /**
   * Optional. An optional ID to identify the request. This value is used to
   * identify duplicate requests. If you make a request with the same request ID
   * and the original request is still in progress or completed, the server
   * ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with
   * the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoveVirtualMachineExadbVmClusterRequest::class, 'Google_Service_OracleDatabase_RemoveVirtualMachineExadbVmClusterRequest');
