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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaListFirebaseLinksResponse extends \Google\Collection
{
  protected $collection_key = 'firebaseLinks';
  protected $firebaseLinksType = GoogleAnalyticsAdminV1betaFirebaseLink::class;
  protected $firebaseLinksDataType = 'array';
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages. Currently, Google
   * Analytics supports only one FirebaseLink per property, so this will never
   * be populated.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * List of FirebaseLinks. This will have at most one value.
   *
   * @param GoogleAnalyticsAdminV1betaFirebaseLink[] $firebaseLinks
   */
  public function setFirebaseLinks($firebaseLinks)
  {
    $this->firebaseLinks = $firebaseLinks;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaFirebaseLink[]
   */
  public function getFirebaseLinks()
  {
    return $this->firebaseLinks;
  }
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages. Currently, Google
   * Analytics supports only one FirebaseLink per property, so this will never
   * be populated.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaListFirebaseLinksResponse::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaListFirebaseLinksResponse');
