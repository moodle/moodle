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

namespace Google\Service\Speech;

class SpeechAdaptation extends \Google\Collection
{
  protected $collection_key = 'phraseSets';
  protected $abnfGrammarType = ABNFGrammar::class;
  protected $abnfGrammarDataType = '';
  protected $customClassesType = CustomClass::class;
  protected $customClassesDataType = 'array';
  /**
   * A collection of phrase set resource names to use.
   *
   * @var string[]
   */
  public $phraseSetReferences;
  protected $phraseSetsType = PhraseSet::class;
  protected $phraseSetsDataType = 'array';

  /**
   * Augmented Backus-Naur form (ABNF) is a standardized grammar notation
   * comprised by a set of derivation rules. See specifications:
   * https://www.w3.org/TR/speech-grammar
   *
   * @param ABNFGrammar $abnfGrammar
   */
  public function setAbnfGrammar(ABNFGrammar $abnfGrammar)
  {
    $this->abnfGrammar = $abnfGrammar;
  }
  /**
   * @return ABNFGrammar
   */
  public function getAbnfGrammar()
  {
    return $this->abnfGrammar;
  }
  /**
   * A collection of custom classes. To specify the classes inline, leave the
   * class' `name` blank and fill in the rest of its fields, giving it a unique
   * `custom_class_id`. Refer to the inline defined class in phrase hints by its
   * `custom_class_id`.
   *
   * @param CustomClass[] $customClasses
   */
  public function setCustomClasses($customClasses)
  {
    $this->customClasses = $customClasses;
  }
  /**
   * @return CustomClass[]
   */
  public function getCustomClasses()
  {
    return $this->customClasses;
  }
  /**
   * A collection of phrase set resource names to use.
   *
   * @param string[] $phraseSetReferences
   */
  public function setPhraseSetReferences($phraseSetReferences)
  {
    $this->phraseSetReferences = $phraseSetReferences;
  }
  /**
   * @return string[]
   */
  public function getPhraseSetReferences()
  {
    return $this->phraseSetReferences;
  }
  /**
   * A collection of phrase sets. To specify the hints inline, leave the phrase
   * set's `name` blank and fill in the rest of its fields. Any phrase set can
   * use any custom class.
   *
   * @param PhraseSet[] $phraseSets
   */
  public function setPhraseSets($phraseSets)
  {
    $this->phraseSets = $phraseSets;
  }
  /**
   * @return PhraseSet[]
   */
  public function getPhraseSets()
  {
    return $this->phraseSets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpeechAdaptation::class, 'Google_Service_Speech_SpeechAdaptation');
