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

namespace Google\Service\Forms;

class TextLink extends \Google\Model
{
  /**
   * Required. Display text for the URI.
   *
   * @var string
   */
  public $displayText;
  /**
   * Required. The URI.
   *
   * @var string
   */
  public $uri;

  /**
   * Required. Display text for the URI.
   *
   * @param string $displayText
   */
  public function setDisplayText($displayText)
  {
    $this->displayText = $displayText;
  }
  /**
   * @return string
   */
  public function getDisplayText()
  {
    return $this->displayText;
  }
  /**
   * Required. The URI.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextLink::class, 'Google_Service_Forms_TextLink');
