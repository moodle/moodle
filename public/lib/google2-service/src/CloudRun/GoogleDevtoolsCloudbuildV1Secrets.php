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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1Secrets extends \Google\Collection
{
  protected $collection_key = 'secretManager';
  protected $inlineType = GoogleDevtoolsCloudbuildV1InlineSecret::class;
  protected $inlineDataType = 'array';
  protected $secretManagerType = GoogleDevtoolsCloudbuildV1SecretManagerSecret::class;
  protected $secretManagerDataType = 'array';

  /**
   * Secrets encrypted with KMS key and the associated secret environment
   * variable.
   *
   * @param GoogleDevtoolsCloudbuildV1InlineSecret[] $inline
   */
  public function setInline($inline)
  {
    $this->inline = $inline;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1InlineSecret[]
   */
  public function getInline()
  {
    return $this->inline;
  }
  /**
   * Secrets in Secret Manager and associated secret environment variable.
   *
   * @param GoogleDevtoolsCloudbuildV1SecretManagerSecret[] $secretManager
   */
  public function setSecretManager($secretManager)
  {
    $this->secretManager = $secretManager;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1SecretManagerSecret[]
   */
  public function getSecretManager()
  {
    return $this->secretManager;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1Secrets::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1Secrets');
