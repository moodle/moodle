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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1UrlMetadata extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const URL_RETRIEVAL_STATUS_URL_RETRIEVAL_STATUS_UNSPECIFIED = 'URL_RETRIEVAL_STATUS_UNSPECIFIED';
  /**
   * The URL was retrieved successfully.
   */
  public const URL_RETRIEVAL_STATUS_URL_RETRIEVAL_STATUS_SUCCESS = 'URL_RETRIEVAL_STATUS_SUCCESS';
  /**
   * The URL retrieval failed.
   */
  public const URL_RETRIEVAL_STATUS_URL_RETRIEVAL_STATUS_ERROR = 'URL_RETRIEVAL_STATUS_ERROR';
  /**
   * The URL retrieved by the tool.
   *
   * @var string
   */
  public $retrievedUrl;
  /**
   * The status of the URL retrieval.
   *
   * @var string
   */
  public $urlRetrievalStatus;

  /**
   * The URL retrieved by the tool.
   *
   * @param string $retrievedUrl
   */
  public function setRetrievedUrl($retrievedUrl)
  {
    $this->retrievedUrl = $retrievedUrl;
  }
  /**
   * @return string
   */
  public function getRetrievedUrl()
  {
    return $this->retrievedUrl;
  }
  /**
   * The status of the URL retrieval.
   *
   * Accepted values: URL_RETRIEVAL_STATUS_UNSPECIFIED,
   * URL_RETRIEVAL_STATUS_SUCCESS, URL_RETRIEVAL_STATUS_ERROR
   *
   * @param self::URL_RETRIEVAL_STATUS_* $urlRetrievalStatus
   */
  public function setUrlRetrievalStatus($urlRetrievalStatus)
  {
    $this->urlRetrievalStatus = $urlRetrievalStatus;
  }
  /**
   * @return self::URL_RETRIEVAL_STATUS_*
   */
  public function getUrlRetrievalStatus()
  {
    return $this->urlRetrievalStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UrlMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UrlMetadata');
