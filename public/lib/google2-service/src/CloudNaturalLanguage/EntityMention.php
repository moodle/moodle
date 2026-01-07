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

namespace Google\Service\CloudNaturalLanguage;

class EntityMention extends \Google\Model
{
  /**
   * Unknown
   */
  public const TYPE_TYPE_UNKNOWN = 'TYPE_UNKNOWN';
  /**
   * Proper name
   */
  public const TYPE_PROPER = 'PROPER';
  /**
   * Common noun (or noun compound)
   */
  public const TYPE_COMMON = 'COMMON';
  /**
   * Probability score associated with the entity. The score shows the
   * probability of the entity mention being the entity type. The score is in
   * (0, 1] range.
   *
   * @var float
   */
  public $probability;
  protected $sentimentType = Sentiment::class;
  protected $sentimentDataType = '';
  protected $textType = TextSpan::class;
  protected $textDataType = '';
  /**
   * The type of the entity mention.
   *
   * @var string
   */
  public $type;

  /**
   * Probability score associated with the entity. The score shows the
   * probability of the entity mention being the entity type. The score is in
   * (0, 1] range.
   *
   * @param float $probability
   */
  public function setProbability($probability)
  {
    $this->probability = $probability;
  }
  /**
   * @return float
   */
  public function getProbability()
  {
    return $this->probability;
  }
  /**
   * For calls to AnalyzeEntitySentiment this field will contain the sentiment
   * expressed for this mention of the entity in the provided document.
   *
   * @param Sentiment $sentiment
   */
  public function setSentiment(Sentiment $sentiment)
  {
    $this->sentiment = $sentiment;
  }
  /**
   * @return Sentiment
   */
  public function getSentiment()
  {
    return $this->sentiment;
  }
  /**
   * The mention text.
   *
   * @param TextSpan $text
   */
  public function setText(TextSpan $text)
  {
    $this->text = $text;
  }
  /**
   * @return TextSpan
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The type of the entity mention.
   *
   * Accepted values: TYPE_UNKNOWN, PROPER, COMMON
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityMention::class, 'Google_Service_CloudNaturalLanguage_EntityMention');
