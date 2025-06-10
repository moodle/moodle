<?php

class PerformanceLog {

    private $start_time;

    public function start_timer() {
        $this->start_time = microtime(true);
    }

    public function stop_timer($ch) {
        $total_response_time = (microtime(true) - $this->start_time);
        $this->log($ch, $total_response_time);
    }

    protected function log($ch, $total_response_time) {
        // Override this method to implement your own logging.
    }

}

//?>