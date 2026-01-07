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

class CheckDataAccessRequest extends \Google\Model
{
  /**
   * No response view specified. The API will default to the BASIC view.
   */
  public const RESPONSE_VIEW_RESPONSE_VIEW_UNSPECIFIED = 'RESPONSE_VIEW_UNSPECIFIED';
  /**
   * Only the `consented` field is populated in CheckDataAccessResponse.
   */
  public const RESPONSE_VIEW_BASIC = 'BASIC';
  /**
   * All fields within CheckDataAccessResponse are populated. When set to
   * `FULL`, all `ACTIVE` Consents are evaluated even if a matching policy is
   * found during evaluation.
   */
  public const RESPONSE_VIEW_FULL = 'FULL';
  protected $consentListType = ConsentList::class;
  protected $consentListDataType = '';
  /**
   * Required. The unique identifier of the resource to check access for. This
   * identifier must correspond to a User data mapping in the given consent
   * store.
   *
   * @var string
   */
  public $dataId;
  /**
   * The values of request attributes associated with this access request.
   *
   * @var string[]
   */
  public $requestAttributes;
  /**
   * Optional. The view for CheckDataAccessResponse. If unspecified, defaults to
   * `BASIC` and returns `consented` as `TRUE` or `FALSE`.
   *
   * @var string
   */
  public $responseView;

  /**
   * Optional. Specific Consents to evaluate the access request against. These
   * Consents must have the same `user_id` as the evaluated User data mapping,
   * must exist in the current `consent_store`, and have a `state` of either
   * `ACTIVE` or `DRAFT`. A maximum of 100 Consents can be provided here. If no
   * selection is specified, the access request is evaluated against all
   * `ACTIVE` unexpired Consents with the same `user_id` as the evaluated User
   * data mapping.
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
   * Required. The unique identifier of the resource to check access for. This
   * identifier must correspond to a User data mapping in the given consent
   * store.
   *
   * @param string $dataId
   */
  public function setDataId($dataId)
  {
    $this->dataId = $dataId;
  }
  /**
   * @return string
   */
  public function getDataId()
  {
    return $this->dataId;
  }
  /**
   * The values of request attributes associated with this access request.
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
   * Optional. The view for CheckDataAccessResponse. If unspecified, defaults to
   * `BASIC` and returns `consented` as `TRUE` or `FALSE`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckDataAccessRequest::class, 'Google_Service_CloudHealthcare_CheckDataAccessRequest');
