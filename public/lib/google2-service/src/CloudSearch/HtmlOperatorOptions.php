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

namespace Google\Service\CloudSearch;

class HtmlOperatorOptions extends \Google\Model
{
  /**
   * Indicates the operator name required in the query in order to isolate the
   * html property. For example, if operatorName is *subject* and the property's
   * name is *subjectLine*, then queries like *subject:* show results only where
   * the value of the property named *subjectLine* matches **. By contrast, a
   * search that uses the same ** without an operator return all items where **
   * matches the value of any html properties or text within the content field
   * for the item. The operator name can only contain lowercase letters (a-z).
   * The maximum length is 32 characters.
   *
   * @var string
   */
  public $operatorName;

  /**
   * Indicates the operator name required in the query in order to isolate the
   * html property. For example, if operatorName is *subject* and the property's
   * name is *subjectLine*, then queries like *subject:* show results only where
   * the value of the property named *subjectLine* matches **. By contrast, a
   * search that uses the same ** without an operator return all items where **
   * matches the value of any html properties or text within the content field
   * for the item. The operator name can only contain lowercase letters (a-z).
   * The maximum length is 32 characters.
   *
   * @param string $operatorName
   */
  public function setOperatorName($operatorName)
  {
    $this->operatorName = $operatorName;
  }
  /**
   * @return string
   */
  public function getOperatorName()
  {
    return $this->operatorName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HtmlOperatorOptions::class, 'Google_Service_CloudSearch_HtmlOperatorOptions');
