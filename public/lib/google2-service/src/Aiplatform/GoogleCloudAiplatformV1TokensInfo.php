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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1TokensInfo extends \Google\Collection
{
  protected $collection_key = 'tokens';
  /**
   * Optional. Optional fields for the role from the corresponding Content.
   *
   * @var string
   */
  public $role;
  /**
   * A list of token ids from the input.
   *
   * @var string[]
   */
  public $tokenIds;
  /**
   * A list of tokens from the input.
   *
   * @var string[]
   */
  public $tokens;

  /**
   * Optional. Optional fields for the role from the corresponding Content.
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * A list of token ids from the input.
   *
   * @param string[] $tokenIds
   */
  public function setTokenIds($tokenIds)
  {
    $this->tokenIds = $tokenIds;
  }
  /**
   * @return string[]
   */
  public function getTokenIds()
  {
    return $this->tokenIds;
  }
  /**
   * A list of tokens from the input.
   *
   * @param string[] $tokens
   */
  public function setTokens($tokens)
  {
    $this->tokens = $tokens;
  }
  /**
   * @return string[]
   */
  public function getTokens()
  {
    return $this->tokens;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TokensInfo::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TokensInfo');
