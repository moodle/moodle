<?php
/**
 * Plan Based Abstract Converter
 */
abstract class plan_converter extends base_converter {

    /**
     * @var convert_plan
     */
    protected $plan;

    /**
     * @var progressive_parser
     */
    protected $xmlparser;

    /**
     * @var convert_structure_parser_processor
     */
    protected $xmlprocessor;

    /**
     * @var array
     */
    protected $pathelements = array();  // Array of pathelements to process

    // @todo needed? redo?
    protected $pathlock;      // Path currently locking processing of children

    // @todo IDK what this is really...
    const SKIP_ALL_CHILDREN = -991399; // To instruct the dispatcher about to ignore
                                       // all children below path processor returning it

    /**
     * @return convert_plan
     */
    public function get_plan() {
        if (!$this->plan instanceof convert_plan) {
            $this->plan = new convert_plan($this);
        }
        return $this->plan;
    }

    abstract public function build_plan();

    public function execute() {
        $this->get_plan()->build();  // Ends up calling $this->build_plan()
        $this->get_plan()->execute();
        $this->xmlparser->process(); // @todo When to really do this?
    }

    public function destroy() {
        parent::destroy();
        $this->get_plan()->destroy();
    }

    public function add_structures($processingobject, array $structures) {
        // Override if using class convert_structure_step
        $this->prepare_pathelements($processingobject, $structures);

        // Add pathelements to processor
        foreach ($this->pathelements as $element) {
            $this->xmlprocessor->add_path($element->get_path(), $element->is_grouped());
        }
    }

    /**
     * Prepare the pathelements for processing, looking for duplicates, applying
     * processing objects and other adjustments
     */
    protected function prepare_pathelements($processingobject, $elementsarr) {
        // First iteration, push them to new array, indexed by name
        // detecting duplicates in names or paths
        $names = array();
        $paths = array();
        foreach($elementsarr as $element) {
            if (!$element instanceof convert_path_element) {
                throw new restore_step_exception('restore_path_element_wrong_class', get_class($element)); // @todo Change exception
            }
            if (array_key_exists($element->get_name(), $names)) {
                throw new restore_step_exception('restore_path_element_name_alreadyexists', $element->get_name()); // @todo Change exception
            }
            if (array_key_exists($element->get_path(), $paths)) {
                throw new restore_step_exception('restore_path_element_path_alreadyexists', $element->get_path()); // @todo Change exception
            }
            $names[$element->get_name()] = true;
            $paths[$element->get_path()] = $element;
        }
        // Now, for each element not having one processing object, if
        // not child of grouped element, assign $this (the step itself) as processing element
        // Note method must exist or we'll get one @restore_path_element_exception
        foreach($paths as $key => $pelement) {
            if ($pelement->get_processing_object() === null && !$this->grouped_parent_exists($pelement, $paths)) {
                $paths[$key]->set_processing_object($processingobject);
            }
        }
        // Done, add them to pathelements (dupes by key - path - are discarded)
        $this->pathelements = array_merge($this->pathelements, $paths);
    }

    /**
     * Given one pathelement, return true if grouped parent was found
     */
    protected function grouped_parent_exists($pelement, $elements) {
        foreach ($elements as $element) {
            if ($pelement->get_path() == $element->get_path()) {
                continue; // Don't compare against itself
            }
            // If element is grouped and parent of pelement, return true
            if ($element->is_grouped() and strpos($pelement->get_path() .  '/', $element->get_path()) === 0) {
                return true;
            }
        }
        return false; // no grouped parent found
    }

    /**
     * Receive one chunk of information form the xml parser processor and
     * dispatch it, following the naming rules
     */
    final public function process($data) {
        if (!array_key_exists($data['path'], $this->pathelements)) { // Incorrect path, must not happen
            throw new restore_step_exception('restore_structure_step_missing_path', $data['path']); // @todo Change exception
        }
        $element = $this->pathelements[$data['path']];
        $object = $element->get_processing_object();
        $method = $element->get_processing_method();
        $rdata  = null;
        if (empty($object)) { // No processing object defined
            throw new restore_step_exception('restore_structure_step_missing_pobject', $object); // @todo Change exception
        }
        // Release the lock if we aren't anymore within children of it
        if (!is_null($this->pathlock) and strpos($data['path'], $this->pathlock) === false) {
            $this->pathlock = null;
        }
        if (is_null($this->pathlock)) { // Only dispatch if there isn't any lock
            $rdata = $object->$method($data['tags']); // Dispatch to proper object/method
        }

        // If the dispatched method returns SKIP_ALL_CHILDREN, we grab current path in order to
        // lock dispatching to any children
        if ($rdata === self::SKIP_ALL_CHILDREN) {
            // Check we haven't any previous lock
            if (!is_null($this->pathlock)) {
                throw new restore_step_exception('restore_structure_step_already_skipping', $data['path']); // @todo Change exception
            }
            // Set the lock
            $this->pathlock = $data['path'] . '/'; // Lock everything below current path

        // Continue with normal processing of return values
        } else if ($rdata !== null) { // If the method has returned any info, set element data to it
            $element->set_data($rdata);
        } else {               // Else, put the original parsed data
            $element->set_data($data);
        }
    }
}