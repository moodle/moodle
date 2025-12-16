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

namespace Google\Service\ContainerAnalysis;

class License extends \Google\Model
{
  /**
   * Comments
   *
   * @var string
   */
  public $comments;
  /**
   * Often a single license can be used to represent the licensing terms.
   * Sometimes it is necessary to include a choice of one or more licenses or
   * some combination of license identifiers. Examples: "LGPL-2.1-only OR MIT",
   * "LGPL-2.1-only AND MIT", "GPL-2.0-or-later WITH Bison-exception-2.2".
   *
   * @var string
   */
  public $expression;

  /**
   * Comments
   *
   * @param string $comments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;
  }
  /**
   * @return string
   */
  public function getComments()
  {
    return $this->comments;
  }
  /**
   * Often a single license can be used to represent the licensing terms.
   * Sometimes it is necessary to include a choice of one or more licenses or
   * some combination of license identifiers. Examples: "LGPL-2.1-only OR MIT",
   * "LGPL-2.1-only AND MIT", "GPL-2.0-or-later WITH Bison-exception-2.2".
   *
   * @param string $expression
   */
  public function setExpression($expression)
  {
    $this->expression = $expression;
  }
  /**
   * @return string
   */
  public function getExpression()
  {
    return $this->expression;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(License::class, 'Google_Service_ContainerAnalysis_License');
