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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchCreateFeaturesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1Feature;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListFeaturesResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "features" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $features = $aiplatformService->projects_locations_featurestores_entityTypes_features;
 *  </code>
 */
class ProjectsLocationsFeaturestoresEntityTypesFeatures extends \Google\Service\Resource
{
  /**
   * Creates a batch of Features in a given EntityType. (features.batchCreate)
   *
   * @param string $parent Required. The resource name of the
   * EntityType/FeatureGroup to create the batch of Features under. Format: `proje
   * cts/{project}/locations/{location}/featurestores/{featurestore}/entityTypes/{
   * entity_type}`
   * `projects/{project}/locations/{location}/featureGroups/{feature_group}`
   * @param GoogleCloudAiplatformV1BatchCreateFeaturesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function batchCreate($parent, GoogleCloudAiplatformV1BatchCreateFeaturesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCreate', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a new Feature in a given EntityType. (features.create)
   *
   * @param string $parent Required. The resource name of the EntityType or
   * FeatureGroup to create a Feature. Format for entity_type as parent: `projects
   * /{project}/locations/{location}/featurestores/{featurestore}/entityTypes/{ent
   * ity_type}` Format for feature_group as parent:
   * `projects/{project}/locations/{location}/featureGroups/{feature_group}`
   * @param GoogleCloudAiplatformV1Feature $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string featureId Required. The ID to use for the Feature, which
   * will become the final component of the Feature's resource name. This value
   * may be up to 128 characters, and valid characters are `[a-z0-9_]`. The first
   * character cannot be a number. The value must be unique within an
   * EntityType/FeatureGroup.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Feature $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a single Feature. (features.delete)
   *
   * @param string $name Required. The name of the Features to be deleted. Format:
   * `projects/{project}/locations/{location}/featurestores/{featurestore}/entityT
   * ypes/{entity_type}/features/{feature}` `projects/{project}/locations/{locatio
   * n}/featureGroups/{feature_group}/features/{feature}`
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
   * Gets details of a single Feature. (features.get)
   *
   * @param string $name Required. The name of the Feature resource. Format for
   * entity_type as parent: `projects/{project}/locations/{location}/featurestores
   * /{featurestore}/entityTypes/{entity_type}` Format for feature_group as
   * parent:
   * `projects/{project}/locations/{location}/featureGroups/{feature_group}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Feature
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Feature::class);
  }
  /**
   * Lists Features in a given EntityType.
   * (features.listProjectsLocationsFeaturestoresEntityTypesFeatures)
   *
   * @param string $parent Required. The resource name of the Location to list
   * Features. Format for entity_type as parent: `projects/{project}/locations/{lo
   * cation}/featurestores/{featurestore}/entityTypes/{entity_type}` Format for
   * feature_group as parent:
   * `projects/{project}/locations/{location}/featureGroups/{feature_group}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the Features that match the filter expression.
   * The following filters are supported: * `value_type`: Supports = and !=
   * comparisons. * `create_time`: Supports =, !=, <, >, >=, and <= comparisons.
   * Values must be in RFC 3339 format. * `update_time`: Supports =, !=, <, >, >=,
   * and <= comparisons. Values must be in RFC 3339 format. * `labels`: Supports
   * key-value equality as well as key presence. Examples: * `value_type = DOUBLE`
   * --> Features whose type is DOUBLE. * `create_time >
   * \"2020-01-31T15:30:00.000000Z\" OR update_time >
   * \"2020-01-31T15:30:00.000000Z\"` --> EntityTypes created or updated after
   * 2020-01-31T15:30:00.000000Z. * `labels.active = yes AND labels.env = prod`
   * --> Features having both (active: yes) and (env: prod) labels. * `labels.env:
   * *` --> Any Feature which has a label with 'env' as the key.
   * @opt_param int latestStatsCount Only applicable for Vertex AI Feature Store
   * (Legacy). If set, return the most recent
   * ListFeaturesRequest.latest_stats_count of stats for each Feature in response.
   * Valid value is [0, 10]. If number of stats exists <
   * ListFeaturesRequest.latest_stats_count, return all existing stats.
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `feature_id` * `value_type` (Not supported for
   * FeatureRegistry Feature) * `create_time` * `update_time`
   * @opt_param int pageSize The maximum number of Features to return. The service
   * may return fewer than this value. If unspecified, at most 1000 Features will
   * be returned. The maximum value is 1000; any value greater than 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * FeaturestoreService.ListFeatures call or FeatureRegistryService.ListFeatures
   * call. Provide this to retrieve the subsequent page. When paginating, all
   * other parameters provided to FeaturestoreService.ListFeatures or
   * FeatureRegistryService.ListFeatures must match the call that provided the
   * page token.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListFeaturesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsFeaturestoresEntityTypesFeatures($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListFeaturesResponse::class);
  }
  /**
   * Updates the parameters of a single Feature. (features.patch)
   *
   * @param string $name Immutable. Name of the Feature. Format: `projects/{projec
   * t}/locations/{location}/featurestores/{featurestore}/entityTypes/{entity_type
   * }/features/{feature}` `projects/{project}/locations/{location}/featureGroups/
   * {feature_group}/features/{feature}` The last part feature is assigned by the
   * client. The feature can be up to 64 characters long and can consist only of
   * ASCII Latin letters A-Z and a-z, underscore(_), and ASCII digits 0-9 starting
   * with a letter. The value will be unique given an entity type.
   * @param GoogleCloudAiplatformV1Feature $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Field mask is used to specify the fields to be
   * overwritten in the Features resource by the update. The fields specified in
   * the update_mask are relative to the resource, not the full request. A field
   * will be overwritten if it is in the mask. If the user does not provide a mask
   * then only the non-empty fields present in the request will be overwritten.
   * Set the update_mask to `*` to override all fields. Updatable fields: *
   * `description` * `labels` * `disable_monitoring` (Not supported for
   * FeatureRegistryService Feature) * `point_of_contact` (Not supported for
   * FeaturestoreService FeatureStore)
   * @return GoogleCloudAiplatformV1Feature
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1Feature $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1Feature::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsFeaturestoresEntityTypesFeatures::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsFeaturestoresEntityTypesFeatures');
