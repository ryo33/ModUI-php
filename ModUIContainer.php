<?php

abstract class ModUIContainer extends ModUIComponent{

    protected $component;
    protected $hooks;
    public function __construct(){
        $this->components = [];
        $this->hooks = [];
    }

    public function add($component, $hook_function=null){
        $this->components[] = $component;
        if($hook_function !== null){
            $this->hooks[] = $hook_function;
        }else{
            $this->hooks[] = function(){};
        }
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
                array_merge(['name' => ModUI::get_child_name($name, $key), 'template_name' => $component->get_template_name(ModUI::get_child_name($name, $key))],
                    $component->get_values(ModUI::get_child_name($name, $key)));
        }
        return $values;
    }

    public function get_scripts($name){
        $get_value_script = [];
        $script = '';
        $template_names = [];
        foreach($this->components as $key => $component){
            $scripts = $component->get_scripts(ModUI::get_child_name($name, $key));
            $script .= ModUI::get_script(ModUI::get_child_name($name, $key), $scripts);
            $child_name = ModUI::get_child_name($name, $key);
            $get_value_script[] = "\"$key\": get_value_$child_name()";
        }
        $get_value_script = 'function($name){return {' . implode(', ', $get_value_script) . '};}';
        return [$get_value_script, $this->get_script($name) . $script];
    }

    abstract protected function get_script($name);

    public function input($name, $value){
        if(strlen($name) !== 0){
            $result = ModUI::get_name($name);
            $this->components[$result[0]]->input($result[1], $value);
            $this->hooks[$result[0]]();
        }else{
            foreach($this->components as $key => $component){
                $component->input('', $value[$key]);
                $this->hooks[$key]();
            }
        }
    }

}
