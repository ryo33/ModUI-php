<?php

class ModUI{

    private $components;
    const SEPARATOR = '-';

    public function __construct(){
        $this->components = [];
        $this->scripts = [];
    }

    public function display($name){
        $templates = [];
        $values = [];
        $scripts = [];
        foreach($this->components as $key => $component){
            $templates = array_merge($templates, $component->get_templates($this->get_name($name, $key)));
            $values[$key] = $component->get_value($this->get_name($name, $key));
            $scripts = array_merge($scripts, $component->get_scripts($this->get_name($name, $key)));
        }
        $templates = array_unique($templates);
        return [$templates, $values, $script];
    }

    public function get_script(){
        foreach($this->scripts as $key => $script){
            $values[$key] = $script->get_value($this->get_name($name, $key));
        }
    }

    public function input($name, $value){
        $result = get_name($name);
        $result = get_name($result[1]);
        $components[$result[0]]->input($result[1], $value);
    }

    public static function get_name($base, $name){
        return $base . self::SEPARATOR . $name;
    }

    public static function get_name($source){
        $name = explode(self::SEPARATOR, $source, 1);
        return [$name[0], substr($source, strlen($name))];
    }

}
