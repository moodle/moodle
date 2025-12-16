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

namespace Google\Service\Classroom;

class AssignmentSubmission extends \Google\Collection
{
  protected $collection_key = 'attachments';
  protected $attachmentsType = Attachment::class;
  protected $attachmentsDataType = 'array';

  /**
   * Attachments added by the student. Drive files that correspond to materials
   * with a share mode of STUDENT_COPY may not exist yet if the student has not
   * accessed the assignment in Classroom. Some attachment metadata is only
   * populated if the requesting user has permission to access it. Identifier
   * and alternate_link fields are always available, but others (for example,
   * title) may not be.
   *
   * @param Attachment[] $attachments
   */
  public function setAttachments($attachments)
  {
    $this->attachments = $attachments;
  }
  /**
   * @return Attachment[]
   */
  public function getAttachments()
  {
    return $this->attachments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssignmentSubmission::class, 'Google_Service_Classroom_AssignmentSubmission');
