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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1TextParagraph extends \Google\Model
{
  /**
   * The text is rendered as HTML if unspecified.
   */
  public const TEXT_SYNTAX_TEXT_SYNTAX_UNSPECIFIED = 'TEXT_SYNTAX_UNSPECIFIED';
  /**
   * The text is rendered as HTML. This is the default value.
   */
  public const TEXT_SYNTAX_HTML = 'HTML';
  /**
   * The text is rendered as Markdown.
   */
  public const TEXT_SYNTAX_MARKDOWN = 'MARKDOWN';
  /**
   * The maximum number of lines of text that are displayed in the widget. If
   * the text exceeds the specified maximum number of lines, the excess content
   * is concealed behind a **show more** button. If the text is equal or shorter
   * than the specified maximum number of lines, a **show more** button isn't
   * displayed. The default value is 0, in which case all context is displayed.
   * Negative values are ignored.
   *
   * @var int
   */
  public $maxLines;
  /**
   * The text that's shown in the widget.
   *
   * @var string
   */
  public $text;
  /**
   * The syntax of the text. If not set, the text is rendered as HTML. [Google
   * Chat apps](https://developers.google.com/workspace/chat):
   *
   * @var string
   */
  public $textSyntax;

  /**
   * The maximum number of lines of text that are displayed in the widget. If
   * the text exceeds the specified maximum number of lines, the excess content
   * is concealed behind a **show more** button. If the text is equal or shorter
   * than the specified maximum number of lines, a **show more** button isn't
   * displayed. The default value is 0, in which case all context is displayed.
   * Negative values are ignored.
   *
   * @param int $maxLines
   */
  public function setMaxLines($maxLines)
  {
    $this->maxLines = $maxLines;
  }
  /**
   * @return int
   */
  public function getMaxLines()
  {
    return $this->maxLines;
  }
  /**
   * The text that's shown in the widget.
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
   * The syntax of the text. If not set, the text is rendered as HTML. [Google
   * Chat apps](https://developers.google.com/workspace/chat):
   *
   * Accepted values: TEXT_SYNTAX_UNSPECIFIED, HTML, MARKDOWN
   *
   * @param self::TEXT_SYNTAX_* $textSyntax
   */
  public function setTextSyntax($textSyntax)
  {
    $this->textSyntax = $textSyntax;
  }
  /**
   * @return self::TEXT_SYNTAX_*
   */
  public function getTextSyntax()
  {
    return $this->textSyntax;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1TextParagraph::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1TextParagraph');
