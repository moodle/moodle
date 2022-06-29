<?php

namespace Packback\Lti1p3;

class LtiLineitem
{
    private $id;
    private $score_maximum;
    private $label;
    private $resource_id;
    private $resource_link_id;
    private $tag;
    private $start_date_time;
    private $end_date_time;

    public function __construct(array $lineitem = null)
    {
        $this->id = $lineitem['id'] ?? null;
        $this->score_maximum = $lineitem['scoreMaximum'] ?? null;
        $this->label = $lineitem['label'] ?? null;
        $this->resource_id = $lineitem['resourceId'] ?? null;
        $this->resource_link_id = $lineitem['resourceLinkId'] ?? null;
        $this->tag = $lineitem['tag'] ?? null;
        $this->start_date_time = $lineitem['startDateTime'] ?? null;
        $this->end_date_time = $lineitem['endDateTime'] ?? null;
    }

    public function __toString()
    {
        // Additionally, includes the call back to filter out only NULL values
        return json_encode(array_filter([
            'id' => $this->id,
            'scoreMaximum' => $this->score_maximum,
            'label' => $this->label,
            'resourceId' => $this->resource_id,
            'resourceLinkId' => $this->resource_link_id,
            'tag' => $this->tag,
            'startDateTime' => $this->start_date_time,
            'endDateTime' => $this->end_date_time,
        ], '\Packback\Lti1p3\Helpers\Helpers::checkIfNullValue'));
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new()
    {
        return new LtiLineitem();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($value)
    {
        $this->label = $value;

        return $this;
    }

    public function getScoreMaximum()
    {
        return $this->score_maximum;
    }

    public function setScoreMaximum($value)
    {
        $this->score_maximum = $value;

        return $this;
    }

    public function getResourceId()
    {
        return $this->resource_id;
    }

    public function setResourceId($value)
    {
        $this->resource_id = $value;

        return $this;
    }

    public function getResourceLinkId()
    {
        return $this->resource_link_id;
    }

    public function setResourceLinkId($value)
    {
        $this->resource_link_id = $value;

        return $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($value)
    {
        $this->tag = $value;

        return $this;
    }

    public function getStartDateTime()
    {
        return $this->start_date_time;
    }

    public function setStartDateTime($value)
    {
        $this->start_date_time = $value;

        return $this;
    }

    public function getEndDateTime()
    {
        return $this->end_date_time;
    }

    public function setEndDateTime($value)
    {
        $this->end_date_time = $value;

        return $this;
    }
}
