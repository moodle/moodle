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

namespace Google\Service\DriveActivity;

class FieldValue extends \Google\Model
{
  protected $dateType = Date::class;
  protected $dateDataType = '';
  protected $integerType = DriveactivityInteger::class;
  protected $integerDataType = '';
  protected $selectionType = Selection::class;
  protected $selectionDataType = '';
  protected $selectionListType = SelectionList::class;
  protected $selectionListDataType = '';
  protected $textType = Text::class;
  protected $textDataType = '';
  protected $textListType = TextList::class;
  protected $textListDataType = '';
  protected $userType = SingleUser::class;
  protected $userDataType = '';
  protected $userListType = UserList::class;
  protected $userListDataType = '';

  /**
   * Date Field value.
   *
   * @param Date $date
   */
  public function setDate(Date $date)
  {
    $this->date = $date;
  }
  /**
   * @return Date
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * Integer Field value.
   *
   * @param DriveactivityInteger $integer
   */
  public function setInteger(DriveactivityInteger $integer)
  {
    $this->integer = $integer;
  }
  /**
   * @return DriveactivityInteger
   */
  public function getInteger()
  {
    return $this->integer;
  }
  /**
   * Selection Field value.
   *
   * @param Selection $selection
   */
  public function setSelection(Selection $selection)
  {
    $this->selection = $selection;
  }
  /**
   * @return Selection
   */
  public function getSelection()
  {
    return $this->selection;
  }
  /**
   * Selection List Field value.
   *
   * @param SelectionList $selectionList
   */
  public function setSelectionList(SelectionList $selectionList)
  {
    $this->selectionList = $selectionList;
  }
  /**
   * @return SelectionList
   */
  public function getSelectionList()
  {
    return $this->selectionList;
  }
  /**
   * Text Field value.
   *
   * @param Text $text
   */
  public function setText(Text $text)
  {
    $this->text = $text;
  }
  /**
   * @return Text
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Text List Field value.
   *
   * @param TextList $textList
   */
  public function setTextList(TextList $textList)
  {
    $this->textList = $textList;
  }
  /**
   * @return TextList
   */
  public function getTextList()
  {
    return $this->textList;
  }
  /**
   * User Field value.
   *
   * @param SingleUser $user
   */
  public function setUser(SingleUser $user)
  {
    $this->user = $user;
  }
  /**
   * @return SingleUser
   */
  public function getUser()
  {
    return $this->user;
  }
  /**
   * User List Field value.
   *
   * @param UserList $userList
   */
  public function setUserList(UserList $userList)
  {
    $this->userList = $userList;
  }
  /**
   * @return UserList
   */
  public function getUserList()
  {
    return $this->userList;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldValue::class, 'Google_Service_DriveActivity_FieldValue');
