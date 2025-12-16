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

namespace Google\Service\ServiceManagement;

class DocumentationRule extends \Google\Model
{
  /**
   * Deprecation description of the selected element(s). It can be provided if
   * an element is marked as `deprecated`.
   *
   * @var string
   */
  public $deprecationDescription;
  /**
   * Description of the selected proto element (e.g. a message, a method, a
   * 'service' definition, or a field). Defaults to leading & trailing comments
   * taken from the proto source definition of the proto element.
   *
   * @var string
   */
  public $description;
  /**
   * String of comma or space separated case-sensitive words for which
   * method/field name replacement will be disabled.
   *
   * @var string
   */
  public $disableReplacementWords;
  /**
   * The selector is a comma-separated list of patterns for any element such as
   * a method, a field, an enum value. Each pattern is a qualified name of the
   * element which may end in "*", indicating a wildcard. Wildcards are only
   * allowed at the end and for a whole component of the qualified name, i.e.
   * "foo.*" is ok, but not "foo.b*" or "foo.*.bar". A wildcard will match one
   * or more components. To specify a default for all applicable elements, the
   * whole pattern "*" is used.
   *
   * @var string
   */
  public $selector;

  /**
   * Deprecation description of the selected element(s). It can be provided if
   * an element is marked as `deprecated`.
   *
   * @param string $deprecationDescription
   */
  public function setDeprecationDescription($deprecationDescription)
  {
    $this->deprecationDescription = $deprecationDescription;
  }
  /**
   * @return string
   */
  public function getDeprecationDescription()
  {
    return $this->deprecationDescription;
  }
  /**
   * Description of the selected proto element (e.g. a message, a method, a
   * 'service' definition, or a field). Defaults to leading & trailing comments
   * taken from the proto source definition of the proto element.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * String of comma or space separated case-sensitive words for which
   * method/field name replacement will be disabled.
   *
   * @param string $disableReplacementWords
   */
  public function setDisableReplacementWords($disableReplacementWords)
  {
    $this->disableReplacementWords = $disableReplacementWords;
  }
  /**
   * @return string
   */
  public function getDisableReplacementWords()
  {
    return $this->disableReplacementWords;
  }
  /**
   * The selector is a comma-separated list of patterns for any element such as
   * a method, a field, an enum value. Each pattern is a qualified name of the
   * element which may end in "*", indicating a wildcard. Wildcards are only
   * allowed at the end and for a whole component of the qualified name, i.e.
   * "foo.*" is ok, but not "foo.b*" or "foo.*.bar". A wildcard will match one
   * or more components. To specify a default for all applicable elements, the
   * whole pattern "*" is used.
   *
   * @param string $selector
   */
  public function setSelector($selector)
  {
    $this->selector = $selector;
  }
  /**
   * @return string
   */
  public function getSelector()
  {
    return $this->selector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DocumentationRule::class, 'Google_Service_ServiceManagement_DocumentationRule');
