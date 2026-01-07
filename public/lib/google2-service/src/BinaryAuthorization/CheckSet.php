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

class CheckSet extends \Google\Collection
{
  protected $collection_key = 'checks';
  protected $checksType = Check::class;
  protected $checksDataType = 'array';
  /**
   * Optional. A user-provided name for this `CheckSet`. This field has no
   * effect on the policy evaluation behavior except to improve readability of
   * messages in evaluation results.
   *
   * @var string
   */
  public $displayName;
  protected $imageAllowlistType = ImageAllowlist::class;
  protected $imageAllowlistDataType = '';
  protected $scopeType = Scope::class;
  protected $scopeDataType = '';

  /**
   * Optional. The checks to apply. The ultimate result of evaluating the check
   * set will be "allow" if and only if every check in `checks` evaluates to
   * "allow". If `checks` is empty, the default behavior is "always allow".
   *
   * @param Check[] $checks
   */
  public function setChecks($checks)
  {
    $this->checks = $checks;
  }
  /**
   * @return Check[]
   */
  public function getChecks()
  {
    return $this->checks;
  }
  /**
   * Optional. A user-provided name for this `CheckSet`. This field has no
   * effect on the policy evaluation behavior except to improve readability of
   * messages in evaluation results.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Images exempted from this `CheckSet`. If any of the patterns
   * match the image being evaluated, no checks in the `CheckSet` will be
   * evaluated.
   *
   * @param ImageAllowlist $imageAllowlist
   */
  public function setImageAllowlist(ImageAllowlist $imageAllowlist)
  {
    $this->imageAllowlist = $imageAllowlist;
  }
  /**
   * @return ImageAllowlist
   */
  public function getImageAllowlist()
  {
    return $this->imageAllowlist;
  }
  /**
   * Optional. The scope to which this `CheckSet` applies. If unset or an empty
   * string (the default), applies to all namespaces and service accounts. See
   * the `Scope` message documentation for details on scoping rules.
   *
   * @param Scope $scope
   */
  public function setScope(Scope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return Scope
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckSet::class, 'Google_Service_BinaryAuthorization_CheckSet');
