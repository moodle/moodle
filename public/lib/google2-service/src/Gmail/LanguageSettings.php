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

namespace Google\Service\Gmail;

class LanguageSettings extends \Google\Model
{
  /**
   * The language to display Gmail in, formatted as an RFC 3066 Language Tag
   * (for example `en-GB`, `fr` or `ja` for British English, French, or Japanese
   * respectively). The set of languages supported by Gmail evolves over time,
   * so please refer to the "Language" dropdown in the Gmail settings for all
   * available options, as described in the language settings help article. For
   * a table of sample values, see [Manage language settings](https://developers
   * .google.com/workspace/gmail/api/guides/language-settings). Not all Gmail
   * clients can display the same set of languages. In the case that a user's
   * display language is not available for use on a particular client, said
   * client automatically chooses to display in the closest supported variant
   * (or a reasonable default).
   *
   * @var string
   */
  public $displayLanguage;

  /**
   * The language to display Gmail in, formatted as an RFC 3066 Language Tag
   * (for example `en-GB`, `fr` or `ja` for British English, French, or Japanese
   * respectively). The set of languages supported by Gmail evolves over time,
   * so please refer to the "Language" dropdown in the Gmail settings for all
   * available options, as described in the language settings help article. For
   * a table of sample values, see [Manage language settings](https://developers
   * .google.com/workspace/gmail/api/guides/language-settings). Not all Gmail
   * clients can display the same set of languages. In the case that a user's
   * display language is not available for use on a particular client, said
   * client automatically chooses to display in the closest supported variant
   * (or a reasonable default).
   *
   * @param string $displayLanguage
   */
  public function setDisplayLanguage($displayLanguage)
  {
    $this->displayLanguage = $displayLanguage;
  }
  /**
   * @return string
   */
  public function getDisplayLanguage()
  {
    return $this->displayLanguage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LanguageSettings::class, 'Google_Service_Gmail_LanguageSettings');
