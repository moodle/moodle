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

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaDownloadTemplateResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaImportTemplateRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaImportTemplateResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaListTemplatesResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaSearchTemplatesResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaShareTemplateRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaTemplate;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUnshareTemplateRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUploadTemplateRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUploadTemplateResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUseTemplateRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaUseTemplateResponse;
use Google\Service\Integrations\GoogleProtobufEmpty;

/**
 * The "templates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $templates = $integrationsService->projects_locations_templates;
 *  </code>
 */
class ProjectsLocationsTemplates extends \Google\Service\Resource
{
  /**
   * Creates a new template (templates.create)
   *
   * @param string $parent Required. "projects/{project}/locations/{location}"
   * format.
   * @param GoogleCloudIntegrationsV1alphaTemplate $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaTemplate
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudIntegrationsV1alphaTemplate $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudIntegrationsV1alphaTemplate::class);
  }
  /**
   * Deletes a template (templates.delete)
   *
   * @param string $name Required. The name that is associated with the Template.
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
   * Downloads a template. Retrieves the `Template` and returns the response as a
   * string. (templates.download)
   *
   * @param string $name Required. The template to download. Format:
   * projects/{project}/locations/{location}/template/{template_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fileFormat Required. File format for download request.
   * @return GoogleCloudIntegrationsV1alphaDownloadTemplateResponse
   * @throws \Google\Service\Exception
   */
  public function download($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('download', [$params], GoogleCloudIntegrationsV1alphaDownloadTemplateResponse::class);
  }
  /**
   * Get a template in the specified project. (templates.get)
   *
   * @param string $name Required. The template to retrieve. Format:
   * projects/{project}/locations/{location}/templates/{template}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaTemplate
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudIntegrationsV1alphaTemplate::class);
  }
  /**
   * Import the template to an existing integration. This api would keep track of
   * usage_count and last_used_time. PERMISSION_DENIED would be thrown if template
   * is not accessible by client. (templates.import)
   *
   * @param string $name Required. The name that is associated with the Template.
   * @param GoogleCloudIntegrationsV1alphaImportTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaImportTemplateResponse
   * @throws \Google\Service\Exception
   */
  public function import($name, GoogleCloudIntegrationsV1alphaImportTemplateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], GoogleCloudIntegrationsV1alphaImportTemplateResponse::class);
  }
  /**
   * Lists all templates matching the filter.
   * (templates.listProjectsLocationsTemplates)
   *
   * @param string $parent Required. The client, which owns this collection of
   * Templates.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Standard filter field to filter templates.
   * client_id filter won't be supported and will restrict to templates belonging
   * to the current client only. Return all templates of the current client if the
   * filter is empty. Also supports operators like AND, OR, NOT For example,
   * "status=\"ACTIVE\"
   * @opt_param string orderBy Optional. The results would be returned in the
   * order you specified here.
   * @opt_param int pageSize Optional. The size of the response entries. If
   * unspecified, defaults to 100. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken Optional. The token returned in the previous
   * response.
   * @opt_param string readMask Optional. The mask which specifies fields that
   * need to be returned in the template's response.
   * @return GoogleCloudIntegrationsV1alphaListTemplatesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTemplates($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudIntegrationsV1alphaListTemplatesResponse::class);
  }
  /**
   * Updates the template by given id. (templates.patch)
   *
   * @param string $name Identifier. Resource name of the template.
   * @param GoogleCloudIntegrationsV1alphaTemplate $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask specifying the fields in
   * the above template that have been modified and must be updated.
   * @return GoogleCloudIntegrationsV1alphaTemplate
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudIntegrationsV1alphaTemplate $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudIntegrationsV1alphaTemplate::class);
  }
  /**
   * Search templates based on user query and filters. This api would query the
   * templates and return a list of templates based on the user filter.
   * (templates.search)
   *
   * @param string $parent Required. The client, which owns this collection of
   * Templates.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool enableNaturalLanguageQueryUnderstanding Optional. Whether to
   * enable natural language query understanding.
   * @opt_param string filter Optional. Standard filter field to filter templates.
   * client_id filter won't be supported and will restrict to templates belonging
   * to the current client only. Return all templates of the current client if the
   * filter is empty. Also supports operators like AND, OR, NOT For example,
   * "status=\"ACTIVE\"
   * @opt_param string orderBy Optional. The results would be returned in the
   * order you specified here.
   * @opt_param int pageSize Optional. The size of the response entries. If
   * unspecified, defaults to 100. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken Optional. The token returned in the previous
   * response.
   * @opt_param string query Optional. The search query that will be passed to
   * Vertex search service.
   * @opt_param string readMask Optional. The mask which specifies fields that
   * need to be returned in the template's response.
   * @return GoogleCloudIntegrationsV1alphaSearchTemplatesResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GoogleCloudIntegrationsV1alphaSearchTemplatesResponse::class);
  }
  /**
   * Share a template with other clients. Only the template owner can share the
   * templates with other projects. PERMISSION_DENIED would be thrown if the
   * request is not from the owner. (templates.share)
   *
   * @param string $name Required. The name that is associated with the Template.
   * @param GoogleCloudIntegrationsV1alphaShareTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function share($name, GoogleCloudIntegrationsV1alphaShareTemplateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('share', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Unshare a template from given clients. Owner of the template can unshare
   * template with clients. Shared client can only unshare the template from
   * itself. PERMISSION_DENIED would be thrown if request is not from owner or for
   * unsharing itself. (templates.unshare)
   *
   * @param string $name Required. The name that is associated with the Template.
   * @param GoogleCloudIntegrationsV1alphaUnshareTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function unshare($name, GoogleCloudIntegrationsV1alphaUnshareTemplateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unshare', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Uploads a template. The content can be a previously downloaded template.
   * Performs the same function as CreateTemplate, but accepts input in a string
   * format, which holds the complete representation of the Template content.
   * (templates.upload)
   *
   * @param string $parent Required. The template to upload. Format:
   * projects/{project}/locations/{location}
   * @param GoogleCloudIntegrationsV1alphaUploadTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaUploadTemplateResponse
   * @throws \Google\Service\Exception
   */
  public function upload($parent, GoogleCloudIntegrationsV1alphaUploadTemplateRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upload', [$params], GoogleCloudIntegrationsV1alphaUploadTemplateResponse::class);
  }
  /**
   * Use the template to create integration. This api would keep track of
   * usage_count and last_used_time. PERMISSION_DENIED would be thrown if template
   * is not accessible by client. (templates.useProjectsLocationsTemplates)
   *
   * @param string $name Required. The name that is associated with the Template.
   * @param GoogleCloudIntegrationsV1alphaUseTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaUseTemplateResponse
   * @throws \Google\Service\Exception
   */
  public function useProjectsLocationsTemplates($name, GoogleCloudIntegrationsV1alphaUseTemplateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('use', [$params], GoogleCloudIntegrationsV1alphaUseTemplateResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTemplates::class, 'Google_Service_Integrations_Resource_ProjectsLocationsTemplates');
