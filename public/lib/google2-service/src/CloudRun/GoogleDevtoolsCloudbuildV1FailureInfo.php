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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1FailureInfo extends \Google\Model
{
  /**
   * Type unspecified
   */
  public const TYPE_FAILURE_TYPE_UNSPECIFIED = 'FAILURE_TYPE_UNSPECIFIED';
  /**
   * Unable to push the image to the repository.
   */
  public const TYPE_PUSH_FAILED = 'PUSH_FAILED';
  /**
   * Final image not found.
   */
  public const TYPE_PUSH_IMAGE_NOT_FOUND = 'PUSH_IMAGE_NOT_FOUND';
  /**
   * Unauthorized push of the final image.
   */
  public const TYPE_PUSH_NOT_AUTHORIZED = 'PUSH_NOT_AUTHORIZED';
  /**
   * Backend logging failures. Should retry.
   */
  public const TYPE_LOGGING_FAILURE = 'LOGGING_FAILURE';
  /**
   * A build step has failed.
   */
  public const TYPE_USER_BUILD_STEP = 'USER_BUILD_STEP';
  /**
   * The source fetching has failed.
   */
  public const TYPE_FETCH_SOURCE_FAILED = 'FETCH_SOURCE_FAILED';
  /**
   * Explains the failure issue in more detail using hard-coded text.
   *
   * @var string
   */
  public $detail;
  /**
   * The name of the failure.
   *
   * @var string
   */
  public $type;

  /**
   * Explains the failure issue in more detail using hard-coded text.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * The name of the failure.
   *
   * Accepted values: FAILURE_TYPE_UNSPECIFIED, PUSH_FAILED,
   * PUSH_IMAGE_NOT_FOUND, PUSH_NOT_AUTHORIZED, LOGGING_FAILURE,
   * USER_BUILD_STEP, FETCH_SOURCE_FAILED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1FailureInfo::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1FailureInfo');
