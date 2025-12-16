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

namespace Google\Service\MyBusinessQA;

class Question extends \Google\Collection
{
  protected $collection_key = 'topAnswers';
  protected $authorType = Author::class;
  protected $authorDataType = '';
  /**
   * Output only. The timestamp for when the question was written.
   *
   * @var string
   */
  public $createTime;
  /**
   * Immutable. The unique name for the question. locations/questions This field
   * will be ignored if set during question creation.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The text of the question. It should contain at least three words
   * and the total length should be greater than or equal to 10 characters. The
   * maximum length is 4096 characters.
   *
   * @var string
   */
  public $text;
  protected $topAnswersType = Answer::class;
  protected $topAnswersDataType = 'array';
  /**
   * Output only. The total number of answers posted for this question.
   *
   * @var int
   */
  public $totalAnswerCount;
  /**
   * Output only. The timestamp for when the question was last modified.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The number of upvotes for the question.
   *
   * @var int
   */
  public $upvoteCount;

  /**
   * Output only. The author of the question.
   *
   * @param Author $author
   */
  public function setAuthor(Author $author)
  {
    $this->author = $author;
  }
  /**
   * @return Author
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * Output only. The timestamp for when the question was written.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Immutable. The unique name for the question. locations/questions This field
   * will be ignored if set during question creation.
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
   * Required. The text of the question. It should contain at least three words
   * and the total length should be greater than or equal to 10 characters. The
   * maximum length is 4096 characters.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Output only. A list of answers to the question, sorted by upvotes. This may
   * not be a complete list of answers depending on the request parameters
   * (answers_per_question)
   *
   * @param Answer[] $topAnswers
   */
  public function setTopAnswers($topAnswers)
  {
    $this->topAnswers = $topAnswers;
  }
  /**
   * @return Answer[]
   */
  public function getTopAnswers()
  {
    return $this->topAnswers;
  }
  /**
   * Output only. The total number of answers posted for this question.
   *
   * @param int $totalAnswerCount
   */
  public function setTotalAnswerCount($totalAnswerCount)
  {
    $this->totalAnswerCount = $totalAnswerCount;
  }
  /**
   * @return int
   */
  public function getTotalAnswerCount()
  {
    return $this->totalAnswerCount;
  }
  /**
   * Output only. The timestamp for when the question was last modified.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The number of upvotes for the question.
   *
   * @param int $upvoteCount
   */
  public function setUpvoteCount($upvoteCount)
  {
    $this->upvoteCount = $upvoteCount;
  }
  /**
   * @return int
   */
  public function getUpvoteCount()
  {
    return $this->upvoteCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Question::class, 'Google_Service_MyBusinessQA_Question');
