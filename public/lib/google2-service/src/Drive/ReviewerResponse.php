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

namespace Google\Service\Drive;

class ReviewerResponse extends \Google\Model
{
  /**
   * Response was set to an unrecognized value.
   */
  public const RESPONSE_RESPONSE_UNSPECIFIED = 'RESPONSE_UNSPECIFIED';
  /**
   * The reviewer has not yet responded
   */
  public const RESPONSE_NO_RESPONSE = 'NO_RESPONSE';
  /**
   * The Reviewer has approved the item.
   */
  public const RESPONSE_APPROVED = 'APPROVED';
  /**
   * The Reviewer has declined the item.
   */
  public const RESPONSE_DECLINED = 'DECLINED';
  /**
   * This is always drive#reviewerResponse.
   *
   * @var string
   */
  public $kind;
  /**
   * A Reviewer’s Response for the Approval.
   *
   * @var string
   */
  public $response;
  protected $reviewerType = User::class;
  protected $reviewerDataType = '';

  /**
   * This is always drive#reviewerResponse.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A Reviewer’s Response for the Approval.
   *
   * Accepted values: RESPONSE_UNSPECIFIED, NO_RESPONSE, APPROVED, DECLINED
   *
   * @param self::RESPONSE_* $response
   */
  public function setResponse($response)
  {
    $this->response = $response;
  }
  /**
   * @return self::RESPONSE_*
   */
  public function getResponse()
  {
    return $this->response;
  }
  /**
   * The user that is responsible for this response.
   *
   * @param User $reviewer
   */
  public function setReviewer(User $reviewer)
  {
    $this->reviewer = $reviewer;
  }
  /**
   * @return User
   */
  public function getReviewer()
  {
    return $this->reviewer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReviewerResponse::class, 'Google_Service_Drive_ReviewerResponse');
