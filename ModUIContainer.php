<?php

abstract class ModUIContainer extends ModUIComponent{

    protected $components;
    public function __construct(){
        $this->components = [];
    }

    public function add($component){
        $this->components[] = $component;
    }

    public function get_templates($name){
        $templates = [$this->get_template_name($name) => $this->get_template($name)];
        foreach($this->components as $key => $component){
            $templates = array_merge($templates, $component->get_templates(ModUI::get_child_name($name, $key)));
        }
        return $templates;
    }

    abstract protected function get_template($name);

    public function get_values($name){
        $values = ['name' => $name];
        foreach($this->components as $key => $component){
            $values[ModUI::get_child_name($name, $key)] =
                array_merge(['name' => ModUI::get_child_name($name, $key)], $component->get_values(ModUI::get_child_name($name, $key)));
        }
        return $values;
    }

    public function get_scripts($name){
        $script = '';
        foreach($this->components as $key => $component){
            $scripts = $component->get_scripts(ModUI::get_child_name($name, $key));
            $script .= ModUI::get_script(ModUI::get_child_name($name, $key), $scripts);
        }
        return [$this->get_update_script($name), $script];
    }

    abstract protected function get_update_script($name);

    public function input($name, $value){
        $result = ModUI::get_name($name);
        $this->components[$result[0]]->input($result[1], $value);
    }

    public static function get_name($source){
        $name = explode(ModUI::SEPARATOR, $source, 1);
        return [$name[0], substr($source, strlen($name[0]) + strlen(ModUI::SEPARATOR))];
    }

}
