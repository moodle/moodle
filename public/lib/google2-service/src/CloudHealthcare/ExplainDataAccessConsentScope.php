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

namespace Google\Service\CloudHealthcare;

class ExplainDataAccessConsentScope extends \Google\Collection
{
  /**
   * Unspecified consent decision type.
   */
  public const DECISION_CONSENT_DECISION_TYPE_UNSPECIFIED = 'CONSENT_DECISION_TYPE_UNSPECIFIED';
  /**
   * Consent permitted access.
   */
  public const DECISION_CONSENT_DECISION_TYPE_PERMIT = 'CONSENT_DECISION_TYPE_PERMIT';
  /**
   * Consent denied access.
   */
  public const DECISION_CONSENT_DECISION_TYPE_DENY = 'CONSENT_DECISION_TYPE_DENY';
  protected $collection_key = 'exceptions';
  protected $accessorScopeType = ConsentAccessorScope::class;
  protected $accessorScopeDataType = '';
  /**
   * Whether the current consent scope is permitted or denied access on the
   * requested resource.
   *
   * @var string
   */
  public $decision;
  protected $enforcingConsentsType = ExplainDataAccessConsentInfo::class;
  protected $enforcingConsentsDataType = 'array';
  protected $exceptionsType = ExplainDataAccessConsentScope::class;
  protected $exceptionsDataType = 'array';

  /**
   * The accessor scope that describes who can access, for what purpose, and in
   * which environment.
   *
   * @param ConsentAccessorScope $accessorScope
   */
  public function setAccessorScope(ConsentAccessorScope $accessorScope)
  {
    $this->accessorScope = $accessorScope;
  }
  /**
   * @return ConsentAccessorScope
   */
  public function getAccessorScope()
  {
    return $this->accessorScope;
  }
  /**
   * Whether the current consent scope is permitted or denied access on the
   * requested resource.
   *
   * Accepted values: CONSENT_DECISION_TYPE_UNSPECIFIED,
   * CONSENT_DECISION_TYPE_PERMIT, CONSENT_DECISION_TYPE_DENY
   *
   * @param self::DECISION_* $decision
   */
  public function setDecision($decision)
  {
    $this->decision = $decision;
  }
  /**
   * @return self::DECISION_*
   */
  public function getDecision()
  {
    return $this->decision;
  }
  /**
   * Metadata of the consent resources that enforce the consent scope's access.
   *
   * @param ExplainDataAccessConsentInfo[] $enforcingConsents
   */
  public function setEnforcingConsents($enforcingConsents)
  {
    $this->enforcingConsents = $enforcingConsents;
  }
  /**
   * @return ExplainDataAccessConsentInfo[]
   */
  public function getEnforcingConsents()
  {
    return $this->enforcingConsents;
  }
  /**
   * Other consent scopes that created exceptions within this scope.
   *
   * @param ExplainDataAccessConsentScope[] $exceptions
   */
  public function setExceptions($exceptions)
  {
    $this->exceptions = $exceptions;
  }
  /**
   * @return ExplainDataAccessConsentScope[]
   */
  public function getExceptions()
  {
    return $this->exceptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExplainDataAccessConsentScope::class, 'Google_Service_CloudHealthcare_ExplainDataAccessConsentScope');
