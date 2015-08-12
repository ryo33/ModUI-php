<?php

class ModUI{

    private $components;
    const SEPARATOR = '-';

    public function __construct(){
        $this->components = [];
    }

    public function display($name){
        $template = '';
        foreach($this->components as $key => $component){
            $template .= '{use ' . $this->get_child_name($name, $key) . ' ' . $this->get_child_name($name, $key) . '}';
        }
        $script = '';
        foreach($this->components as $key => $component){
            $script .= 'function(){scripts["' . $key . '"]();}';
        }
        $templates = [$name=>$template];
        $values = [];
        $scripts = [$script];
        foreach($this->components as $key => $component){
            $templates = array_merge($templates, $component->get_templates($this->get_child_name($name, $key)));
            $scripts = array_merge($scripts, $component->get_scripts($this->get_child_name($name, $key)));
            $values[$this->get_child_name($name, $key)] = array_merge(['name' => $this->get_child_name($name, $key)], $component->get_values($this->get_child_name($name, $key)));
        }
        $templates = array_unique($templates);
        return ['templates' => $templates, 'values' => $values, 'scripts' => $scripts];
    }

    public function input($name, $value){
        $result = get_name($name);
        $result = get_name($result[1]);
        $this->components[$result[0]]->input($result[1], $value);
    }

    public function add($component){
        $this->components[] = $component;
    }

    public static function get_child_name($base, $name){
        return $base . self::SEPARATOR . $name;
    }

    public static function get_name($source){
        $name = explode(self::SEPARATOR, $source, 1);
        return [$name[0], substr($source, strlen($name[0]))];
    }

}
