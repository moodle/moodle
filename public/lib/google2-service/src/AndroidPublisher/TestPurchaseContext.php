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

namespace Google\Service\AndroidPublisher;

class TestPurchaseContext extends \Google\Model
{
  /**
   * Fop type unspecified. This value should never be set.
   */
  public const FOP_TYPE_FOP_TYPE_UNSPECIFIED = 'FOP_TYPE_UNSPECIFIED';
  /**
   * The purchase was made using a test card.
   */
  public const FOP_TYPE_TEST = 'TEST';
  /**
   * The fop type of the test purchase.
   *
   * @var string
   */
  public $fopType;

  /**
   * The fop type of the test purchase.
   *
   * Accepted values: FOP_TYPE_UNSPECIFIED, TEST
   *
   * @param self::FOP_TYPE_* $fopType
   */
  public function setFopType($fopType)
  {
    $this->fopType = $fopType;
  }
  /**
   * @return self::FOP_TYPE_*
   */
  public function getFopType()
  {
    return $this->fopType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestPurchaseContext::class, 'Google_Service_AndroidPublisher_TestPurchaseContext');
