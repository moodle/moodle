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

class GoogleCloudAiplatformV1SuggestTrialsRequest extends \Google\Collection
{
  protected $collection_key = 'contexts';
  /**
   * Required. The identifier of the client that is requesting the suggestion.
   * If multiple SuggestTrialsRequests have the same `client_id`, the service
   * will return the identical suggested Trial if the Trial is pending, and
   * provide a new Trial if the last suggested Trial was completed.
   *
   * @var string
   */
  public $clientId;
  protected $contextsType = GoogleCloudAiplatformV1TrialContext::class;
  protected $contextsDataType = 'array';
  /**
   * Required. The number of suggestions requested. It must be positive.
   *
   * @var int
   */
  public $suggestionCount;

  /**
   * Required. The identifier of the client that is requesting the suggestion.
   * If multiple SuggestTrialsRequests have the same `client_id`, the service
   * will return the identical suggested Trial if the Trial is pending, and
   * provide a new Trial if the last suggested Trial was completed.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Optional. This allows you to specify the "context" for a Trial; a context
   * is a slice (a subspace) of the search space. Typical uses for contexts: 1)
   * You are using Vizier to tune a server for best performance, but there's a
   * strong weekly cycle. The context specifies the day-of-week. This allows
   * Tuesday to generalize from Wednesday without assuming that everything is
   * identical. 2) Imagine you're optimizing some medical treatment for people.
   * As they walk in the door, you know certain facts about them (e.g. sex,
   * weight, height, blood-pressure). Put that information in the context, and
   * Vizier will adapt its suggestions to the patient. 3) You want to do a fair
   * A/B test efficiently. Specify the "A" and "B" conditions as contexts, and
   * Vizier will generalize between "A" and "B" conditions. If they are similar,
   * this will allow Vizier to converge to the optimum faster than if "A" and
   * "B" were separate Studies. NOTE: You can also enter contexts as REQUESTED
   * Trials, e.g. via the CreateTrial() RPC; that's the asynchronous option
   * where you don't need a close association between contexts and suggestions.
   * NOTE: All the Parameters you set in a context MUST be defined in the Study.
   * NOTE: You must supply 0 or $suggestion_count contexts. If you don't supply
   * any contexts, Vizier will make suggestions from the full search space
   * specified in the StudySpec; if you supply a full set of context, each
   * suggestion will match the corresponding context. NOTE: A Context with no
   * features set matches anything, and allows suggestions from the full search
   * space. NOTE: Contexts MUST lie within the search space specified in the
   * StudySpec. It's an error if they don't. NOTE: Contexts preferentially match
   * ACTIVE then REQUESTED trials before new suggestions are generated. NOTE:
   * Generation of suggestions involves a match between a Context and
   * (optionally) a REQUESTED trial; if that match is not fully specified, a
   * suggestion will be geneated in the merged subspace.
   *
   * @param GoogleCloudAiplatformV1TrialContext[] $contexts
   */
  public function setContexts($contexts)
  {
    $this->contexts = $contexts;
  }
  /**
   * @return GoogleCloudAiplatformV1TrialContext[]
   */
  public function getContexts()
  {
    return $this->contexts;
  }
  /**
   * Required. The number of suggestions requested. It must be positive.
   *
   * @param int $suggestionCount
   */
  public function setSuggestionCount($suggestionCount)
  {
    $this->suggestionCount = $suggestionCount;
  }
  /**
   * @return int
   */
  public function getSuggestionCount()
  {
    return $this->suggestionCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SuggestTrialsRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SuggestTrialsRequest');
