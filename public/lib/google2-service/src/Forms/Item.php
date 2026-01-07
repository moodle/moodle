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

namespace Google\Service\Forms;

class Item extends \Google\Model
{
  /**
   * The description of the item.
   *
   * @var string
   */
  public $description;
  protected $imageItemType = ImageItem::class;
  protected $imageItemDataType = '';
  /**
   * The item ID. On creation, it can be provided but the ID must not be already
   * used in the form. If not provided, a new ID is assigned.
   *
   * @var string
   */
  public $itemId;
  protected $pageBreakItemType = PageBreakItem::class;
  protected $pageBreakItemDataType = '';
  protected $questionGroupItemType = QuestionGroupItem::class;
  protected $questionGroupItemDataType = '';
  protected $questionItemType = QuestionItem::class;
  protected $questionItemDataType = '';
  protected $textItemType = TextItem::class;
  protected $textItemDataType = '';
  /**
   * The title of the item.
   *
   * @var string
   */
  public $title;
  protected $videoItemType = VideoItem::class;
  protected $videoItemDataType = '';

  /**
   * The description of the item.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Displays an image on the page.
   *
   * @param ImageItem $imageItem
   */
  public function setImageItem(ImageItem $imageItem)
  {
    $this->imageItem = $imageItem;
  }
  /**
   * @return ImageItem
   */
  public function getImageItem()
  {
    return $this->imageItem;
  }
  /**
   * The item ID. On creation, it can be provided but the ID must not be already
   * used in the form. If not provided, a new ID is assigned.
   *
   * @param string $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string
   */
  public function getItemId()
  {
    return $this->itemId;
  }
  /**
   * Starts a new page with a title.
   *
   * @param PageBreakItem $pageBreakItem
   */
  public function setPageBreakItem(PageBreakItem $pageBreakItem)
  {
    $this->pageBreakItem = $pageBreakItem;
  }
  /**
   * @return PageBreakItem
   */
  public function getPageBreakItem()
  {
    return $this->pageBreakItem;
  }
  /**
   * Poses one or more questions to the user with a single major prompt.
   *
   * @param QuestionGroupItem $questionGroupItem
   */
  public function setQuestionGroupItem(QuestionGroupItem $questionGroupItem)
  {
    $this->questionGroupItem = $questionGroupItem;
  }
  /**
   * @return QuestionGroupItem
   */
  public function getQuestionGroupItem()
  {
    return $this->questionGroupItem;
  }
  /**
   * Poses a question to the user.
   *
   * @param QuestionItem $questionItem
   */
  public function setQuestionItem(QuestionItem $questionItem)
  {
    $this->questionItem = $questionItem;
  }
  /**
   * @return QuestionItem
   */
  public function getQuestionItem()
  {
    return $this->questionItem;
  }
  /**
   * Displays a title and description on the page.
   *
   * @param TextItem $textItem
   */
  public function setTextItem(TextItem $textItem)
  {
    $this->textItem = $textItem;
  }
  /**
   * @return TextItem
   */
  public function getTextItem()
  {
    return $this->textItem;
  }
  /**
   * The title of the item.
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
   * Displays a video on the page.
   *
   * @param VideoItem $videoItem
   */
  public function setVideoItem(VideoItem $videoItem)
  {
    $this->videoItem = $videoItem;
  }
  /**
   * @return VideoItem
   */
  public function getVideoItem()
  {
    return $this->videoItem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Item::class, 'Google_Service_Forms_Item');
