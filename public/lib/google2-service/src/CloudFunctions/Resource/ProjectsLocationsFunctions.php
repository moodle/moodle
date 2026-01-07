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

namespace Google\Service\CloudFunctions\Resource;

use Google\Service\CloudFunctions\AbortFunctionUpgradeRequest;
use Google\Service\CloudFunctions\CloudfunctionsFunction;
use Google\Service\CloudFunctions\CommitFunctionUpgradeAsGen2Request;
use Google\Service\CloudFunctions\CommitFunctionUpgradeRequest;
use Google\Service\CloudFunctions\DetachFunctionRequest;
use Google\Service\CloudFunctions\GenerateDownloadUrlRequest;
use Google\Service\CloudFunctions\GenerateDownloadUrlResponse;
use Google\Service\CloudFunctions\GenerateUploadUrlRequest;
use Google\Service\CloudFunctions\GenerateUploadUrlResponse;
use Google\Service\CloudFunctions\ListFunctionsResponse;
use Google\Service\CloudFunctions\Operation;
use Google\Service\CloudFunctions\Policy;
use Google\Service\CloudFunctions\RedirectFunctionUpgradeTrafficRequest;
use Google\Service\CloudFunctions\RollbackFunctionUpgradeTrafficRequest;
use Google\Service\CloudFunctions\SetIamPolicyRequest;
use Google\Service\CloudFunctions\SetupFunctionUpgradeConfigRequest;
use Google\Service\CloudFunctions\TestIamPermissionsRequest;
use Google\Service\CloudFunctions\TestIamPermissionsResponse;

/**
 * The "functions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudfunctionsService = new Google\Service\CloudFunctions(...);
 *   $functions = $cloudfunctionsService->projects_locations_functions;
 *  </code>
 */
class ProjectsLocationsFunctions extends \Google\Service\Resource
{
  /**
   * Aborts generation upgrade process for a function with the given name from the
   * specified project. Deletes all 2nd Gen copy related configuration and
   * resources which were created during the upgrade process.
   * (functions.abortFunctionUpgrade)
   *
   * @param string $name Required. The name of the function for which upgrade
   * should be aborted.
   * @param AbortFunctionUpgradeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function abortFunctionUpgrade($name, AbortFunctionUpgradeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('abortFunctionUpgrade', [$params], Operation::class);
  }
  /**
   * Finalizes the upgrade after which function upgrade can not be rolled back.
   * This is the last step of the multi step process to upgrade 1st Gen functions
   * to 2nd Gen. Deletes all original 1st Gen related configuration and resources.
   * (functions.commitFunctionUpgrade)
   *
   * @param string $name Required. The name of the function for which upgrade
   * should be finalized.
   * @param CommitFunctionUpgradeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function commitFunctionUpgrade($name, CommitFunctionUpgradeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('commitFunctionUpgrade', [$params], Operation::class);
  }
  /**
   * Commits a function upgrade from GCF Gen1 to GCF Gen2. This action deletes the
   * Gen1 function, leaving the Gen2 function active and manageable by the GCFv2
   * API. (functions.commitFunctionUpgradeAsGen2)
   *
   * @param string $name Required. The name of the function for which upgrade
   * should be committed to Gen2.
   * @param CommitFunctionUpgradeAsGen2Request $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function commitFunctionUpgradeAsGen2($name, CommitFunctionUpgradeAsGen2Request $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('commitFunctionUpgradeAsGen2', [$params], Operation::class);
  }
  /**
   * Creates a new function. If a function with the given name already exists in
   * the specified project, the long running operation will return
   * `ALREADY_EXISTS` error. (functions.create)
   *
   * @param string $parent Required. The project and location in which the
   * function should be created, specified in the format `projects/locations`
   * @param CloudfunctionsFunction $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string functionId The ID to use for the function, which will
   * become the final component of the function's resource name. This value should
   * be 4-63 characters, and valid characters are /a-z-/.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, CloudfunctionsFunction $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a function with the given name from the specified project. If the
   * given function is used by some trigger, the trigger will be updated to remove
   * this function. (functions.delete)
   *
   * @param string $name Required. The name of the function which should be
   * deleted.
   * @param array $optParams Optional parameters.
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
   * Detaches 2nd Gen function to Cloud Run function. (functions.detachFunction)
   *
   * @param string $name Required. The name of the function for which should be
   * detached.
   * @param DetachFunctionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function detachFunction($name, DetachFunctionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('detachFunction', [$params], Operation::class);
  }
  /**
   * Returns a signed URL for downloading deployed function source code. The URL
   * is only valid for a limited period and should be used within 30 minutes of
   * generation. For more information about the signed URL usage see:
   * https://cloud.google.com/storage/docs/access-control/signed-urls
   * (functions.generateDownloadUrl)
   *
   * @param string $name Required. The name of function for which source code
   * Google Cloud Storage signed URL should be generated.
   * @param GenerateDownloadUrlRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GenerateDownloadUrlResponse
   * @throws \Google\Service\Exception
   */
  public function generateDownloadUrl($name, GenerateDownloadUrlRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateDownloadUrl', [$params], GenerateDownloadUrlResponse::class);
  }
  /**
   * Returns a signed URL for uploading a function source code. For more
   * information about the signed URL usage see:
   * https://cloud.google.com/storage/docs/access-control/signed-urls. Once the
   * function source code upload is complete, the used signed URL should be
   * provided in CreateFunction or UpdateFunction request as a reference to the
   * function source code. When uploading source code to the generated signed URL,
   * please follow these restrictions: * Source file type should be a zip file. *
   * No credentials should be attached - the signed URLs provide access to the
   * target bucket using internal service identity; if credentials were attached,
   * the identity from the credentials would be used, but that identity does not
   * have permissions to upload files to the URL. When making a HTTP PUT request,
   * specify this header: * `content-type: application/zip` Do not specify this
   * header: * `Authorization: Bearer YOUR_TOKEN` (functions.generateUploadUrl)
   *
   * @param string $parent Required. The project and location in which the Google
   * Cloud Storage signed URL should be generated, specified in the format
   * `projects/locations`.
   * @param GenerateUploadUrlRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GenerateUploadUrlResponse
   * @throws \Google\Service\Exception
   */
  public function generateUploadUrl($parent, GenerateUploadUrlRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateUploadUrl', [$params], GenerateUploadUrlResponse::class);
  }
  /**
   * Returns a function with the given name from the requested project.
   * (functions.get)
   *
   * @param string $name Required. The name of the function which details should
   * be obtained.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string revision Optional. The optional version of the 1st gen
   * function whose details should be obtained. The version of a 1st gen function
   * is an integer that starts from 1 and gets incremented on redeployments. GCF
   * may keep historical configs for old versions of 1st gen function. This field
   * can be specified to fetch the historical configs. This field is valid only
   * for GCF 1st gen function.
   * @return CloudfunctionsFunction
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CloudfunctionsFunction::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (functions.getIamPolicy)
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
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns a list of functions that belong to the requested project.
   * (functions.listProjectsLocationsFunctions)
   *
   * @param string $parent Required. The project and location from which the
   * function should be listed, specified in the format `projects/locations` If
   * you want to list functions in all locations, use "-" in place of a location.
   * When listing functions in all locations, if one or more location(s) are
   * unreachable, the response will contain functions from all reachable locations
   * along with the names of any unreachable locations.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The filter for Functions that match the filter
   * expression, following the syntax outlined in https://google.aip.dev/160.
   * @opt_param string orderBy The sorting order of the resources returned. Value
   * should be a comma separated list of fields. The default sorting order is
   * ascending. See https://google.aip.dev/132#ordering.
   * @opt_param int pageSize Maximum number of functions to return per call. The
   * largest allowed page_size is 1,000, if the page_size is omitted or specified
   * as greater than 1,000 then it will be replaced as 1,000. The size of the list
   * response can be less than specified when used with filters.
   * @opt_param string pageToken The value returned by the last
   * `ListFunctionsResponse`; indicates that this is a continuation of a prior
   * `ListFunctions` call, and that the system should return the next page of
   * data.
   * @return ListFunctionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsFunctions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFunctionsResponse::class);
  }
  /**
   * Updates existing function. (functions.patch)
   *
   * @param string $name A user-defined name of the function. Function names must
   * be unique globally and match pattern `projects/locations/functions`
   * @param CloudfunctionsFunction $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to be updated. If no field
   * mask is provided, all fields will be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, CloudfunctionsFunction $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Changes the traffic target of a function from the original 1st Gen function
   * to the 2nd Gen copy. This is the second step of the multi step process to
   * upgrade 1st Gen functions to 2nd Gen. After this operation, all new traffic
   * will be served by 2nd Gen copy. (functions.redirectFunctionUpgradeTraffic)
   *
   * @param string $name Required. The name of the function for which traffic
   * target should be changed to 2nd Gen from 1st Gen.
   * @param RedirectFunctionUpgradeTrafficRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function redirectFunctionUpgradeTraffic($name, RedirectFunctionUpgradeTrafficRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('redirectFunctionUpgradeTraffic', [$params], Operation::class);
  }
  /**
   * Reverts the traffic target of a function from the 2nd Gen copy to the
   * original 1st Gen function. After this operation, all new traffic would be
   * served by the 1st Gen. (functions.rollbackFunctionUpgradeTraffic)
   *
   * @param string $name Required. The name of the function for which traffic
   * target should be changed back to 1st Gen from 2nd Gen.
   * @param RollbackFunctionUpgradeTrafficRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function rollbackFunctionUpgradeTraffic($name, RollbackFunctionUpgradeTrafficRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rollbackFunctionUpgradeTraffic', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (functions.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Creates a 2nd Gen copy of the function configuration based on the 1st Gen
   * function with the given name. This is the first step of the multi step
   * process to upgrade 1st Gen functions to 2nd Gen. Only 2nd Gen configuration
   * is setup as part of this request and traffic continues to be served by 1st
   * Gen. (functions.setupFunctionUpgradeConfig)
   *
   * @param string $name Required. The name of the function which should have
   * configuration copied for upgrade.
   * @param SetupFunctionUpgradeConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function setupFunctionUpgradeConfig($name, SetupFunctionUpgradeConfigRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setupFunctionUpgradeConfig', [$params], Operation::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (functions.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsFunctions::class, 'Google_Service_CloudFunctions_Resource_ProjectsLocationsFunctions');
