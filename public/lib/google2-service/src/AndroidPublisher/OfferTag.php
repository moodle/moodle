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

class OfferTag extends \Google\Model
{
  /**
   * Must conform with RFC-1034. That is, this string can only contain lower-
   * case letters (a-z), numbers (0-9), and hyphens (-), and be at most 20
   * characters.
   *
   * @var string
   */
  public $tag;

  /**
   * Must conform with RFC-1034. That is, this string can only contain lower-
   * case letters (a-z), numbers (0-9), and hyphens (-), and be at most 20
   * characters.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OfferTag::class, 'Google_Service_AndroidPublisher_OfferTag');
