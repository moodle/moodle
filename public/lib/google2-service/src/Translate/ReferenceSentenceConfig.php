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

class ReferenceSentenceConfig extends \Google\Collection
{
  protected $collection_key = 'referenceSentencePairLists';
  protected $referenceSentencePairListsType = ReferenceSentencePairList::class;
  protected $referenceSentencePairListsDataType = 'array';
  /**
   * Source language code.
   *
   * @var string
   */
  public $sourceLanguageCode;
  /**
   * Target language code.
   *
   * @var string
   */
  public $targetLanguageCode;

  /**
   * Reference sentences pair lists. Each list will be used as the references to
   * translate the sentence under "content" field at the corresponding index.
   * Length of the list is required to be equal to the length of "content"
   * field.
   *
   * @param ReferenceSentencePairList[] $referenceSentencePairLists
   */
  public function setReferenceSentencePairLists($referenceSentencePairLists)
  {
    $this->referenceSentencePairLists = $referenceSentencePairLists;
  }
  /**
   * @return ReferenceSentencePairList[]
   */
  public function getReferenceSentencePairLists()
  {
    return $this->referenceSentencePairLists;
  }
  /**
   * Source language code.
   *
   * @param string $sourceLanguageCode
   */
  public function setSourceLanguageCode($sourceLanguageCode)
  {
    $this->sourceLanguageCode = $sourceLanguageCode;
  }
  /**
   * @return string
   */
  public function getSourceLanguageCode()
  {
    return $this->sourceLanguageCode;
  }
  /**
   * Target language code.
   *
   * @param string $targetLanguageCode
   */
  public function setTargetLanguageCode($targetLanguageCode)
  {
    $this->targetLanguageCode = $targetLanguageCode;
  }
  /**
   * @return string
   */
  public function getTargetLanguageCode()
  {
    return $this->targetLanguageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReferenceSentenceConfig::class, 'Google_Service_Translate_ReferenceSentenceConfig');
