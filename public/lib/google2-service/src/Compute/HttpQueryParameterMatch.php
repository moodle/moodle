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

namespace Google\Service\Compute;

class HttpQueryParameterMatch extends \Google\Model
{
  /**
   * The queryParameterMatch matches if the value of the parameter exactly
   * matches the contents of exactMatch.
   *
   * Only one of presentMatch, exactMatch, orregexMatch must be set.
   *
   * @var string
   */
  public $exactMatch;
  /**
   * The name of the query parameter to match. The query parameter must exist in
   * the request, in the absence of which the request match fails.
   *
   * @var string
   */
  public $name;
  /**
   * Specifies that the queryParameterMatch matches if the request contains the
   * query parameter, irrespective of whether the parameter has a value or not.
   *
   * Only one of presentMatch, exactMatch, orregexMatch must be set.
   *
   * @var bool
   */
  public $presentMatch;
  /**
   * The queryParameterMatch matches if the value of the parameter matches the
   * regular expression specified byregexMatch. For more information about
   * regular expression syntax, see Syntax.
   *
   * Only one of presentMatch, exactMatch, orregexMatch must be set.
   *
   * Regular expressions can only be used when the loadBalancingScheme is set to
   * INTERNAL_SELF_MANAGED, EXTERNAL_MANAGED (regional scope) or
   * INTERNAL_MANAGED.
   *
   * @var string
   */
  public $regexMatch;

  /**
   * The queryParameterMatch matches if the value of the parameter exactly
   * matches the contents of exactMatch.
   *
   * Only one of presentMatch, exactMatch, orregexMatch must be set.
   *
   * @param string $exactMatch
   */
  public function setExactMatch($exactMatch)
  {
    $this->exactMatch = $exactMatch;
  }
  /**
   * @return string
   */
  public function getExactMatch()
  {
    return $this->exactMatch;
  }
  /**
   * The name of the query parameter to match. The query parameter must exist in
   * the request, in the absence of which the request match fails.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Specifies that the queryParameterMatch matches if the request contains the
   * query parameter, irrespective of whether the parameter has a value or not.
   *
   * Only one of presentMatch, exactMatch, orregexMatch must be set.
   *
   * @param bool $presentMatch
   */
  public function setPresentMatch($presentMatch)
  {
    $this->presentMatch = $presentMatch;
  }
  /**
   * @return bool
   */
  public function getPresentMatch()
  {
    return $this->presentMatch;
  }
  /**
   * The queryParameterMatch matches if the value of the parameter matches the
   * regular expression specified byregexMatch. For more information about
   * regular expression syntax, see Syntax.
   *
   * Only one of presentMatch, exactMatch, orregexMatch must be set.
   *
   * Regular expressions can only be used when the loadBalancingScheme is set to
   * INTERNAL_SELF_MANAGED, EXTERNAL_MANAGED (regional scope) or
   * INTERNAL_MANAGED.
   *
   * @param string $regexMatch
   */
  public function setRegexMatch($regexMatch)
  {
    $this->regexMatch = $regexMatch;
  }
  /**
   * @return string
   */
  public function getRegexMatch()
  {
    return $this->regexMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpQueryParameterMatch::class, 'Google_Service_Compute_HttpQueryParameterMatch');
