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

namespace Google\Service\AndroidManagement;

class PersistentPreferredActivity extends \Google\Collection
{
  protected $collection_key = 'categories';
  /**
   * The intent actions to match in the filter. If any actions are included in
   * the filter, then an intent's action must be one of those values for it to
   * match. If no actions are included, the intent action is ignored.
   *
   * @var string[]
   */
  public $actions;
  /**
   * The intent categories to match in the filter. An intent includes the
   * categories that it requires, all of which must be included in the filter in
   * order to match. In other words, adding a category to the filter has no
   * impact on matching unless that category is specified in the intent.
   *
   * @var string[]
   */
  public $categories;
  /**
   * The activity that should be the default intent handler. This should be an
   * Android component name, e.g. com.android.enterprise.app/.MainActivity.
   * Alternatively, the value may be the package name of an app, which causes
   * Android Device Policy to choose an appropriate activity from the app to
   * handle the intent.
   *
   * @var string
   */
  public $receiverActivity;

  /**
   * The intent actions to match in the filter. If any actions are included in
   * the filter, then an intent's action must be one of those values for it to
   * match. If no actions are included, the intent action is ignored.
   *
   * @param string[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return string[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * The intent categories to match in the filter. An intent includes the
   * categories that it requires, all of which must be included in the filter in
   * order to match. In other words, adding a category to the filter has no
   * impact on matching unless that category is specified in the intent.
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * The activity that should be the default intent handler. This should be an
   * Android component name, e.g. com.android.enterprise.app/.MainActivity.
   * Alternatively, the value may be the package name of an app, which causes
   * Android Device Policy to choose an appropriate activity from the app to
   * handle the intent.
   *
   * @param string $receiverActivity
   */
  public function setReceiverActivity($receiverActivity)
  {
    $this->receiverActivity = $receiverActivity;
  }
  /**
   * @return string
   */
  public function getReceiverActivity()
  {
    return $this->receiverActivity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersistentPreferredActivity::class, 'Google_Service_AndroidManagement_PersistentPreferredActivity');
