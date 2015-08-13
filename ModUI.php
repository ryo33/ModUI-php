<?php

class ModUI{

    private $container;
    const SEPARATOR = '_';

    public function __construct($container){
        $this->container = $container;
    }

    public function display($name){
        $templates = $this->container->get_templates($name);
        $values = $this->container->get_values($name);
        $values['name'] = $name;
        $scripts = $this->container->get_scripts($name);
        $script = self::get_script($name, $scripts);
        return ['templates' => $templates, 'values' => $values, 'script' => $script];
    }

    public function get_lwte_use($template_name, $name){
        return '{use ' . $template_name . ' ' . $name . '}';
    }

    public function input($name, $value){
        $this->container->input($name, $value);
    }

    public function add($component){
        $this->container->add($component);
    }

    public static function get_child_name($base, $name){
        return $base . self::SEPARATOR . $name;
    }

    public static function get_script($name, $scripts){
        $update_script = $scripts[0];
        $script = $scripts[1];
        return <<<JS
function update_$name(){
    var selector = "$name";
    $update_script
}
var update = update_$name;
var selector = "$name";
$script
JS;
    }

}
