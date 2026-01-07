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

namespace Google\Service\BinaryAuthorization;

class ImageResult extends \Google\Model
{
  /**
   * Not specified. This should never be used.
   */
  public const VERDICT_IMAGE_VERDICT_UNSPECIFIED = 'IMAGE_VERDICT_UNSPECIFIED';
  /**
   * Image conforms to the policy.
   */
  public const VERDICT_CONFORMANT = 'CONFORMANT';
  /**
   * Image does not conform to the policy.
   */
  public const VERDICT_NON_CONFORMANT = 'NON_CONFORMANT';
  /**
   * Error evaluating the image. Non-conformance has precedence over errors.
   */
  public const VERDICT_ERROR = 'ERROR';
  protected $allowlistResultType = AllowlistResult::class;
  protected $allowlistResultDataType = '';
  protected $checkSetResultType = CheckSetResult::class;
  protected $checkSetResultDataType = '';
  /**
   * Explanation of this image result. Only populated if no check sets were
   * evaluated.
   *
   * @var string
   */
  public $explanation;
  /**
   * Image URI from the request.
   *
   * @var string
   */
  public $imageUri;
  /**
   * The result of evaluating this image.
   *
   * @var string
   */
  public $verdict;

  /**
   * If the image was exempted by a top-level allow_pattern, contains the
   * allowlist pattern that the image name matched.
   *
   * @param AllowlistResult $allowlistResult
   */
  public function setAllowlistResult(AllowlistResult $allowlistResult)
  {
    $this->allowlistResult = $allowlistResult;
  }
  /**
   * @return AllowlistResult
   */
  public function getAllowlistResult()
  {
    return $this->allowlistResult;
  }
  /**
   * If a check set was evaluated, contains the result of the check set. Empty
   * if there were no check sets.
   *
   * @param CheckSetResult $checkSetResult
   */
  public function setCheckSetResult(CheckSetResult $checkSetResult)
  {
    $this->checkSetResult = $checkSetResult;
  }
  /**
   * @return CheckSetResult
   */
  public function getCheckSetResult()
  {
    return $this->checkSetResult;
  }
  /**
   * Explanation of this image result. Only populated if no check sets were
   * evaluated.
   *
   * @param string $explanation
   */
  public function setExplanation($explanation)
  {
    $this->explanation = $explanation;
  }
  /**
   * @return string
   */
  public function getExplanation()
  {
    return $this->explanation;
  }
  /**
   * Image URI from the request.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * The result of evaluating this image.
   *
   * Accepted values: IMAGE_VERDICT_UNSPECIFIED, CONFORMANT, NON_CONFORMANT,
   * ERROR
   *
   * @param self::VERDICT_* $verdict
   */
  public function setVerdict($verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return self::VERDICT_*
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageResult::class, 'Google_Service_BinaryAuthorization_ImageResult');
