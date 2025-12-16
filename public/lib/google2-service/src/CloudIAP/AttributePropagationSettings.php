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

namespace Google\Service\CloudIAP;

class AttributePropagationSettings extends \Google\Collection
{
  protected $collection_key = 'outputCredentials';
  /**
   * Optional. Whether the provided attribute propagation settings should be
   * evaluated on user requests. If set to true, attributes returned from the
   * expression will be propagated in the set output credentials.
   *
   * @var bool
   */
  public $enable;
  /**
   * Optional. Raw string CEL expression. Must return a list of attributes. A
   * maximum of 45 attributes can be selected. Expressions can select different
   * attribute types from `attributes`: `attributes.saml_attributes`,
   * `attributes.iap_attributes`. The following functions are supported: -
   * filter `.filter(, )`: Returns a subset of `` where `` is true for every
   * item. - in ` in `: Returns true if `` contains ``. - selectByName
   * `.selectByName()`: Returns the attribute in `` with the given `` name,
   * otherwise returns empty. - emitAs `.emitAs()`: Sets the `` name field to
   * the given `` for propagation in selected output credentials. - strict
   * `.strict()`: Ignores the `x-goog-iap-attr-` prefix for the provided `` when
   * propagating with the `HEADER` output credential, such as request headers. -
   * append `.append()` OR `.append()`: Appends the provided `` or `` to the end
   * of ``. Example expression: `attributes.saml_attributes.filter(x, x.name in
   * ['test']).append(attributes.iap_attributes.selectByName('exact').emitAs('cu
   * stom').strict())`
   *
   * @var string
   */
  public $expression;
  /**
   * Optional. Which output credentials attributes selected by the CEL
   * expression should be propagated in. All attributes will be fully duplicated
   * in each selected output credential.
   *
   * @var string[]
   */
  public $outputCredentials;

  /**
   * Optional. Whether the provided attribute propagation settings should be
   * evaluated on user requests. If set to true, attributes returned from the
   * expression will be propagated in the set output credentials.
   *
   * @param bool $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return bool
   */
  public function getEnable()
  {
    return $this->enable;
  }
  /**
   * Optional. Raw string CEL expression. Must return a list of attributes. A
   * maximum of 45 attributes can be selected. Expressions can select different
   * attribute types from `attributes`: `attributes.saml_attributes`,
   * `attributes.iap_attributes`. The following functions are supported: -
   * filter `.filter(, )`: Returns a subset of `` where `` is true for every
   * item. - in ` in `: Returns true if `` contains ``. - selectByName
   * `.selectByName()`: Returns the attribute in `` with the given `` name,
   * otherwise returns empty. - emitAs `.emitAs()`: Sets the `` name field to
   * the given `` for propagation in selected output credentials. - strict
   * `.strict()`: Ignores the `x-goog-iap-attr-` prefix for the provided `` when
   * propagating with the `HEADER` output credential, such as request headers. -
   * append `.append()` OR `.append()`: Appends the provided `` or `` to the end
   * of ``. Example expression: `attributes.saml_attributes.filter(x, x.name in
   * ['test']).append(attributes.iap_attributes.selectByName('exact').emitAs('cu
   * stom').strict())`
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
  /**
   * Optional. Which output credentials attributes selected by the CEL
   * expression should be propagated in. All attributes will be fully duplicated
   * in each selected output credential.
   *
   * @param string[] $outputCredentials
   */
  public function setOutputCredentials($outputCredentials)
  {
    $this->outputCredentials = $outputCredentials;
  }
  /**
   * @return string[]
   */
  public function getOutputCredentials()
  {
    return $this->outputCredentials;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttributePropagationSettings::class, 'Google_Service_CloudIAP_AttributePropagationSettings');
