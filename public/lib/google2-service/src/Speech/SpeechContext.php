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

class SpeechContext extends \Google\Collection
{
  protected $collection_key = 'phrases';
  /**
   * Hint Boost. Positive value will increase the probability that a specific
   * phrase will be recognized over other similar sounding phrases. The higher
   * the boost, the higher the chance of false positive recognition as well.
   * Negative boost values would correspond to anti-biasing. Anti-biasing is not
   * enabled, so negative boost will simply be ignored. Though `boost` can
   * accept a wide range of positive values, most use cases are best served with
   * values between 0 and 20. We recommend using a binary search approach to
   * finding the optimal value for your use case.
   *
   * @var float
   */
  public $boost;
  /**
   * A list of strings containing words and phrases "hints" so that the speech
   * recognition is more likely to recognize them. This can be used to improve
   * the accuracy for specific words and phrases, for example, if specific
   * commands are typically spoken by the user. This can also be used to add
   * additional words to the vocabulary of the recognizer. See [usage
   * limits](https://cloud.google.com/speech-to-text/quotas#content). List items
   * can also be set to classes for groups of words that represent common
   * concepts that occur in natural language. For example, rather than providing
   * phrase hints for every month of the year, using the $MONTH class improves
   * the likelihood of correctly transcribing audio that includes months.
   *
   * @var string[]
   */
  public $phrases;

  /**
   * Hint Boost. Positive value will increase the probability that a specific
   * phrase will be recognized over other similar sounding phrases. The higher
   * the boost, the higher the chance of false positive recognition as well.
   * Negative boost values would correspond to anti-biasing. Anti-biasing is not
   * enabled, so negative boost will simply be ignored. Though `boost` can
   * accept a wide range of positive values, most use cases are best served with
   * values between 0 and 20. We recommend using a binary search approach to
   * finding the optimal value for your use case.
   *
   * @param float $boost
   */
  public function setBoost($boost)
  {
    $this->boost = $boost;
  }
  /**
   * @return float
   */
  public function getBoost()
  {
    return $this->boost;
  }
  /**
   * A list of strings containing words and phrases "hints" so that the speech
   * recognition is more likely to recognize them. This can be used to improve
   * the accuracy for specific words and phrases, for example, if specific
   * commands are typically spoken by the user. This can also be used to add
   * additional words to the vocabulary of the recognizer. See [usage
   * limits](https://cloud.google.com/speech-to-text/quotas#content). List items
   * can also be set to classes for groups of words that represent common
   * concepts that occur in natural language. For example, rather than providing
   * phrase hints for every month of the year, using the $MONTH class improves
   * the likelihood of correctly transcribing audio that includes months.
   *
   * @param string[] $phrases
   */
  public function setPhrases($phrases)
  {
    $this->phrases = $phrases;
  }
  /**
   * @return string[]
   */
  public function getPhrases()
  {
    return $this->phrases;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpeechContext::class, 'Google_Service_Speech_SpeechContext');
