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

namespace Google\Service\FactCheckTools;

class GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkupPage extends \Google\Collection
{
  protected $collection_key = 'claimReviewMarkups';
  protected $claimReviewAuthorType = GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewAuthor::class;
  protected $claimReviewAuthorDataType = '';
  protected $claimReviewMarkupsType = GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkup::class;
  protected $claimReviewMarkupsDataType = 'array';
  /**
   * The name of this `ClaimReview` markup page resource, in the form of
   * `pages/{page_id}`. Except for update requests, this field is output-only
   * and should not be set by the user.
   *
   * @var string
   */
  public $name;
  /**
   * The URL of the page associated with this `ClaimReview` markup. While every
   * individual `ClaimReview` has its own URL field, semantically this is a
   * page-level field, and each `ClaimReview` on this page will use this value
   * unless individually overridden. Corresponds to `ClaimReview.url`
   *
   * @var string
   */
  public $pageUrl;
  /**
   * The date when the fact check was published. Similar to the URL,
   * semantically this is a page-level field, and each `ClaimReview` on this
   * page will contain the same value. Corresponds to
   * `ClaimReview.datePublished`
   *
   * @var string
   */
  public $publishDate;
  /**
   * The version ID for this markup. Except for update requests, this field is
   * output-only and should not be set by the user.
   *
   * @var string
   */
  public $versionId;

  /**
   * Info about the author of this claim review. Similar to the above,
   * semantically these are page-level fields, and each `ClaimReview` on this
   * page will contain the same values.
   *
   * @param GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewAuthor $claimReviewAuthor
   */
  public function setClaimReviewAuthor(GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewAuthor $claimReviewAuthor)
  {
    $this->claimReviewAuthor = $claimReviewAuthor;
  }
  /**
   * @return GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewAuthor
   */
  public function getClaimReviewAuthor()
  {
    return $this->claimReviewAuthor;
  }
  /**
   * A list of individual claim reviews for this page. Each item in the list
   * corresponds to one `ClaimReview` element.
   *
   * @param GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkup[] $claimReviewMarkups
   */
  public function setClaimReviewMarkups($claimReviewMarkups)
  {
    $this->claimReviewMarkups = $claimReviewMarkups;
  }
  /**
   * @return GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkup[]
   */
  public function getClaimReviewMarkups()
  {
    return $this->claimReviewMarkups;
  }
  /**
   * The name of this `ClaimReview` markup page resource, in the form of
   * `pages/{page_id}`. Except for update requests, this field is output-only
   * and should not be set by the user.
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
   * The URL of the page associated with this `ClaimReview` markup. While every
   * individual `ClaimReview` has its own URL field, semantically this is a
   * page-level field, and each `ClaimReview` on this page will use this value
   * unless individually overridden. Corresponds to `ClaimReview.url`
   *
   * @param string $pageUrl
   */
  public function setPageUrl($pageUrl)
  {
    $this->pageUrl = $pageUrl;
  }
  /**
   * @return string
   */
  public function getPageUrl()
  {
    return $this->pageUrl;
  }
  /**
   * The date when the fact check was published. Similar to the URL,
   * semantically this is a page-level field, and each `ClaimReview` on this
   * page will contain the same value. Corresponds to
   * `ClaimReview.datePublished`
   *
   * @param string $publishDate
   */
  public function setPublishDate($publishDate)
  {
    $this->publishDate = $publishDate;
  }
  /**
   * @return string
   */
  public function getPublishDate()
  {
    return $this->publishDate;
  }
  /**
   * The version ID for this markup. Except for update requests, this field is
   * output-only and should not be set by the user.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkupPage::class, 'Google_Service_FactCheckTools_GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkupPage');
