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

namespace Google\Service\Translate;

class Glossary extends \Google\Model
{
  /**
   * Optional. The display name of the glossary.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. When the glossary creation was finished.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. The number of entries defined in the glossary.
   *
   * @var int
   */
  public $entryCount;
  protected $inputConfigType = GlossaryInputConfig::class;
  protected $inputConfigDataType = '';
  protected $languageCodesSetType = LanguageCodesSet::class;
  protected $languageCodesSetDataType = '';
  protected $languagePairType = LanguageCodePair::class;
  protected $languagePairDataType = '';
  /**
   * Required. The resource name of the glossary. Glossary names have the form
   * `projects/{project-number-or-id}/locations/{location-
   * id}/glossaries/{glossary-id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. When CreateGlossary was called.
   *
   * @var string
   */
  public $submitTime;

  /**
   * Optional. The display name of the glossary.
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
   * Output only. When the glossary creation was finished.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The number of entries defined in the glossary.
   *
   * @param int $entryCount
   */
  public function setEntryCount($entryCount)
  {
    $this->entryCount = $entryCount;
  }
  /**
   * @return int
   */
  public function getEntryCount()
  {
    return $this->entryCount;
  }
  /**
   * Required. Provides examples to build the glossary from. Total glossary must
   * not exceed 10M Unicode codepoints.
   *
   * @param GlossaryInputConfig $inputConfig
   */
  public function setInputConfig(GlossaryInputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return GlossaryInputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
  /**
   * Used with equivalent term set glossaries.
   *
   * @param LanguageCodesSet $languageCodesSet
   */
  public function setLanguageCodesSet(LanguageCodesSet $languageCodesSet)
  {
    $this->languageCodesSet = $languageCodesSet;
  }
  /**
   * @return LanguageCodesSet
   */
  public function getLanguageCodesSet()
  {
    return $this->languageCodesSet;
  }
  /**
   * Used with unidirectional glossaries.
   *
   * @param LanguageCodePair $languagePair
   */
  public function setLanguagePair(LanguageCodePair $languagePair)
  {
    $this->languagePair = $languagePair;
  }
  /**
   * @return LanguageCodePair
   */
  public function getLanguagePair()
  {
    return $this->languagePair;
  }
  /**
   * Required. The resource name of the glossary. Glossary names have the form
   * `projects/{project-number-or-id}/locations/{location-
   * id}/glossaries/{glossary-id}`.
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
   * Output only. When CreateGlossary was called.
   *
   * @param string $submitTime
   */
  public function setSubmitTime($submitTime)
  {
    $this->submitTime = $submitTime;
  }
  /**
   * @return string
   */
  public function getSubmitTime()
  {
    return $this->submitTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Glossary::class, 'Google_Service_Translate_Glossary');
