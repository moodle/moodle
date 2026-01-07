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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1DeployIndexRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1DeployedIndex;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1FindNeighborsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1FindNeighborsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1IndexEndpoint;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListIndexEndpointsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReadIndexDatapointsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReadIndexDatapointsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1UndeployIndexRequest;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "indexEndpoints" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $indexEndpoints = $aiplatformService->projects_locations_indexEndpoints;
 *  </code>
 */
class ProjectsLocationsIndexEndpoints extends \Google\Service\Resource
{
  /**
   * Creates an IndexEndpoint. (indexEndpoints.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the IndexEndpoint in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1IndexEndpoint $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1IndexEndpoint $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes an IndexEndpoint. (indexEndpoints.delete)
   *
   * @param string $name Required. The name of the IndexEndpoint resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/indexEndpoints/{index_endpoint}`
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deploys an Index into this IndexEndpoint, creating a DeployedIndex within it.
   * (indexEndpoints.deployIndex)
   *
   * @param string $indexEndpoint Required. The name of the IndexEndpoint resource
   * into which to deploy an Index. Format:
   * `projects/{project}/locations/{location}/indexEndpoints/{index_endpoint}`
   * @param GoogleCloudAiplatformV1DeployIndexRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function deployIndex($indexEndpoint, GoogleCloudAiplatformV1DeployIndexRequest $postBody, $optParams = [])
  {
    $params = ['indexEndpoint' => $indexEndpoint, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deployIndex', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Finds the nearest neighbors of each vector within the request.
   * (indexEndpoints.findNeighbors)
   *
   * @param string $indexEndpoint Required. The name of the index endpoint.
   * Format:
   * `projects/{project}/locations/{location}/indexEndpoints/{index_endpoint}`
   * @param GoogleCloudAiplatformV1FindNeighborsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1FindNeighborsResponse
   * @throws \Google\Service\Exception
   */
  public function findNeighbors($indexEndpoint, GoogleCloudAiplatformV1FindNeighborsRequest $postBody, $optParams = [])
  {
    $params = ['indexEndpoint' => $indexEndpoint, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('findNeighbors', [$params], GoogleCloudAiplatformV1FindNeighborsResponse::class);
  }
  /**
   * Gets an IndexEndpoint. (indexEndpoints.get)
   *
   * @param string $name Required. The name of the IndexEndpoint resource. Format:
   * `projects/{project}/locations/{location}/indexEndpoints/{index_endpoint}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1IndexEndpoint
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1IndexEndpoint::class);
  }
  /**
   * Lists IndexEndpoints in a Location.
   * (indexEndpoints.listProjectsLocationsIndexEndpoints)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the IndexEndpoints. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. For field names both snake_case and camelCase are supported. *
   * `index_endpoint` supports = and !=. `index_endpoint` represents the
   * IndexEndpoint ID, ie. the last segment of the IndexEndpoint's resourcename. *
   * `display_name` supports =, != and regex() (uses
   * [re2](https://github.com/google/re2/wiki/Syntax) syntax) * `labels` supports
   * general map functions that is: `labels.key=value` - key:value equality
   * `labels.key:* or labels:key - key existence A key including a space must be
   * quoted. `labels."a key"`. Some examples: * `index_endpoint="1"` *
   * `display_name="myDisplayName"` * `regex(display_name, "^A") -> The display
   * name starts with an A. * `labels.myKey="myValue"`
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token. Typically
   * obtained via ListIndexEndpointsResponse.next_page_token of the previous
   * IndexEndpointService.ListIndexEndpoints call.
   * @opt_param string readMask Optional. Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListIndexEndpointsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsIndexEndpoints($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListIndexEndpointsResponse::class);
  }
  /**
   * Update an existing DeployedIndex under an IndexEndpoint.
   * (indexEndpoints.mutateDeployedIndex)
   *
   * @param string $indexEndpoint Required. The name of the IndexEndpoint resource
   * into which to deploy an Index. Format:
   * `projects/{project}/locations/{location}/indexEndpoints/{index_endpoint}`
   * @param GoogleCloudAiplatformV1DeployedIndex $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function mutateDeployedIndex($indexEndpoint, GoogleCloudAiplatformV1DeployedIndex $postBody, $optParams = [])
  {
    $params = ['indexEndpoint' => $indexEndpoint, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('mutateDeployedIndex', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Updates an IndexEndpoint. (indexEndpoints.patch)
   *
   * @param string $name Output only. The resource name of the IndexEndpoint.
   * @param GoogleCloudAiplatformV1IndexEndpoint $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource. See google.protobuf.FieldMask.
   * @return GoogleCloudAiplatformV1IndexEndpoint
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1IndexEndpoint $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1IndexEndpoint::class);
  }
  /**
   * Reads the datapoints/vectors of the given IDs. A maximum of 1000 datapoints
   * can be retrieved in a batch. (indexEndpoints.readIndexDatapoints)
   *
   * @param string $indexEndpoint Required. The name of the index endpoint.
   * Format:
   * `projects/{project}/locations/{location}/indexEndpoints/{index_endpoint}`
   * @param GoogleCloudAiplatformV1ReadIndexDatapointsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ReadIndexDatapointsResponse
   * @throws \Google\Service\Exception
   */
  public function readIndexDatapoints($indexEndpoint, GoogleCloudAiplatformV1ReadIndexDatapointsRequest $postBody, $optParams = [])
  {
    $params = ['indexEndpoint' => $indexEndpoint, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('readIndexDatapoints', [$params], GoogleCloudAiplatformV1ReadIndexDatapointsResponse::class);
  }
  /**
   * Undeploys an Index from an IndexEndpoint, removing a DeployedIndex from it,
   * and freeing all resources it's using. (indexEndpoints.undeployIndex)
   *
   * @param string $indexEndpoint Required. The name of the IndexEndpoint resource
   * from which to undeploy an Index. Format:
   * `projects/{project}/locations/{location}/indexEndpoints/{index_endpoint}`
   * @param GoogleCloudAiplatformV1UndeployIndexRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function undeployIndex($indexEndpoint, GoogleCloudAiplatformV1UndeployIndexRequest $postBody, $optParams = [])
  {
    $params = ['indexEndpoint' => $indexEndpoint, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('undeployIndex', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsIndexEndpoints::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsIndexEndpoints');
