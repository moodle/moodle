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

class Recommendation extends \Google\Collection
{
  protected $collection_key = 'creative';
  protected $additionalCallToActionType = RecommendationCallToAction::class;
  protected $additionalCallToActionDataType = 'array';
  protected $additionalDescriptionsType = RecommendationDescription::class;
  protected $additionalDescriptionsDataType = 'array';
  protected $creativeType = RecommendationCreative::class;
  protected $creativeDataType = 'array';
  protected $defaultCallToActionType = RecommendationCallToAction::class;
  protected $defaultCallToActionDataType = '';
  /**
   * Optional. Localized recommendation description. The localization the {@link
   * `GenerateRecommendationsRequest.language_code`} field in {@link
   * `GenerateRecommendationsRequest`} requests.
   *
   * @var string
   */
  public $defaultDescription;
  /**
   * Optional. A numerical score of the impact from the recommendation's
   * description. For example, a recommendation might suggest an upward trend in
   * sales for a certain product. Higher number means larger impact.
   *
   * @var int
   */
  public $numericalImpact;
  /**
   * Optional. Indicates whether a user needs to pay when they complete the user
   * journey suggested by the recommendation.
   *
   * @var bool
   */
  public $paid;
  /**
   * Optional. Localized recommendation name. The localization uses the {@link
   * `GenerateRecommendationsRequest.language_code`} field in {@link
   * `GenerateRecommendationsRequest`} requests.
   *
   * @var string
   */
  public $recommendationName;
  /**
   * Optional. Subtype of the recommendations. Only applicable when multiple
   * recommendations can be generated per type, and is used as an identifier of
   * recommendation under the same recommendation type.
   *
   * @var string
   */
  public $subType;
  /**
   * Optional. Localized Recommendation Title. Localization uses the {@link
   * `GenerateRecommendationsRequest.language_code`} field in {@link
   * `GenerateRecommendationsRequest`} requests.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. Type of the recommendation. List of currently available
   * recommendation types: - OPPORTUNITY_CREATE_NEW_COLLECTION -
   * OPPORTUNITY_CREATE_EMAIL_CAMPAIGN
   *
   * @var string
   */
  public $type;

  /**
   * Output only. CTAs of this recommendation. Repeated.
   *
   * @param RecommendationCallToAction[] $additionalCallToAction
   */
  public function setAdditionalCallToAction($additionalCallToAction)
  {
    $this->additionalCallToAction = $additionalCallToAction;
  }
  /**
   * @return RecommendationCallToAction[]
   */
  public function getAdditionalCallToAction()
  {
    return $this->additionalCallToAction;
  }
  /**
   * Output only. List of additional localized descriptions for a
   * recommendation. Localication uses the `languageCode` field in
   * `GenerateRecommendations` requests. Not all description types are
   * guaranteed to be present and we recommend to rely on default description.
   *
   * @param RecommendationDescription[] $additionalDescriptions
   */
  public function setAdditionalDescriptions($additionalDescriptions)
  {
    $this->additionalDescriptions = $additionalDescriptions;
  }
  /**
   * @return RecommendationDescription[]
   */
  public function getAdditionalDescriptions()
  {
    return $this->additionalDescriptions;
  }
  /**
   * Output only. Any creatives attached to the recommendation. Repeated.
   *
   * @param RecommendationCreative[] $creative
   */
  public function setCreative($creative)
  {
    $this->creative = $creative;
  }
  /**
   * @return RecommendationCreative[]
   */
  public function getCreative()
  {
    return $this->creative;
  }
  /**
   * Optional. Default CTA of the recommendation.
   *
   * @param RecommendationCallToAction $defaultCallToAction
   */
  public function setDefaultCallToAction(RecommendationCallToAction $defaultCallToAction)
  {
    $this->defaultCallToAction = $defaultCallToAction;
  }
  /**
   * @return RecommendationCallToAction
   */
  public function getDefaultCallToAction()
  {
    return $this->defaultCallToAction;
  }
  /**
   * Optional. Localized recommendation description. The localization the {@link
   * `GenerateRecommendationsRequest.language_code`} field in {@link
   * `GenerateRecommendationsRequest`} requests.
   *
   * @param string $defaultDescription
   */
  public function setDefaultDescription($defaultDescription)
  {
    $this->defaultDescription = $defaultDescription;
  }
  /**
   * @return string
   */
  public function getDefaultDescription()
  {
    return $this->defaultDescription;
  }
  /**
   * Optional. A numerical score of the impact from the recommendation's
   * description. For example, a recommendation might suggest an upward trend in
   * sales for a certain product. Higher number means larger impact.
   *
   * @param int $numericalImpact
   */
  public function setNumericalImpact($numericalImpact)
  {
    $this->numericalImpact = $numericalImpact;
  }
  /**
   * @return int
   */
  public function getNumericalImpact()
  {
    return $this->numericalImpact;
  }
  /**
   * Optional. Indicates whether a user needs to pay when they complete the user
   * journey suggested by the recommendation.
   *
   * @param bool $paid
   */
  public function setPaid($paid)
  {
    $this->paid = $paid;
  }
  /**
   * @return bool
   */
  public function getPaid()
  {
    return $this->paid;
  }
  /**
   * Optional. Localized recommendation name. The localization uses the {@link
   * `GenerateRecommendationsRequest.language_code`} field in {@link
   * `GenerateRecommendationsRequest`} requests.
   *
   * @param string $recommendationName
   */
  public function setRecommendationName($recommendationName)
  {
    $this->recommendationName = $recommendationName;
  }
  /**
   * @return string
   */
  public function getRecommendationName()
  {
    return $this->recommendationName;
  }
  /**
   * Optional. Subtype of the recommendations. Only applicable when multiple
   * recommendations can be generated per type, and is used as an identifier of
   * recommendation under the same recommendation type.
   *
   * @param string $subType
   */
  public function setSubType($subType)
  {
    $this->subType = $subType;
  }
  /**
   * @return string
   */
  public function getSubType()
  {
    return $this->subType;
  }
  /**
   * Optional. Localized Recommendation Title. Localization uses the {@link
   * `GenerateRecommendationsRequest.language_code`} field in {@link
   * `GenerateRecommendationsRequest`} requests.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Output only. Type of the recommendation. List of currently available
   * recommendation types: - OPPORTUNITY_CREATE_NEW_COLLECTION -
   * OPPORTUNITY_CREATE_EMAIL_CAMPAIGN
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
class_alias(Recommendation::class, 'Google_Service_ShoppingContent_Recommendation');
