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

namespace Google\Service\CloudSearch;

class ThirdPartyGenericCard extends \Google\Model
{
  /**
   * Unique identifier for the card.
   *
   * @var string
   */
  public $cardId;
  /**
   * Category that the card belongs to.
   *
   * @var string
   */
  public $category;
  protected $contentType = Content::class;
  protected $contentDataType = '';
  protected $contextType = Context::class;
  protected $contextDataType = '';
  /**
   * Whether the card can be dismissed.
   *
   * @var bool
   */
  public $isDismissible;
  /**
   * Priority of the card, where 0 is the highest priority.
   *
   * @var int
   */
  public $priority;

  /**
   * Unique identifier for the card.
   *
   * @param string $cardId
   */
  public function setCardId($cardId)
  {
    $this->cardId = $cardId;
  }
  /**
   * @return string
   */
  public function getCardId()
  {
    return $this->cardId;
  }
  /**
   * Category that the card belongs to.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * [Required] Card content.
   *
   * @param Content $content
   */
  public function setContent(Content $content)
  {
    $this->content = $content;
  }
  /**
   * @return Content
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * [Required] Context where the card should be triggered.
   *
   * @param Context $context
   */
  public function setContext(Context $context)
  {
    $this->context = $context;
  }
  /**
   * @return Context
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Whether the card can be dismissed.
   *
   * @param bool $isDismissible
   */
  public function setIsDismissible($isDismissible)
  {
    $this->isDismissible = $isDismissible;
  }
  /**
   * @return bool
   */
  public function getIsDismissible()
  {
    return $this->isDismissible;
  }
  /**
   * Priority of the card, where 0 is the highest priority.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThirdPartyGenericCard::class, 'Google_Service_CloudSearch_ThirdPartyGenericCard');
