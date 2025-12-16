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

namespace Google\Service\CloudHealthcare;

class EvaluateUserConsentsRequest extends \Google\Model
{
  /**
   * No response view specified. The API will default to the BASIC view.
   */
  public const RESPONSE_VIEW_RESPONSE_VIEW_UNSPECIFIED = 'RESPONSE_VIEW_UNSPECIFIED';
  /**
   * Only the `data_id` and `consented` fields are populated in the response.
   */
  public const RESPONSE_VIEW_BASIC = 'BASIC';
  /**
   * All fields within the response are populated. When set to `FULL`, all
   * `ACTIVE` Consents are evaluated even if a matching policy is found during
   * evaluation.
   */
  public const RESPONSE_VIEW_FULL = 'FULL';
  protected $consentListType = ConsentList::class;
  protected $consentListDataType = '';
  /**
   * Optional. Limit on the number of User data mappings to return in a single
   * response. If not specified, 100 is used. May not be larger than 1000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. Token to retrieve the next page of results, or empty to get the
   * first page.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. The values of request attributes associated with this access
   * request.
   *
   * @var string[]
   */
  public $requestAttributes;
  /**
   * Optional. The values of resource attributes associated with the resources
   * being requested. If no values are specified, then all resources are
   * queried.
   *
   * @var string[]
   */
  public $resourceAttributes;
  /**
   * Optional. The view for EvaluateUserConsentsResponse. If unspecified,
   * defaults to `BASIC` and returns `consented` as `TRUE` or `FALSE`.
   *
   * @var string
   */
  public $responseView;
  /**
   * Required. User ID to evaluate consents for.
   *
   * @var string
   */
  public $userId;

  /**
   * Optional. Specific Consents to evaluate the access request against. These
   * Consents must have the same `user_id` as the User data mappings being
   * evalauted, must exist in the current `consent_store`, and must have a
   * `state` of either `ACTIVE` or `DRAFT`. A maximum of 100 Consents can be
   * provided here. If unspecified, all `ACTIVE` unexpired Consents in the
   * current `consent_store` will be evaluated.
   *
   * @param ConsentList $consentList
   */
  public function setConsentList(ConsentList $consentList)
  {
    $this->consentList = $consentList;
  }
  /**
   * @return ConsentList
   */
  public function getConsentList()
  {
    return $this->consentList;
  }
  /**
   * Optional. Limit on the number of User data mappings to return in a single
   * response. If not specified, 100 is used. May not be larger than 1000.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Optional. Token to retrieve the next page of results, or empty to get the
   * first page.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Required. The values of request attributes associated with this access
   * request.
   *
   * @param string[] $requestAttributes
   */
  public function setRequestAttributes($requestAttributes)
  {
    $this->requestAttributes = $requestAttributes;
  }
  /**
   * @return string[]
   */
  public function getRequestAttributes()
  {
    return $this->requestAttributes;
  }
  /**
   * Optional. The values of resource attributes associated with the resources
   * being requested. If no values are specified, then all resources are
   * queried.
   *
   * @param string[] $resourceAttributes
   */
  public function setResourceAttributes($resourceAttributes)
  {
    $this->resourceAttributes = $resourceAttributes;
  }
  /**
   * @return string[]
   */
  public function getResourceAttributes()
  {
    return $this->resourceAttributes;
  }
  /**
   * Optional. The view for EvaluateUserConsentsResponse. If unspecified,
   * defaults to `BASIC` and returns `consented` as `TRUE` or `FALSE`.
   *
   * Accepted values: RESPONSE_VIEW_UNSPECIFIED, BASIC, FULL
   *
   * @param self::RESPONSE_VIEW_* $responseView
   */
  public function setResponseView($responseView)
  {
    $this->responseView = $responseView;
  }
  /**
   * @return self::RESPONSE_VIEW_*
   */
  public function getResponseView()
  {
    return $this->responseView;
  }
  /**
   * Required. User ID to evaluate consents for.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EvaluateUserConsentsRequest::class, 'Google_Service_CloudHealthcare_EvaluateUserConsentsRequest');
