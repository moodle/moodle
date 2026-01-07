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

namespace Google\Service\DLP\Resource;

use Google\Service\DLP\GooglePrivacyDlpV2CreateInspectTemplateRequest;
use Google\Service\DLP\GooglePrivacyDlpV2InspectTemplate;
use Google\Service\DLP\GooglePrivacyDlpV2ListInspectTemplatesResponse;
use Google\Service\DLP\GooglePrivacyDlpV2UpdateInspectTemplateRequest;
use Google\Service\DLP\GoogleProtobufEmpty;

/**
 * The "inspectTemplates" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dlpService = new Google\Service\DLP(...);
 *   $inspectTemplates = $dlpService->organizations_inspectTemplates;
 *  </code>
 */
class OrganizationsInspectTemplates extends \Google\Service\Resource
{
  /**
   * Creates an InspectTemplate for reusing frequently used configuration for
   * inspecting content, images, and storage. See
   * https://cloud.google.com/sensitive-data-protection/docs/creating-templates to
   * learn more. (inspectTemplates.create)
   *
   * @param string $parent Required. Parent resource name. The format of this
   * value varies depending on the scope of the request (project or organization)
   * and whether you have [specified a processing
   * location](https://cloud.google.com/sensitive-data-protection/docs/specifying-
   * location): + Projects scope, location specified:
   * `projects/{project_id}/locations/{location_id}` + Projects scope, no location
   * specified (defaults to global): `projects/{project_id}` + Organizations
   * scope, location specified: `organizations/{org_id}/locations/{location_id}` +
   * Organizations scope, no location specified (defaults to global):
   * `organizations/{org_id}` The following example `parent` string specifies a
   * parent project with the identifier `example-project`, and specifies the
   * `europe-west3` location for processing data: parent=projects/example-
   * project/locations/europe-west3
   * @param GooglePrivacyDlpV2CreateInspectTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2InspectTemplate
   * @throws \Google\Service\Exception
   */
  public function create($parent, GooglePrivacyDlpV2CreateInspectTemplateRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GooglePrivacyDlpV2InspectTemplate::class);
  }
  /**
   * Deletes an InspectTemplate. See https://cloud.google.com/sensitive-data-
   * protection/docs/creating-templates to learn more. (inspectTemplates.delete)
   *
   * @param string $name Required. Resource name of the organization and
   * inspectTemplate to be deleted, for example
   * `organizations/433245324/inspectTemplates/432452342` or projects/project-
   * id/inspectTemplates/432452342.
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
   * Gets an InspectTemplate. See https://cloud.google.com/sensitive-data-
   * protection/docs/creating-templates to learn more. (inspectTemplates.get)
   *
   * @param string $name Required. Resource name of the organization and
   * inspectTemplate to be read, for example
   * `organizations/433245324/inspectTemplates/432452342` or projects/project-
   * id/inspectTemplates/432452342.
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2InspectTemplate
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GooglePrivacyDlpV2InspectTemplate::class);
  }
  /**
   * Lists InspectTemplates. See https://cloud.google.com/sensitive-data-
   * protection/docs/creating-templates to learn more.
   * (inspectTemplates.listOrganizationsInspectTemplates)
   *
   * @param string $parent Required. Parent resource name. The format of this
   * value varies depending on the scope of the request (project or organization)
   * and whether you have [specified a processing
   * location](https://cloud.google.com/sensitive-data-protection/docs/specifying-
   * location): + Projects scope, location specified:
   * `projects/{project_id}/locations/{location_id}` + Projects scope, no location
   * specified (defaults to global): `projects/{project_id}` + Organizations
   * scope, location specified: `organizations/{org_id}/locations/{location_id}` +
   * Organizations scope, no location specified (defaults to global):
   * `organizations/{org_id}` The following example `parent` string specifies a
   * parent project with the identifier `example-project`, and specifies the
   * `europe-west3` location for processing data: parent=projects/example-
   * project/locations/europe-west3
   * @param array $optParams Optional parameters.
   *
   * @opt_param string locationId Deprecated. This field has no effect.
   * @opt_param string orderBy Comma-separated list of fields to order by,
   * followed by `asc` or `desc` postfix. This list is case insensitive. The
   * default sorting order is ascending. Redundant space characters are
   * insignificant. Example: `name asc,update_time, create_time desc` Supported
   * fields are: - `create_time`: corresponds to the time the template was
   * created. - `update_time`: corresponds to the time the template was last
   * updated. - `name`: corresponds to the template's name. - `display_name`:
   * corresponds to the template's display name.
   * @opt_param int pageSize Size of the page. This value can be limited by the
   * server. If zero server returns a page of max size 100.
   * @opt_param string pageToken Page token to continue retrieval. Comes from the
   * previous call to `ListInspectTemplates`.
   * @return GooglePrivacyDlpV2ListInspectTemplatesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsInspectTemplates($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GooglePrivacyDlpV2ListInspectTemplatesResponse::class);
  }
  /**
   * Updates the InspectTemplate. See https://cloud.google.com/sensitive-data-
   * protection/docs/creating-templates to learn more. (inspectTemplates.patch)
   *
   * @param string $name Required. Resource name of organization and
   * inspectTemplate to be updated, for example
   * `organizations/433245324/inspectTemplates/432452342` or projects/project-
   * id/inspectTemplates/432452342.
   * @param GooglePrivacyDlpV2UpdateInspectTemplateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2InspectTemplate
   * @throws \Google\Service\Exception
   */
  public function patch($name, GooglePrivacyDlpV2UpdateInspectTemplateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GooglePrivacyDlpV2InspectTemplate::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsInspectTemplates::class, 'Google_Service_DLP_Resource_OrganizationsInspectTemplates');
