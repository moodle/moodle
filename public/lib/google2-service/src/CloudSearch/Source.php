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

class Source extends \Google\Model
{
  public const PREDEFINED_SOURCE_NONE = 'NONE';
  /**
   * Suggests queries issued by the user in the past. Only valid when used with
   * the suggest API. Ignored when used in the query API.
   */
  public const PREDEFINED_SOURCE_QUERY_HISTORY = 'QUERY_HISTORY';
  /**
   * Suggests people in the organization. Only valid when used with the suggest
   * API. Results in an error when used in the query API.
   */
  public const PREDEFINED_SOURCE_PERSON = 'PERSON';
  public const PREDEFINED_SOURCE_GOOGLE_DRIVE = 'GOOGLE_DRIVE';
  public const PREDEFINED_SOURCE_GOOGLE_GMAIL = 'GOOGLE_GMAIL';
  public const PREDEFINED_SOURCE_GOOGLE_SITES = 'GOOGLE_SITES';
  public const PREDEFINED_SOURCE_GOOGLE_GROUPS = 'GOOGLE_GROUPS';
  public const PREDEFINED_SOURCE_GOOGLE_CALENDAR = 'GOOGLE_CALENDAR';
  public const PREDEFINED_SOURCE_GOOGLE_KEEP = 'GOOGLE_KEEP';
  /**
   * Source name for content indexed by the Indexing API.
   *
   * @var string
   */
  public $name;
  /**
   * Predefined content source for Google Apps.
   *
   * @var string
   */
  public $predefinedSource;

  /**
   * Source name for content indexed by the Indexing API.
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
   * Predefined content source for Google Apps.
   *
   * Accepted values: NONE, QUERY_HISTORY, PERSON, GOOGLE_DRIVE, GOOGLE_GMAIL,
   * GOOGLE_SITES, GOOGLE_GROUPS, GOOGLE_CALENDAR, GOOGLE_KEEP
   *
   * @param self::PREDEFINED_SOURCE_* $predefinedSource
   */
  public function setPredefinedSource($predefinedSource)
  {
    $this->predefinedSource = $predefinedSource;
  }
  /**
   * @return self::PREDEFINED_SOURCE_*
   */
  public function getPredefinedSource()
  {
    return $this->predefinedSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Source::class, 'Google_Service_CloudSearch_Source');
