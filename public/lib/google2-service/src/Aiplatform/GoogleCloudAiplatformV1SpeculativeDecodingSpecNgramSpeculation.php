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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SpeculativeDecodingSpecNgramSpeculation extends \Google\Model
{
  /**
   * The number of last N input tokens used as ngram to search/match against the
   * previous prompt sequence. This is equal to the N in N-Gram. The default
   * value is 3 if not specified.
   *
   * @var int
   */
  public $ngramSize;

  /**
   * The number of last N input tokens used as ngram to search/match against the
   * previous prompt sequence. This is equal to the N in N-Gram. The default
   * value is 3 if not specified.
   *
   * @param int $ngramSize
   */
  public function setNgramSize($ngramSize)
  {
    $this->ngramSize = $ngramSize;
  }
  /**
   * @return int
   */
  public function getNgramSize()
  {
    return $this->ngramSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SpeculativeDecodingSpecNgramSpeculation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SpeculativeDecodingSpecNgramSpeculation');
