<?php

declare(strict_types=1);

namespace SimpleSAML\Module\admin\Controller;

use SimpleSAML\Locale\Translate;
use SimpleSAML\Module;
use SimpleSAML\XHTML\Template;

/**
 * A class to handle the menu in admin pages.
 *
 * @package SimpleSAML\Module\admin
 */
final class Menu
{
    /** @var array */
    private $options;


    /**
     * Menu constructor.
     *
     * Initialize the menu with some default admin options, and call a hook for anyone willing to extend it.
     */
    public function __construct()
    {
        $this->options = [
            'main' => [
                'url' => Module::getModuleURL('admin/'),
                'name' => Translate::noop('Configuration'),
            ],
            'test' => [
                'url' => Module::getModuleURL('admin/test'),
                'name' => Translate::noop('Test'),
            ],
            'federation' => [
                'url' => Module::getModuleURL('admin/federation'),
                'name' => Translate::noop('Federation')
            ]
        ];
    }


    /**
     * Add a new option to this menu.
     *
     * If an option with the same $id already exists, it will be overwritten. Otherwise, the option is appended. Note
     * that if the name of the option needs translation, you need to prepare for translation on your own (e.g. by
     * registering your module as a translation domain in the template).
     *
     * @param string $id The identifier of this option.
     * @param string $url The URL this option points to.
     * @param string $name The name of the option for display purposes.
     * @return void
     */
    public function addOption($id, $url, $name)
    {
        $this->options[$id] = [
            'url' => $url,
            'name' => $name,
        ];
    }


    /**
     * Inserts this menu into a template.
     *
     * The menu will be inserted into the "data" of the template, in the form of an array, where the key for each
     * element is the identifier of the option (the default theme will compare this ID when determining if a menu
     * option is currently selected), and the value itself is also an array with two keys:
     *
     *   - url: The URL this option points to.
     *   - name: The name of the option for display purposes.
     *
     * This method will call the "adminmenu" hook, allowing modules to extend the menu by adding new options. If you
     * are adding an option and need to translate its name, you need to add the translations to your own module, and
     * add your module as a translation domain to the template object:
     *
     *   $template->getLocalization()->addModuleDomain('mymodule');
     *
     * @param \SimpleSAML\XHTML\Template $template The template we should insert this menu into.
     *
     * @return \SimpleSAML\XHTML\Template The template with the added menu.
     */
    public function insert(Template $template)
    {
        $template->data['menu'] = $this->options;
        Module::callHooks('adminmenu', $template);
        return $template;
    }
}
