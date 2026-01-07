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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1ListSecurityFeedbackResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1SecurityFeedback;
use Google\Service\Apigee\GoogleProtobufEmpty;

/**
 * The "securityFeedback" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $securityFeedback = $apigeeService->organizations_securityFeedback;
 *  </code>
 */
class OrganizationsSecurityFeedback extends \Google\Service\Resource
{
  /**
   * Creates a new report containing customer feedback. (securityFeedback.create)
   *
   * @param string $parent Required. Name of the organization. Use the following
   * structure in your request: `organizations/{org}`.
   * @param GoogleCloudApigeeV1SecurityFeedback $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string securityFeedbackId Optional. The id for this feedback
   * report. If not provided, it will be set to a system-generated UUID.
   * @return GoogleCloudApigeeV1SecurityFeedback
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1SecurityFeedback $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1SecurityFeedback::class);
  }
  /**
   * Deletes a specific feedback report. Used for "undo" of a feedback submission.
   * (securityFeedback.delete)
   *
   * @param string $name Required. Name of the SecurityFeedback to delete. Use the
   * following structure in your request:
   * `organizations/{org}/securityFeedback/{feedback_id}`
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
   * Gets a specific customer feedback report. (securityFeedback.get)
   *
   * @param string $name Required. Name of the SecurityFeedback. Format:
   * `organizations/{org}/securityFeedback/{feedback_id}` Example:
   * organizations/apigee-organization-name/securityFeedback/feedback-id
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SecurityFeedback
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1SecurityFeedback::class);
  }
  /**
   * Lists all feedback reports which have already been submitted.
   * (securityFeedback.listOrganizationsSecurityFeedback)
   *
   * @param string $parent Required. Name of the organization. Format:
   * `organizations/{org}`. Example: organizations/apigee-organization-
   * name/securityFeedback
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of feedback reports to
   * return. The service may return fewer than this value.
   * LINT.IfChange(documented_page_size_limits) If unspecified, at most 10
   * feedback reports will be returned. The maximum value is 100; values above 100
   * will be coerced to 100. LINT.ThenChange( //depot/google3/edge/sense/boq/servi
   * ce/v1/securityfeedback/securityfeedback_rpc.go:page_size_limits )
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListSecurityFeedback` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListSecurityFeedback` must
   * match the call that provided the page token.
   * @return GoogleCloudApigeeV1ListSecurityFeedbackResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsSecurityFeedback($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListSecurityFeedbackResponse::class);
  }
  /**
   * Updates a specific feedback report. (securityFeedback.patch)
   *
   * @param string $name Output only. Identifier. The feedback name is intended to
   * be a system-generated uuid.
   * @param GoogleCloudApigeeV1SecurityFeedback $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleCloudApigeeV1SecurityFeedback
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApigeeV1SecurityFeedback $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApigeeV1SecurityFeedback::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSecurityFeedback::class, 'Google_Service_Apigee_Resource_OrganizationsSecurityFeedback');
