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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1DeleteFeatureValuesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1EntityType;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ExportFeatureValuesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ImportFeatureValuesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListEntityTypesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReadFeatureValuesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReadFeatureValuesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1StreamingReadFeatureValuesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1WriteFeatureValuesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1WriteFeatureValuesResponse;
use Google\Service\Aiplatform\GoogleIamV1Policy;
use Google\Service\Aiplatform\GoogleIamV1SetIamPolicyRequest;
use Google\Service\Aiplatform\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "entityTypes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $entityTypes = $aiplatformService->projects_locations_featurestores_entityTypes;
 *  </code>
 */
class ProjectsLocationsFeaturestoresEntityTypes extends \Google\Service\Resource
{
  /**
   * Creates a new EntityType in a given Featurestore. (entityTypes.create)
   *
   * @param string $parent Required. The resource name of the Featurestore to
   * create EntityTypes. Format:
   * `projects/{project}/locations/{location}/featurestores/{featurestore}`
   * @param GoogleCloudAiplatformV1EntityType $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string entityTypeId Required. The ID to use for the EntityType,
   * which will become the final component of the EntityType's resource name. This
   * value may be up to 60 characters, and valid characters are `[a-z0-9_]`. The
   * first character cannot be a number. The value must be unique within a
   * featurestore.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1EntityType $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a single EntityType. The EntityType must not have any Features or
   * `force` must be set to true for the request to succeed. (entityTypes.delete)
   *
   * @param string $name Required. The name of the EntityType to be deleted.
   * Format: `projects/{project}/locations/{location}/featurestores/{featurestore}
   * /entityTypes/{entity_type}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force If set to true, any Features for this EntityType will
   * also be deleted. (Otherwise, the request will only work if the EntityType has
   * no Features.)
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
   * Delete Feature values from Featurestore. The progress of the deletion is
   * tracked by the returned operation. The deleted feature values are guaranteed
   * to be invisible to subsequent read operations after the operation is marked
   * as successfully done. If a delete feature values operation fails, the feature
   * values returned from reads and exports may be inconsistent. If consistency is
   * required, the caller must retry the same delete request again and wait till
   * the new operation returned is marked as successfully done.
   * (entityTypes.deleteFeatureValues)
   *
   * @param string $entityType Required. The resource name of the EntityType
   * grouping the Features for which values are being deleted from. Format: `proje
   * cts/{project}/locations/{location}/featurestores/{featurestore}/entityTypes/{
   * entityType}`
   * @param GoogleCloudAiplatformV1DeleteFeatureValuesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function deleteFeatureValues($entityType, GoogleCloudAiplatformV1DeleteFeatureValuesRequest $postBody, $optParams = [])
  {
    $params = ['entityType' => $entityType, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deleteFeatureValues', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Exports Feature values from all the entities of a target EntityType.
   * (entityTypes.exportFeatureValues)
   *
   * @param string $entityType Required. The resource name of the EntityType from
   * which to export Feature values. Format: `projects/{project}/locations/{locati
   * on}/featurestores/{featurestore}/entityTypes/{entity_type}`
   * @param GoogleCloudAiplatformV1ExportFeatureValuesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function exportFeatureValues($entityType, GoogleCloudAiplatformV1ExportFeatureValuesRequest $postBody, $optParams = [])
  {
    $params = ['entityType' => $entityType, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportFeatureValues', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets details of a single EntityType. (entityTypes.get)
   *
   * @param string $name Required. The name of the EntityType resource. Format: `p
   * rojects/{project}/locations/{location}/featurestores/{featurestore}/entityTyp
   * es/{entity_type}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1EntityType
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1EntityType::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (entityTypes.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Imports Feature values into the Featurestore from a source storage. The
   * progress of the import is tracked by the returned operation. The imported
   * features are guaranteed to be visible to subsequent read operations after the
   * operation is marked as successfully done. If an import operation fails, the
   * Feature values returned from reads and exports may be inconsistent. If
   * consistency is required, the caller must retry the same import request again
   * and wait till the new operation returned is marked as successfully done.
   * There are also scenarios where the caller can cause inconsistency. - Source
   * data for import contains multiple distinct Feature values for the same entity
   * ID and timestamp. - Source is modified during an import. This includes
   * adding, updating, or removing source data and/or metadata. Examples of
   * updating metadata include but are not limited to changing storage location,
   * storage class, or retention policy. - Online serving cluster is under-
   * provisioned. (entityTypes.importFeatureValues)
   *
   * @param string $entityType Required. The resource name of the EntityType
   * grouping the Features for which values are being imported. Format: `projects/
   * {project}/locations/{location}/featurestores/{featurestore}/entityTypes/{enti
   * tyType}`
   * @param GoogleCloudAiplatformV1ImportFeatureValuesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function importFeatureValues($entityType, GoogleCloudAiplatformV1ImportFeatureValuesRequest $postBody, $optParams = [])
  {
    $params = ['entityType' => $entityType, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('importFeatureValues', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Lists EntityTypes in a given Featurestore.
   * (entityTypes.listProjectsLocationsFeaturestoresEntityTypes)
   *
   * @param string $parent Required. The resource name of the Featurestore to list
   * EntityTypes. Format:
   * `projects/{project}/locations/{location}/featurestores/{featurestore}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the EntityTypes that match the filter
   * expression. The following filters are supported: * `create_time`: Supports
   * `=`, `!=`, `<`, `>`, `>=`, and `<=` comparisons. Values must be in RFC 3339
   * format. * `update_time`: Supports `=`, `!=`, `<`, `>`, `>=`, and `<=`
   * comparisons. Values must be in RFC 3339 format. * `labels`: Supports key-
   * value equality as well as key presence. Examples: * `create_time >
   * \"2020-01-31T15:30:00.000000Z\" OR update_time >
   * \"2020-01-31T15:30:00.000000Z\"` --> EntityTypes created or updated after
   * 2020-01-31T15:30:00.000000Z. * `labels.active = yes AND labels.env = prod`
   * --> EntityTypes having both (active: yes) and (env: prod) labels. *
   * `labels.env: *` --> Any EntityType which has a label with 'env' as the key.
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `entity_type_id` * `create_time` * `update_time`
   * @opt_param int pageSize The maximum number of EntityTypes to return. The
   * service may return fewer than this value. If unspecified, at most 1000
   * EntityTypes will be returned. The maximum value is 1000; any value greater
   * than 1000 will be coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * FeaturestoreService.ListEntityTypes call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * FeaturestoreService.ListEntityTypes must match the call that provided the
   * page token.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListEntityTypesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsFeaturestoresEntityTypes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListEntityTypesResponse::class);
  }
  /**
   * Updates the parameters of a single EntityType. (entityTypes.patch)
   *
   * @param string $name Immutable. Name of the EntityType. Format: `projects/{pro
   * ject}/locations/{location}/featurestores/{featurestore}/entityTypes/{entity_t
   * ype}` The last part entity_type is assigned by the client. The entity_type
   * can be up to 64 characters long and can consist only of ASCII Latin letters
   * A-Z and a-z and underscore(_), and ASCII digits 0-9 starting with a letter.
   * The value will be unique given a featurestore.
   * @param GoogleCloudAiplatformV1EntityType $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Field mask is used to specify the fields to be
   * overwritten in the EntityType resource by the update. The fields specified in
   * the update_mask are relative to the resource, not the full request. A field
   * will be overwritten if it is in the mask. If the user does not provide a mask
   * then only the non-empty fields present in the request will be overwritten.
   * Set the update_mask to `*` to override all fields. Updatable fields: *
   * `description` * `labels` * `monitoring_config.snapshot_analysis.disabled` *
   * `monitoring_config.snapshot_analysis.monitoring_interval_days` *
   * `monitoring_config.snapshot_analysis.staleness_days` *
   * `monitoring_config.import_features_analysis.state` *
   * `monitoring_config.import_features_analysis.anomaly_detection_baseline` *
   * `monitoring_config.numerical_threshold_config.value` *
   * `monitoring_config.categorical_threshold_config.value` *
   * `offline_storage_ttl_days`
   * @return GoogleCloudAiplatformV1EntityType
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1EntityType $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1EntityType::class);
  }
  /**
   * Reads Feature values of a specific entity of an EntityType. For reading
   * feature values of multiple entities of an EntityType, please use
   * StreamingReadFeatureValues. (entityTypes.readFeatureValues)
   *
   * @param string $entityType Required. The resource name of the EntityType for
   * the entity being read. Value format: `projects/{project}/locations/{location}
   * /featurestores/{featurestore}/entityTypes/{entityType}`. For example, for a
   * machine learning model predicting user clicks on a website, an EntityType ID
   * could be `user`.
   * @param GoogleCloudAiplatformV1ReadFeatureValuesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ReadFeatureValuesResponse
   * @throws \Google\Service\Exception
   */
  public function readFeatureValues($entityType, GoogleCloudAiplatformV1ReadFeatureValuesRequest $postBody, $optParams = [])
  {
    $params = ['entityType' => $entityType, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('readFeatureValues', [$params], GoogleCloudAiplatformV1ReadFeatureValuesResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (entityTypes.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GoogleIamV1SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, GoogleIamV1SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Reads Feature values for multiple entities. Depending on their size, data for
   * different entities may be broken up across multiple responses.
   * (entityTypes.streamingReadFeatureValues)
   *
   * @param string $entityType Required. The resource name of the entities' type.
   * Value format: `projects/{project}/locations/{location}/featurestores/{feature
   * store}/entityTypes/{entityType}`. For example, for a machine learning model
   * predicting user clicks on a website, an EntityType ID could be `user`.
   * @param GoogleCloudAiplatformV1StreamingReadFeatureValuesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ReadFeatureValuesResponse
   * @throws \Google\Service\Exception
   */
  public function streamingReadFeatureValues($entityType, GoogleCloudAiplatformV1StreamingReadFeatureValuesRequest $postBody, $optParams = [])
  {
    $params = ['entityType' => $entityType, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('streamingReadFeatureValues', [$params], GoogleCloudAiplatformV1ReadFeatureValuesResponse::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (entityTypes.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string permissions The set of permissions to check for the
   * `resource`. Permissions with wildcards (such as `*` or `storage.*`) are not
   * allowed. For more information see [IAM
   * Overview](https://cloud.google.com/iam/docs/overview#permissions).
   * @return GoogleIamV1TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], GoogleIamV1TestIamPermissionsResponse::class);
  }
  /**
   * Writes Feature values of one or more entities of an EntityType. The Feature
   * values are merged into existing entities if any. The Feature values to be
   * written must have timestamp within the online storage retention.
   * (entityTypes.writeFeatureValues)
   *
   * @param string $entityType Required. The resource name of the EntityType for
   * the entities being written. Value format:
   * `projects/{project}/locations/{location}/featurestores/
   * {featurestore}/entityTypes/{entityType}`. For example, for a machine learning
   * model predicting user clicks on a website, an EntityType ID could be `user`.
   * @param GoogleCloudAiplatformV1WriteFeatureValuesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1WriteFeatureValuesResponse
   * @throws \Google\Service\Exception
   */
  public function writeFeatureValues($entityType, GoogleCloudAiplatformV1WriteFeatureValuesRequest $postBody, $optParams = [])
  {
    $params = ['entityType' => $entityType, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('writeFeatureValues', [$params], GoogleCloudAiplatformV1WriteFeatureValuesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsFeaturestoresEntityTypes::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsFeaturestoresEntityTypes');
