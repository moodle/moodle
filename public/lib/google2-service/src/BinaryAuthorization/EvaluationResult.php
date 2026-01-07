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

class EvaluationResult extends \Google\Model
{
  /**
   * Not specified. This should never be used.
   */
  public const VERDICT_CHECK_VERDICT_UNSPECIFIED = 'CHECK_VERDICT_UNSPECIFIED';
  /**
   * The check was successfully evaluated and the image satisfied the check.
   */
  public const VERDICT_CONFORMANT = 'CONFORMANT';
  /**
   * The check was successfully evaluated and the image did not satisfy the
   * check.
   */
  public const VERDICT_NON_CONFORMANT = 'NON_CONFORMANT';
  /**
   * The check was not successfully evaluated.
   */
  public const VERDICT_ERROR = 'ERROR';
  /**
   * The result of evaluating this check.
   *
   * @var string
   */
  public $verdict;

  /**
   * The result of evaluating this check.
   *
   * Accepted values: CHECK_VERDICT_UNSPECIFIED, CONFORMANT, NON_CONFORMANT,
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
class_alias(EvaluationResult::class, 'Google_Service_BinaryAuthorization_EvaluationResult');
