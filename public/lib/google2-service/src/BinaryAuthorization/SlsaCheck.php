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

class SlsaCheck extends \Google\Collection
{
  protected $collection_key = 'rules';
  protected $rulesType = VerificationRule::class;
  protected $rulesDataType = 'array';

  /**
   * Specifies a list of verification rules for the SLSA attestations. An image
   * is considered compliant with the SlsaCheck if any of the rules are
   * satisfied.
   *
   * @param VerificationRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return VerificationRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SlsaCheck::class, 'Google_Service_BinaryAuthorization_SlsaCheck');
