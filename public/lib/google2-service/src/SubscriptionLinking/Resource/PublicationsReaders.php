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

namespace Google\Service\SubscriptionLinking\Resource;

use Google\Service\SubscriptionLinking\DeleteReaderResponse;
use Google\Service\SubscriptionLinking\Reader;
use Google\Service\SubscriptionLinking\ReaderEntitlements;

/**
 * The "readers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $readerrevenuesubscriptionlinkingService = new Google\Service\SubscriptionLinking(...);
 *   $readers = $readerrevenuesubscriptionlinkingService->publications_readers;
 *  </code>
 */
class PublicationsReaders extends \Google\Service\Resource
{
  /**
   * Removes a publication reader, effectively severing the association with a
   * Google user. If `force` is set to true, any entitlements for this reader will
   * also be deleted. (Otherwise, the request will only work if the reader has no
   * entitlements.) - If the reader does not exist, return NOT_FOUND. - Return
   * FAILED_PRECONDITION if the force field is false (or unset) and entitlements
   * are present. (readers.delete)
   *
   * @param string $name Required. The resource name of the reader. Format:
   * publications/{publication_id}/readers/{ppid}
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force If set to true, any entitlements under the reader will
   * also be purged.
   * @return DeleteReaderResponse
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], DeleteReaderResponse::class);
  }
  /**
   * Gets a reader of a publication. Returns NOT_FOUND if the reader does not
   * exist. (readers.get)
   *
   * @param string $name Required. The resource name of the reader. Format:
   * publications/{publication_id}/readers/{ppid}
   * @param array $optParams Optional parameters.
   * @return Reader
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Reader::class);
  }
  /**
   * Gets the reader entitlements for a publication reader. - Returns
   * PERMISSION_DENIED if the caller does not have access. - Returns NOT_FOUND if
   * the reader does not exist. (readers.getEntitlements)
   *
   * @param string $name Required. The name of the reader entitlements to
   * retrieve. Format:
   * publications/{publication_id}/readers/{reader_id}/entitlements
   * @param array $optParams Optional parameters.
   * @return ReaderEntitlements
   * @throws \Google\Service\Exception
   */
  public function getEntitlements($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getEntitlements', [$params], ReaderEntitlements::class);
  }
  /**
   * Updates the reader entitlements for a publication reader. The entire reader
   * entitlements will be overwritten by the new reader entitlements in the
   * payload, like a PUT. - Returns PERMISSION_DENIED if the caller does not have
   * access. - Returns NOT_FOUND if the reader does not exist.
   * (readers.updateEntitlements)
   *
   * @param string $name Output only. The resource name of the singleton.
   * @param ReaderEntitlements $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update. Defaults
   * to all fields.
   * @return ReaderEntitlements
   * @throws \Google\Service\Exception
   */
  public function updateEntitlements($name, ReaderEntitlements $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateEntitlements', [$params], ReaderEntitlements::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublicationsReaders::class, 'Google_Service_SubscriptionLinking_Resource_PublicationsReaders');
