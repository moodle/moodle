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

namespace Google\Service\Bigquery\Resource;

use Google\Service\Bigquery\Dataset;
use Google\Service\Bigquery\DatasetList;
use Google\Service\Bigquery\UndeleteDatasetRequest;

/**
 * The "datasets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigqueryService = new Google\Service\Bigquery(...);
 *   $datasets = $bigqueryService->datasets;
 *  </code>
 */
class Datasets extends \Google\Service\Resource
{
  /**
   * Deletes the dataset specified by the datasetId value. Before you can delete a
   * dataset, you must delete all its tables, either manually or by specifying
   * deleteContents. Immediately after deletion, you can create another dataset
   * with the same name. (datasets.delete)
   *
   * @param string $projectId Required. Project ID of the dataset being deleted
   * @param string $datasetId Required. Dataset ID of dataset being deleted
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool deleteContents If True, delete all the tables in the dataset.
   * If False and the dataset contains tables, the request will fail. Default is
   * False
   * @throws \Google\Service\Exception
   */
  public function delete($projectId, $datasetId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Returns the dataset specified by datasetID. (datasets.get)
   *
   * @param string $projectId Required. Project ID of the requested dataset
   * @param string $datasetId Required. Dataset ID of the requested dataset
   * @param array $optParams Optional parameters.
   *
   * @opt_param int accessPolicyVersion Optional. The version of the access policy
   * schema to fetch. Valid values are 0, 1, and 3. Requests specifying an invalid
   * value will be rejected. Requests for conditional access policy binding in
   * datasets must specify version 3. Dataset with no conditional role bindings in
   * access policy may specify any valid value or leave the field unset. This
   * field will be mapped to [IAM Policy version]
   * (https://cloud.google.com/iam/docs/policies#versions) and will be used to
   * fetch policy from IAM. If unset or if 0 or 1 value is used for dataset with
   * conditional bindings, access entry with condition will have role string
   * appended by 'withcond' string followed by a hash value. For example : {
   * "access": [ { "role":
   * "roles/bigquery.dataViewer_with_conditionalbinding_7a34awqsda",
   * "userByEmail": "user@example.com", } ] } Please refer
   * https://cloud.google.com/iam/docs/troubleshooting-withcond for more details.
   * @opt_param string datasetView Optional. Specifies the view that determines
   * which dataset information is returned. By default, metadata and ACL
   * information are returned.
   * @return Dataset
   * @throws \Google\Service\Exception
   */
  public function get($projectId, $datasetId, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Dataset::class);
  }
  /**
   * Creates a new empty dataset. (datasets.insert)
   *
   * @param string $projectId Required. Project ID of the new dataset
   * @param Dataset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param int accessPolicyVersion Optional. The version of the provided
   * access policy schema. Valid values are 0, 1, and 3. Requests specifying an
   * invalid value will be rejected. This version refers to the schema version of
   * the access policy and not the version of access policy. This field's value
   * can be equal or more than the access policy schema provided in the request.
   * For example, * Requests with conditional access policy binding in datasets
   * must specify version 3. * But dataset with no conditional role bindings in
   * access policy may specify any valid value or leave the field unset. If unset
   * or if 0 or 1 value is used for dataset with conditional bindings, request
   * will be rejected. This field will be mapped to IAM Policy version
   * (https://cloud.google.com/iam/docs/policies#versions) and will be used to set
   * policy in IAM.
   * @return Dataset
   * @throws \Google\Service\Exception
   */
  public function insert($projectId, Dataset $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Dataset::class);
  }
  /**
   * Lists all datasets in the specified project to which the user has been
   * granted the READER dataset role. (datasets.listDatasets)
   *
   * @param string $projectId Required. Project ID of the datasets to be listed
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool all Whether to list all datasets, including hidden ones
   * @opt_param string filter An expression for filtering the results of the
   * request by label. The syntax is `labels.[:]`. Multiple filters can be AND-ed
   * together by connecting with a space. Example: `labels.department:receiving
   * labels.active`. See [Filtering datasets using
   * labels](https://cloud.google.com/bigquery/docs/filtering-
   * labels#filtering_datasets_using_labels) for details.
   * @opt_param string maxResults The maximum number of results to return in a
   * single response page. Leverage the page tokens to iterate through the entire
   * collection.
   * @opt_param string pageToken Page token, returned by a previous call, to
   * request the next page of results
   * @return DatasetList
   * @throws \Google\Service\Exception
   */
  public function listDatasets($projectId, $optParams = [])
  {
    $params = ['projectId' => $projectId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], DatasetList::class);
  }
  /**
   * Updates information in an existing dataset. The update method replaces the
   * entire dataset resource, whereas the patch method only replaces fields that
   * are provided in the submitted dataset resource. This method supports RFC5789
   * patch semantics. (datasets.patch)
   *
   * @param string $projectId Required. Project ID of the dataset being updated
   * @param string $datasetId Required. Dataset ID of the dataset being updated
   * @param Dataset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param int accessPolicyVersion Optional. The version of the provided
   * access policy schema. Valid values are 0, 1, and 3. Requests specifying an
   * invalid value will be rejected. This version refers to the schema version of
   * the access policy and not the version of access policy. This field's value
   * can be equal or more than the access policy schema provided in the request.
   * For example, * Operations updating conditional access policy binding in
   * datasets must specify version 3. Some of the operations are : - Adding a new
   * access policy entry with condition. - Removing an access policy entry with
   * condition. - Updating an access policy entry with condition. * But dataset
   * with no conditional role bindings in access policy may specify any valid
   * value or leave the field unset. If unset or if 0 or 1 value is used for
   * dataset with conditional bindings, request will be rejected. This field will
   * be mapped to IAM Policy version
   * (https://cloud.google.com/iam/docs/policies#versions) and will be used to set
   * policy in IAM.
   * @opt_param string updateMode Optional. Specifies the fields of dataset that
   * update/patch operation is targeting By default, both metadata and ACL fields
   * are updated.
   * @return Dataset
   * @throws \Google\Service\Exception
   */
  public function patch($projectId, $datasetId, Dataset $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Dataset::class);
  }
  /**
   * Undeletes a dataset which is within time travel window based on datasetId. If
   * a time is specified, the dataset version deleted at that time is undeleted,
   * else the last live version is undeleted. (datasets.undelete)
   *
   * @param string $projectId Required. Project ID of the dataset to be undeleted
   * @param string $datasetId Required. Dataset ID of dataset being deleted
   * @param UndeleteDatasetRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Dataset
   * @throws \Google\Service\Exception
   */
  public function undelete($projectId, $datasetId, UndeleteDatasetRequest $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('undelete', [$params], Dataset::class);
  }
  /**
   * Updates information in an existing dataset. The update method replaces the
   * entire dataset resource, whereas the patch method only replaces fields that
   * are provided in the submitted dataset resource. (datasets.update)
   *
   * @param string $projectId Required. Project ID of the dataset being updated
   * @param string $datasetId Required. Dataset ID of the dataset being updated
   * @param Dataset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param int accessPolicyVersion Optional. The version of the provided
   * access policy schema. Valid values are 0, 1, and 3. Requests specifying an
   * invalid value will be rejected. This version refers to the schema version of
   * the access policy and not the version of access policy. This field's value
   * can be equal or more than the access policy schema provided in the request.
   * For example, * Operations updating conditional access policy binding in
   * datasets must specify version 3. Some of the operations are : - Adding a new
   * access policy entry with condition. - Removing an access policy entry with
   * condition. - Updating an access policy entry with condition. * But dataset
   * with no conditional role bindings in access policy may specify any valid
   * value or leave the field unset. If unset or if 0 or 1 value is used for
   * dataset with conditional bindings, request will be rejected. This field will
   * be mapped to IAM Policy version
   * (https://cloud.google.com/iam/docs/policies#versions) and will be used to set
   * policy in IAM.
   * @opt_param string updateMode Optional. Specifies the fields of dataset that
   * update/patch operation is targeting By default, both metadata and ACL fields
   * are updated.
   * @return Dataset
   * @throws \Google\Service\Exception
   */
  public function update($projectId, $datasetId, Dataset $postBody, $optParams = [])
  {
    $params = ['projectId' => $projectId, 'datasetId' => $datasetId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Dataset::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Datasets::class, 'Google_Service_Bigquery_Resource_Datasets');
