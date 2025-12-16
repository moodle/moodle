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

namespace Google\Service\NetworkServices;

class ExtensionChainMatchCondition extends \Google\Model
{
  /**
   * Required. A Common Expression Language (CEL) expression that is used to
   * match requests for which the extension chain is executed. For more
   * information, see [CEL matcher language
   * reference](https://cloud.google.com/service-extensions/docs/cel-matcher-
   * language-reference).
   *
   * @var string
   */
  public $celExpression;

  /**
   * Required. A Common Expression Language (CEL) expression that is used to
   * match requests for which the extension chain is executed. For more
   * information, see [CEL matcher language
   * reference](https://cloud.google.com/service-extensions/docs/cel-matcher-
   * language-reference).
   *
   * @param string $celExpression
   */
  public function setCelExpression($celExpression)
  {
    $this->celExpression = $celExpression;
  }
  /**
   * @return string
   */
  public function getCelExpression()
  {
    return $this->celExpression;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtensionChainMatchCondition::class, 'Google_Service_NetworkServices_ExtensionChainMatchCondition');
