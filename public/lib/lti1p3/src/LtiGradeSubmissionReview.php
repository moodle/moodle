<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Concerns\JsonStringable;

class LtiGradeSubmissionReview
{
    use JsonStringable;
    private $reviewable_status;
    private $label;
    private $url;
    private $custom;

    public function __construct(?array $gradeSubmission = null)
    {
        $this->reviewable_status = $gradeSubmission['reviewableStatus'] ?? null;
        $this->label = $gradeSubmission['label'] ?? null;
        $this->url = $gradeSubmission['url'] ?? null;
        $this->custom = $gradeSubmission['custom'] ?? null;
    }

    public function getArray(): array
    {
        return [
            'reviewableStatus' => $this->reviewable_status,
            'label' => $this->label,
            'url' => $this->url,
            'custom' => $this->custom,
        ];
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(): self
    {
        return new LtiGradeSubmissionReview();
    }

    public function getReviewableStatus()
    {
        return $this->reviewable_status;
    }

    public function setReviewableStatus($value): self
    {
        $this->reviewable_status = $value;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($value): self
    {
        $this->label = $value;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCustom()
    {
        return $this->custom;
    }

    public function setCustom($value): self
    {
        $this->custom = $value;

        return $this;
    }
}
