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

namespace Google\Service\BlockchainNodeEngine\Resource;

use Google\Service\BlockchainNodeEngine\BlockchainNode;
use Google\Service\BlockchainNodeEngine\ListBlockchainNodesResponse;
use Google\Service\BlockchainNodeEngine\Operation;

/**
 * The "blockchainNodes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $blockchainnodeengineService = new Google\Service\BlockchainNodeEngine(...);
 *   $blockchainNodes = $blockchainnodeengineService->projects_locations_blockchainNodes;
 *  </code>
 */
class ProjectsLocationsBlockchainNodes extends \Google\Service\Resource
{
  /**
   * Creates a new blockchain node in a given project and location.
   * (blockchainNodes.create)
   *
   * @param string $parent Required. Value for parent.
   * @param BlockchainNode $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string blockchainNodeId Required. ID of the requesting object.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BlockchainNode $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single blockchain node. (blockchainNodes.delete)
   *
   * @param string $name Required. The fully qualified name of the blockchain node
   * to delete. e.g. `projects/my-project/locations/us-
   * central1/blockchainNodes/my-node`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details of a single blockchain node. (blockchainNodes.get)
   *
   * @param string $name Required. The fully qualified name of the blockchain node
   * to fetch. e.g. `projects/my-project/locations/us-central1/blockchainNodes/my-
   * node`.
   * @param array $optParams Optional parameters.
   * @return BlockchainNode
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BlockchainNode::class);
  }
  /**
   * Lists blockchain nodes in a given project and location.
   * (blockchainNodes.listProjectsLocationsBlockchainNodes)
   *
   * @param string $parent Required. Parent value for `ListNodesRequest`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results.
   * @opt_param string orderBy Hint for how to order the results.
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListBlockchainNodesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBlockchainNodes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBlockchainNodesResponse::class);
  }
  /**
   * Updates the parameters of a single blockchain node. (blockchainNodes.patch)
   *
   * @param string $name Output only. The fully qualified name of the blockchain
   * node. e.g. `projects/my-project/locations/us-central1/blockchainNodes/my-
   * node`.
   * @param BlockchainNode $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Blockchain node resource by the update. The
   * fields specified in the `update_mask` are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BlockchainNode $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBlockchainNodes::class, 'Google_Service_BlockchainNodeEngine_Resource_ProjectsLocationsBlockchainNodes');
