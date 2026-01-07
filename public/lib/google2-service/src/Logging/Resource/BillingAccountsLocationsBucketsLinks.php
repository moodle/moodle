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

namespace Google\Service\Logging\Resource;

use Google\Service\Logging\Link;
use Google\Service\Logging\ListLinksResponse;
use Google\Service\Logging\Operation;

/**
 * The "links" collection of methods.
 * Typical usage is:
 *  <code>
 *   $loggingService = new Google\Service\Logging(...);
 *   $links = $loggingService->billingAccounts_locations_buckets_links;
 *  </code>
 */
class BillingAccountsLocationsBucketsLinks extends \Google\Service\Resource
{
  /**
   * Asynchronously creates a linked dataset in BigQuery which makes it possible
   * to use BigQuery to read the logs stored in the log bucket. A log bucket may
   * currently only contain one link. (links.create)
   *
   * @param string $parent Required. The full resource name of the bucket to
   * create a link for.
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"
   * @param Link $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string linkId Required. The ID to use for the link. The link_id
   * can have up to 100 characters. A valid link_id must only have alphanumeric
   * characters and underscores within it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Link $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a link. This will also delete the corresponding BigQuery linked
   * dataset. (links.delete)
   *
   * @param string $name Required. The full resource name of the link to delete. "
   * projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/links/[LINK
   * _ID]" "organizations/[ORGANIZATION_ID]/locations/[LOCATION_ID]/buckets/[BUCKE
   * T_ID]/links/[LINK_ID]" "billingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCAT
   * ION_ID]/buckets/[BUCKET_ID]/links/[LINK_ID]" "folders/[FOLDER_ID]/locations/[
   * LOCATION_ID]/buckets/[BUCKET_ID]/links/[LINK_ID]"
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
   * Gets a link. (links.get)
   *
   * @param string $name Required. The resource name of the link: "projects/[PROJE
   * CT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/links/[LINK_ID]" "organiza
   * tions/[ORGANIZATION_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/links/[LI
   * NK_ID]" "billingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCATION_ID]/buckets
   * /[BUCKET_ID]/links/[LINK_ID]" "folders/[FOLDER_ID]/locations/[LOCATION_ID]/bu
   * ckets/[BUCKET_ID]/links/[LINK_ID]"
   * @param array $optParams Optional parameters.
   * @return Link
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Link::class);
  }
  /**
   * Lists links. (links.listBillingAccountsLocationsBucketsLinks)
   *
   * @param string $parent Required. The parent resource whose links are to be
   * listed: "projects/[PROJECT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"
   * "organizations/[ORGANIZATION_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"
   * "billingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCATION_ID]/buckets/[BUCKET
   * _ID]" "folders/[FOLDER_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of results to return
   * from this request.
   * @opt_param string pageToken Optional. If present, then retrieve the next
   * batch of results from the preceding call to this method. pageToken must be
   * the value of nextPageToken from the previous response.
   * @return ListLinksResponse
   * @throws \Google\Service\Exception
   */
  public function listBillingAccountsLocationsBucketsLinks($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListLinksResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BillingAccountsLocationsBucketsLinks::class, 'Google_Service_Logging_Resource_BillingAccountsLocationsBucketsLinks');
