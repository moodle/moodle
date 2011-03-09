<?php
/**
 * Plan Based Abstract Converter
 */
abstract class plan_converter extends base_converter {

    protected $plan;

    /**
     * @return convert_plan
     */
    public function get_plan() {
        if ($this->plan instanceof convert_plan) {
            $this->plan = new convert_plan($this);
        }
        return $this->plan;
    }

    abstract public function build_plan();

    public function execute() {
        $this->get_plan()->build();  // Ends up calling $this->build_plan()
        $this->get_plan()->execute();
    }

    public function destroy() {
        parent::destroy();
        $this->get_plan()->destroy();
    }
}