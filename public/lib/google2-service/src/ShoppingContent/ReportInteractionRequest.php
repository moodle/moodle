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

namespace Google\Service\ShoppingContent;

class ReportInteractionRequest extends \Google\Model
{
  /**
   * Default value. If provided, the service will throw ApiError with
   * description "Required parameter: interactionType".
   */
  public const INTERACTION_TYPE_INTERACTION_TYPE_UNSPECIFIED = 'INTERACTION_TYPE_UNSPECIFIED';
  /**
   * When a recommendation is dismissed.
   */
  public const INTERACTION_TYPE_INTERACTION_DISMISS = 'INTERACTION_DISMISS';
  /**
   * When a recommendation is clicked.
   */
  public const INTERACTION_TYPE_INTERACTION_CLICK = 'INTERACTION_CLICK';
  /**
   * Required. Type of the interaction that is reported, for example
   * INTERACTION_CLICK.
   *
   * @var string
   */
  public $interactionType;
  /**
   * Required. Token of the response when recommendation was returned.
   *
   * @var string
   */
  public $responseToken;
  /**
   * Optional. Subtype of the recommendations this interaction happened on. This
   * field must be set only to the value that is returned by {@link
   * `RecommendationsService.GenerateRecommendations`} call.
   *
   * @var string
   */
  public $subtype;
  /**
   * Required. Type of the recommendations on which this interaction happened.
   * This field must be set only to the value that is returned by {@link
   * `GenerateRecommendationsResponse`} call.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Type of the interaction that is reported, for example
   * INTERACTION_CLICK.
   *
   * Accepted values: INTERACTION_TYPE_UNSPECIFIED, INTERACTION_DISMISS,
   * INTERACTION_CLICK
   *
   * @param self::INTERACTION_TYPE_* $interactionType
   */
  public function setInteractionType($interactionType)
  {
    $this->interactionType = $interactionType;
  }
  /**
   * @return self::INTERACTION_TYPE_*
   */
  public function getInteractionType()
  {
    return $this->interactionType;
  }
  /**
   * Required. Token of the response when recommendation was returned.
   *
   * @param string $responseToken
   */
  public function setResponseToken($responseToken)
  {
    $this->responseToken = $responseToken;
  }
  /**
   * @return string
   */
  public function getResponseToken()
  {
    return $this->responseToken;
  }
  /**
   * Optional. Subtype of the recommendations this interaction happened on. This
   * field must be set only to the value that is returned by {@link
   * `RecommendationsService.GenerateRecommendations`} call.
   *
   * @param string $subtype
   */
  public function setSubtype($subtype)
  {
    $this->subtype = $subtype;
  }
  /**
   * @return string
   */
  public function getSubtype()
  {
    return $this->subtype;
  }
  /**
   * Required. Type of the recommendations on which this interaction happened.
   * This field must be set only to the value that is returned by {@link
   * `GenerateRecommendationsResponse`} call.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportInteractionRequest::class, 'Google_Service_ShoppingContent_ReportInteractionRequest');
