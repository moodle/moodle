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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ToolAnnotations extends \Google\Model
{
  /**
   * Optional. Additional hints which may help tools and not covered in
   * defaults.
   *
   * @var string[]
   */
  public $additionalHints;
  /**
   * Optional. Hint indicating if the tool may have destructive side effects.
   *
   * @var bool
   */
  public $destructiveHint;
  /**
   * Optional. Hint indicating if the tool is idempotent.
   *
   * @var bool
   */
  public $idempotentHint;
  /**
   * Optional. Hint indicating if the tool interacts with the open world (e.g.,
   * internet).
   *
   * @var bool
   */
  public $openWorldHint;
  /**
   * Optional. Hint indicating if the tool is read-only.
   *
   * @var bool
   */
  public $readOnlyHint;
  /**
   * Optional. A human-readable title for the tool (if different from
   * Tool.title).
   *
   * @var string
   */
  public $title;

  /**
   * Optional. Additional hints which may help tools and not covered in
   * defaults.
   *
   * @param string[] $additionalHints
   */
  public function setAdditionalHints($additionalHints)
  {
    $this->additionalHints = $additionalHints;
  }
  /**
   * @return string[]
   */
  public function getAdditionalHints()
  {
    return $this->additionalHints;
  }
  /**
   * Optional. Hint indicating if the tool may have destructive side effects.
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
   * Optional. Hint indicating if the tool is idempotent.
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
   * Optional. Hint indicating if the tool interacts with the open world (e.g.,
   * internet).
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
   * Optional. Hint indicating if the tool is read-only.
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
   * Optional. A human-readable title for the tool (if different from
   * Tool.title).
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
class_alias(GoogleCloudApihubV1ToolAnnotations::class, 'Google_Service_APIhub_GoogleCloudApihubV1ToolAnnotations');
