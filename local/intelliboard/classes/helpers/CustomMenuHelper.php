<?php
namespace local_intelliboard\helpers;

class CustomMenuItem
{
    public $text = "";
    public $url = "";
    public $tooltip = "";

    public function __construct($text, $url = "", $tooltip = "") {
        $this->text = $text;
        $this->url = $url;
        $this->tooltip = $tooltip;
    }

    public function divider()
    {
        return new CustomMenuItem("###");
    }

    public function getItem()
    {
        $item = "";
        if ($this->text) {
            $item = $this->text;
            $item .= !empty($this->url) ? '|' . $this->url : '';
            $item .= !empty($this->tooltip) ? '|' . $this->tooltip : '';
        }
        return $item;
    }

    public function exists()
    {
        global $CFG;
        $item = $this->getItem();
        if (isset($CFG->custommenuitems) && !empty($item)) {
            return strpos($CFG->custommenuitems, $this->getItem()) !== false;
        }
        return true;
    }
}

class CustomMenuHelper extends CustomMenuItem
{
    public $items;

    public function __construct($text, $url ="", $tooltip = "")
    {
        parent::__construct($text, $url ="", $tooltip = "");
        $this->items = array();
    }

    public function add($text, $url ="", $tooltip = "")
    {
        $this->items[] = new CustomMenuItem($text, $url, $tooltip);
        return $this;
    }

    public function divider()
    {
        return $this->addItem("###");
    }

    public function getMenu()
    {
        $menu = "";
        if (!$this->exists()) {
            $menu .= $this->getItem();
            if (!empty($menu)) {
                $menu = "\n" . $menu;
                foreach ($this->items as $item) {
                    $menu .= "\n-" . $item->getItem();
                }
            }
        }
        return $menu;
    }

    public function setupMenu()
    {
        global $CFG;
        if (get_config('local_intelliboard', 'custommenuitem') && isset($CFG->custommenuitems)) {
            $CFG->custommenuitems .= $this->getMenu();
        }
    }
}

