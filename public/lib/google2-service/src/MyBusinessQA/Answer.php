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

class Answer extends \Google\Model
{
  protected $authorType = Author::class;
  protected $authorDataType = '';
  /**
   * Output only. The timestamp for when the answer was written. Only retrieved
   * during ListResponse fetching.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The unique name for the answer locations/questions/answers
   *
   * @var string
   */
  public $name;
  /**
   * Required. The text of the answer. It should contain at least one non-
   * whitespace character. The maximum length is 4096 characters.
   *
   * @var string
   */
  public $text;
  /**
   * Output only. The timestamp for when the answer was last modified.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The number of upvotes for the answer.
   *
   * @var int
   */
  public $upvoteCount;

  /**
   * Output only. The author of the answer. Will only be set during list
   * operations.
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
   * Output only. The timestamp for when the answer was written. Only retrieved
   * during ListResponse fetching.
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
   * Output only. The unique name for the answer locations/questions/answers
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
   * Required. The text of the answer. It should contain at least one non-
   * whitespace character. The maximum length is 4096 characters.
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
   * Output only. The timestamp for when the answer was last modified.
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
   * Output only. The number of upvotes for the answer.
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
class_alias(Answer::class, 'Google_Service_MyBusinessQA_Answer');
