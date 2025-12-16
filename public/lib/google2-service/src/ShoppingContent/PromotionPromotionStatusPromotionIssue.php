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

namespace Google\Service\ShoppingContent;

class PromotionPromotionStatusPromotionIssue extends \Google\Model
{
  /**
   * Code of the issue.
   *
   * @var string
   */
  public $code;
  /**
   * Explanation of the issue.
   *
   * @var string
   */
  public $detail;

  /**
   * Code of the issue.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Explanation of the issue.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PromotionPromotionStatusPromotionIssue::class, 'Google_Service_ShoppingContent_PromotionPromotionStatusPromotionIssue');
