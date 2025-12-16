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

namespace Google\Service\Connectors;

class ToolAnnotations extends \Google\Model
{
  /**
   * If true, the tool may perform destructive updates to its environment. If
   * false, the tool performs only additive updates. (This property is
   * meaningful only when `read_only_hint == false`)
   *
   * @var bool
   */
  public $destructiveHint;
  /**
   * If true, calling the tool repeatedly with the same arguments will have no
   * additional effect on the environment. (This property is meaningful only
   * when `read_only_hint == false`)
   *
   * @var bool
   */
  public $idempotentHint;
  /**
   * If true, this tool may interact with an "open world" of external entities.
   * If false, the tool's domain of interaction is closed. For example, the
   * world of a web search tool is open, whereas that of a memory tool is not.
   *
   * @var bool
   */
  public $openWorldHint;
  /**
   * If true, the tool does not modify its environment.
   *
   * @var bool
   */
  public $readOnlyHint;
  /**
   * A human-readable title for the tool.
   *
   * @var string
   */
  public $title;

  /**
   * If true, the tool may perform destructive updates to its environment. If
   * false, the tool performs only additive updates. (This property is
   * meaningful only when `read_only_hint == false`)
   *
   * @param bool $destructiveHint
   */
  public function setDestructiveHint($destructiveHint)
  {
    $this->destructiveHint = $destructiveHint;
  }
  /**
   * @return bool
   */
  public function getDestructiveHint()
  {
    return $this->destructiveHint;
  }
  /**
   * If true, calling the tool repeatedly with the same arguments will have no
   * additional effect on the environment. (This property is meaningful only
   * when `read_only_hint == false`)
   *
   * @param bool $idempotentHint
   */
  public function setIdempotentHint($idempotentHint)
  {
    $this->idempotentHint = $idempotentHint;
  }
  /**
   * @return bool
   */
  public function getIdempotentHint()
  {
    return $this->idempotentHint;
  }
  /**
   * If true, this tool may interact with an "open world" of external entities.
   * If false, the tool's domain of interaction is closed. For example, the
   * world of a web search tool is open, whereas that of a memory tool is not.
   *
   * @param bool $openWorldHint
   */
  public function setOpenWorldHint($openWorldHint)
  {
    $this->openWorldHint = $openWorldHint;
  }
  /**
   * @return bool
   */
  public function getOpenWorldHint()
  {
    return $this->openWorldHint;
  }
  /**
   * If true, the tool does not modify its environment.
   *
   * @param bool $readOnlyHint
   */
  public function setReadOnlyHint($readOnlyHint)
  {
    $this->readOnlyHint = $readOnlyHint;
  }
  /**
   * @return bool
   */
  public function getReadOnlyHint()
  {
    return $this->readOnlyHint;
  }
  /**
   * A human-readable title for the tool.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ToolAnnotations::class, 'Google_Service_Connectors_ToolAnnotations');
