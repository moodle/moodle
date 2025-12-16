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

class TextOperatorOptions extends \Google\Model
{
  /**
   * If true, the text value is tokenized as one atomic value in operator
   * searches and facet matches. For example, if the operator name is "genre"
   * and the value is "science-fiction" the query restrictions "genre:science"
   * and "genre:fiction" doesn't match the item; "genre:science-fiction" does.
   * Text value matching is case-sensitive and does not remove special
   * characters. If false, the text is tokenized. For example, if the value is
   * "science-fiction" the queries "genre:science" and "genre:fiction" matches
   * the item.
   *
   * @var bool
   */
  public $exactMatchWithOperator;
  /**
   * Indicates the operator name required in the query in order to isolate the
   * text property. For example, if operatorName is *subject* and the property's
   * name is *subjectLine*, then queries like *subject:* show results only where
   * the value of the property named *subjectLine* matches **. By contrast, a
   * search that uses the same ** without an operator returns all items where **
   * matches the value of any text properties or text within the content field
   * for the item. The operator name can only contain lowercase letters (a-z).
   * The maximum length is 32 characters.
   *
   * @var string
   */
  public $operatorName;

  /**
   * If true, the text value is tokenized as one atomic value in operator
   * searches and facet matches. For example, if the operator name is "genre"
   * and the value is "science-fiction" the query restrictions "genre:science"
   * and "genre:fiction" doesn't match the item; "genre:science-fiction" does.
   * Text value matching is case-sensitive and does not remove special
   * characters. If false, the text is tokenized. For example, if the value is
   * "science-fiction" the queries "genre:science" and "genre:fiction" matches
   * the item.
   *
   * @param bool $exactMatchWithOperator
   */
  public function setExactMatchWithOperator($exactMatchWithOperator)
  {
    $this->exactMatchWithOperator = $exactMatchWithOperator;
  }
  /**
   * @return bool
   */
  public function getExactMatchWithOperator()
  {
    return $this->exactMatchWithOperator;
  }
  /**
   * Indicates the operator name required in the query in order to isolate the
   * text property. For example, if operatorName is *subject* and the property's
   * name is *subjectLine*, then queries like *subject:* show results only where
   * the value of the property named *subjectLine* matches **. By contrast, a
   * search that uses the same ** without an operator returns all items where **
   * matches the value of any text properties or text within the content field
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
class_alias(TextOperatorOptions::class, 'Google_Service_CloudSearch_TextOperatorOptions');
