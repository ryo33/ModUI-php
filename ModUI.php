<?php

class ModUI{

    private $container;
    private $name;
    private $selector;
    private static $update_script = <<<JS
JS
    ;
    const SEPARATOR = '_';

    public function __construct($name, $container){
        $this->name = $name;
        $this->container = $container;
    }

    public function display(){
        $templates = $this->container->get_templates($this->name);
        $values = $this->container->get_values($this->name);
        $values['name'] = $this->name;
        $scripts = $this->container->get_scripts($this->name);
        $script = self::get_script($this->name, $scripts);
        return ['templates' => $templates, 'values' => $values, 'script' => $script];
    }

    public function input($params){
        header('Content-Type: application/json');
        if(isset($params['name'], $params['value'])){
            $name = $params['name'];
            $value = json_decode($params['value'], true);
            $name = self::get_name($name);
            echo json_encode($this->container->input($name[1], $value));
            exit();
        }
    }

    public function add($component){
        $this->container->add($component);
    }

    public static function get_lwte_use($template_name, $name){
        return "<span id=\"$name--span\">{use $template_name $name}</span>";
    }

    public static function get_child_name($base, $name){
        return $base . self::SEPARATOR . $name;
    }

    public static function set_update_script($update_script){
        self::$update_script = $update_script;
    }

    public static function get_script($name, $scripts){
        $get_value_script = $scripts[0];
        $script = $scripts[1];
        $update_script = self::$update_script;
        return <<<JS
function update_$name(){
    (update_modui("$name", get_value_$name()));
}
function get_value_$name(){
    return ($get_value_script("$name"));
}
var update = update_$name;
var selector = "$name";
$script
JS;
    }

    public static function get_name($source){
        $name = explode(ModUI::SEPARATOR, $source, 2);
        if(strlen($name[0]) === strlen($source)){
            $remainder = '';
        }else{
            $remainder = substr($source, strlen($name[0]) + strlen(ModUI::SEPARATOR));
        }
        return [$name[0], $remainder];
    }

}
