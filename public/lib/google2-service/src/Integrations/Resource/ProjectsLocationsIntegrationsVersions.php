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

namespace Google\Service\Integrations\Resource;

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaDownloadIntegrationVersionResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaDownloadJsonPackageResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaIntegrationVersion;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaListIntegrationVersionsResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaPublishIntegrationVersionRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaPublishIntegrationVersionResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaTestIntegrationsRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaTestIntegrationsResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUnpublishIntegrationVersionRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUploadIntegrationVersionRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUploadIntegrationVersionResponse;
use Google\Service\Integrations\GoogleProtobufEmpty;

/**
 * The "versions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $versions = $integrationsService->projects_locations_integrations_versions;
 *  </code>
 */
class ProjectsLocationsIntegrationsVersions extends \Google\Service\Resource
{
  /**
   * Create a integration with a draft version in the specified project.
   * (versions.create)
   *
   * @param string $parent Required. The parent resource where this version will
   * be created. Format:
   * projects/{project}/locations/{location}/integrations/{integration}
   * @param GoogleCloudIntegrationsV1alphaIntegrationVersion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool createSampleIntegrations Optional. Optional. Indicates if
   * sample workflow should be created.
   * @opt_param bool newIntegration Set this flag to true, if draft version is to
   * be created for a brand new integration. False, if the request is for an
   * existing integration. For backward compatibility reasons, even if this flag
   * is set to `false` and no existing integration is found, a new draft
   * integration will still be created.
   * @return GoogleCloudIntegrationsV1alphaIntegrationVersion
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudIntegrationsV1alphaIntegrationVersion $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudIntegrationsV1alphaIntegrationVersion::class);
  }
  /**
   * Soft-deletes the integration. Changes the status of the integration to
   * ARCHIVED. If the integration being ARCHIVED is tagged as "HEAD", the tag is
   * removed from this snapshot and set to the previous non-ARCHIVED snapshot. The
   * PUBLISH_REQUESTED, DUE_FOR_DELETION tags are removed too. This RPC throws an
   * exception if the version being deleted is DRAFT, and if the `locked_by` user
   * is not the same as the user performing the Delete. Audit fields updated
   * include last_modified_timestamp, last_modified_by. Any existing lock is
   * released when Deleting a integration. Currently, there is no undelete
   * mechanism. (versions.delete)
   *
   * @param string $name Required. The version to delete. Format: projects/{projec
   * t}/locations/{location}/integrations/{integration}/versions/{version}
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Downloads an integration. Retrieves the `IntegrationVersion` for a given
   * `integration_id` and returns the response as a string. (versions.download)
   *
   * @param string $name Required. The version to download. Format: projects/{proj
   * ect}/locations/{location}/integrations/{integration}/versions/{version}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fileFormat File format for download request.
   * @opt_param string files Optional. Integration related file to download like
   * Integration Json, Config variable, testcase etc.
   * @return GoogleCloudIntegrationsV1alphaDownloadIntegrationVersionResponse
   * @throws \Google\Service\Exception
   */
  public function download($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('download', [$params], GoogleCloudIntegrationsV1alphaDownloadIntegrationVersionResponse::class);
  }
  /**
   * Downloads an Integration version package like IntegrationVersion,Integration
   * Config etc. Retrieves the IntegrationVersion package for a given
   * `integration_id` and returns the response as a JSON.
   * (versions.downloadJsonPackage)
   *
   * @param string $name Required. Integration version name Format: projects/{proj
   * ect}/locations/{location}/integrations/{integration}/versions/{version}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string files Optional. Integration related file to download like
   * Integration Version, Config variable, testcase etc.
   * @return GoogleCloudIntegrationsV1alphaDownloadJsonPackageResponse
   * @throws \Google\Service\Exception
   */
  public function downloadJsonPackage($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('downloadJsonPackage', [$params], GoogleCloudIntegrationsV1alphaDownloadJsonPackageResponse::class);
  }
  /**
   * Get a integration in the specified project. (versions.get)
   *
   * @param string $name Required. The version to retrieve. Format: projects/{proj
   * ect}/locations/{location}/integrations/{integration}/versions/{version}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaIntegrationVersion
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudIntegrationsV1alphaIntegrationVersion::class);
  }
  /**
   * Returns the list of all integration versions in the specified project.
   * (versions.listProjectsLocationsIntegrationsVersions)
   *
   * @param string $parent Required. The parent resource where this version will
   * be created. Format:
   * projects/{project}/locations/{location}/integrations/{integration}
   * Specifically, when parent equals: 1. projects//locations//integrations/,
   * Meaning: "List versions (with filter) for a particular integration". 2.
   * projects//locations//integrations/- Meaning: "List versions (with filter) for
   * a client within a particular region".
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fieldMask The field mask which specifies the particular
   * data to be returned.
   * @opt_param string filter Filter on fields of IntegrationVersion. Fields can
   * be compared with literal values by use of ":" (containment), "=" (equality),
   * ">" (greater), "<" (less than), >=" (greater than or equal to), "<=" (less
   * than or equal to), and "!=" (inequality) operators. Negation, conjunction,
   * and disjunction are written using NOT, AND, and OR keywords. For example,
   * organization_id=\"1\" AND state=ACTIVE AND description:"test". Filtering
   * cannot be performed on repeated fields like `task_config`.
   * @opt_param string orderBy The results would be returned in order you
   * specified here. Currently supported sort keys are: Descending sort order for
   * "last\_modified\_time", "created\_time", and "snapshot\_number". Ascending
   * sort order for `name`.
   * @opt_param int pageSize The maximum number of versions to return. The service
   * may return fewer than this value. If unspecified, at most 50 versions will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListIntegrationVersions` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListIntegrationVersions`
   * must match the call that provided the page token.
   * @return GoogleCloudIntegrationsV1alphaListIntegrationVersionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsIntegrationsVersions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudIntegrationsV1alphaListIntegrationVersionsResponse::class);
  }
  /**
   * Update a integration with a draft version in the specified project.
   * (versions.patch)
   *
   * @param string $name Output only. Auto-generated primary key.
   * @param GoogleCloudIntegrationsV1alphaIntegrationVersion $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Field mask specifying the fields in the above
   * integration that have been modified and need to be updated.
   * @return GoogleCloudIntegrationsV1alphaIntegrationVersion
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudIntegrationsV1alphaIntegrationVersion $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudIntegrationsV1alphaIntegrationVersion::class);
  }
  /**
   * This RPC throws an exception if the integration is in ARCHIVED or ACTIVE
   * state. This RPC throws an exception if the version being published is DRAFT,
   * and if the `locked_by` user is not the same as the user performing the
   * Publish. Audit fields updated include last_published_timestamp,
   * last_published_by, last_modified_timestamp, last_modified_by. Any existing
   * lock is on this integration is released. (versions.publish)
   *
   * @param string $name Required. The version to publish. Format: projects/{proje
   * ct}/locations/{location}/integrations/{integration}/versions/{version}
   * @param GoogleCloudIntegrationsV1alphaPublishIntegrationVersionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaPublishIntegrationVersionResponse
   * @throws \Google\Service\Exception
   */
  public function publish($name, GoogleCloudIntegrationsV1alphaPublishIntegrationVersionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('publish', [$params], GoogleCloudIntegrationsV1alphaPublishIntegrationVersionResponse::class);
  }
  /**
   * Execute the integration in draft state (versions.test)
   *
   * @param string $name Output only. Auto-generated primary key.
   * @param GoogleCloudIntegrationsV1alphaTestIntegrationsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaTestIntegrationsResponse
   * @throws \Google\Service\Exception
   */
  public function test($name, GoogleCloudIntegrationsV1alphaTestIntegrationsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('test', [$params], GoogleCloudIntegrationsV1alphaTestIntegrationsResponse::class);
  }
  /**
   * Sets the status of the ACTIVE integration to SNAPSHOT with a new tag
   * "PREVIOUSLY_PUBLISHED" after validating it. The "HEAD" and
   * "PUBLISH_REQUESTED" tags do not change. This RPC throws an exception if the
   * version being snapshot is not ACTIVE. Audit fields added include action,
   * action_by, action_timestamp. (versions.unpublish)
   *
   * @param string $name Required. The version to deactivate. Format: projects/{pr
   * oject}/locations/{location}/integrations/{integration}/versions/{version}
   * @param GoogleCloudIntegrationsV1alphaUnpublishIntegrationVersionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function unpublish($name, GoogleCloudIntegrationsV1alphaUnpublishIntegrationVersionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unpublish', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Uploads an integration. The content can be a previously downloaded
   * integration. Performs the same function as CreateDraftIntegrationVersion, but
   * accepts input in a string format, which holds the complete representation of
   * the IntegrationVersion content. (versions.upload)
   *
   * @param string $parent Required. The version to upload. Format:
   * projects/{project}/locations/{location}/integrations/{integration}
   * @param GoogleCloudIntegrationsV1alphaUploadIntegrationVersionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaUploadIntegrationVersionResponse
   * @throws \Google\Service\Exception
   */
  public function upload($parent, GoogleCloudIntegrationsV1alphaUploadIntegrationVersionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], GoogleCloudIntegrationsV1alphaUploadIntegrationVersionResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsIntegrationsVersions::class, 'Google_Service_Integrations_Resource_ProjectsLocationsIntegrationsVersions');
