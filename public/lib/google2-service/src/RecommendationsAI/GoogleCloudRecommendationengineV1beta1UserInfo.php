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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1UserInfo extends \Google\Model
{
  /**
   * Optional. Indicates if the request is made directly from the end user in
   * which case the user_agent and ip_address fields can be populated from the
   * HTTP request. This should *not* be set when using the javascript pixel.
   * This flag should be set only if the API request is made directly from the
   * end user such as a mobile app (and not if a gateway or a server is
   * processing and pushing the user events).
   *
   * @var bool
   */
  public $directUserRequest;
  /**
   * Optional. IP address of the user. This could be either IPv4 (e.g.
   * 104.133.9.80) or IPv6 (e.g. 2001:0db8:85a3:0000:0000:8a2e:0370:7334). This
   * should *not* be set when using the javascript pixel or if
   * `direct_user_request` is set. Used to extract location information for
   * personalization.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Optional. User agent as included in the HTTP header. UTF-8 encoded string
   * with a length limit of 1 KiB. This should *not* be set when using the
   * JavaScript pixel or if `directUserRequest` is set.
   *
   * @var string
   */
  public $userAgent;
  /**
   * Optional. Unique identifier for logged-in user with a length limit of 128
   * bytes. Required only for logged-in users. Don't set for anonymous users.
   * Don't set the field to the same fixed ID for different users. This mixes
   * the event history of those users together, which results in degraded model
   * quality.
   *
   * @var string
   */
  public $userId;
  /**
   * Required. A unique identifier for tracking visitors with a length limit of
   * 128 bytes. For example, this could be implemented with an HTTP cookie,
   * which should be able to uniquely identify a visitor on a single device.
   * This unique identifier should not change if the visitor logs in or out of
   * the website. Maximum length 128 bytes. Cannot be empty. Don't set the field
   * to the same fixed ID for different users. This mixes the event history of
   * those users together, which results in degraded model quality.
   *
   * @var string
   */
  public $visitorId;

  /**
   * Optional. Indicates if the request is made directly from the end user in
   * which case the user_agent and ip_address fields can be populated from the
   * HTTP request. This should *not* be set when using the javascript pixel.
   * This flag should be set only if the API request is made directly from the
   * end user such as a mobile app (and not if a gateway or a server is
   * processing and pushing the user events).
   *
   * @param bool $directUserRequest
   */
  public function setDirectUserRequest($directUserRequest)
  {
    $this->directUserRequest = $directUserRequest;
  }
  /**
   * @return bool
   */
  public function getDirectUserRequest()
  {
    return $this->directUserRequest;
  }
  /**
   * Optional. IP address of the user. This could be either IPv4 (e.g.
   * 104.133.9.80) or IPv6 (e.g. 2001:0db8:85a3:0000:0000:8a2e:0370:7334). This
   * should *not* be set when using the javascript pixel or if
   * `direct_user_request` is set. Used to extract location information for
   * personalization.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Optional. User agent as included in the HTTP header. UTF-8 encoded string
   * with a length limit of 1 KiB. This should *not* be set when using the
   * JavaScript pixel or if `directUserRequest` is set.
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
  /**
   * Optional. Unique identifier for logged-in user with a length limit of 128
   * bytes. Required only for logged-in users. Don't set for anonymous users.
   * Don't set the field to the same fixed ID for different users. This mixes
   * the event history of those users together, which results in degraded model
   * quality.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
  /**
   * Required. A unique identifier for tracking visitors with a length limit of
   * 128 bytes. For example, this could be implemented with an HTTP cookie,
   * which should be able to uniquely identify a visitor on a single device.
   * This unique identifier should not change if the visitor logs in or out of
   * the website. Maximum length 128 bytes. Cannot be empty. Don't set the field
   * to the same fixed ID for different users. This mixes the event history of
   * those users together, which results in degraded model quality.
   *
   * @param string $visitorId
   */
  public function setVisitorId($visitorId)
  {
    $this->visitorId = $visitorId;
  }
  /**
   * @return string
   */
  public function getVisitorId()
  {
    return $this->visitorId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1UserInfo::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1UserInfo');
