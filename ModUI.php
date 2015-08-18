<?php

class ModUI{

    private $container;
    private $name;
    private $selector;
    private $auto_reload_time = 0;
    private $auto_reload_script = null;

    const SEPARATOR = '_';

    public function __construct($name, $container){
        $this->name = $name;
        $this->container = $container;
    }

    public function display(){
        $templates = $this->container->get_templates($this->name);
        $scripts = $this->container->get_scripts($this->name);
        $script = self::get_script($this->name, $scripts);
        $values = $this->get_values();
        if($this->auto_reload_time !== 0 && $this->auto_reload_script !== null){
            $old_values = json_encode($values);
            $script .= <<<JS
old_values = $old_values;
setInterval({$this->auto_reload_script}, {$this->auto_reload_time});
function update_auto(name, new_data, lwte){
    update_auto_body(name, new_data.template_name, old_values, new_data, null, lwte);
    old_values = new_data;
}
function update_auto_body(name, template_name, old_data, new_data, new_data2, lwte){
    if(new_data.name == name && old_data.name == name && new_data.template_name != undefined && new_data.template_name == old_data.template_name){
        for(var key in old_data){
            update_auto_body(key, new_data.template_name, old_data[key], new_data[key], new_data, lwte);
        }
    }else{
        if(old_data != new_data){
            document.getElementById(new_data2.name + "--span").innerHTML = lwte.useTemplate(template_name, new_data2);
        }
    }
}
JS;
        }
        return ['templates' => $templates, 'values' => $values, 'script' => $script];
    }

    public function get_values(){
        $values = $this->container->get_values($this->name);
        $values['name'] = $this->name;
        $values['template_name'] = $this->name;
        return $values;
    }

    public function input($params){
        header('Content-Type: application/json');
        if(isset($params['name'], $params['value'])){
            $name = $params['name'];
            $value = json_decode($params['value'], true);
            $name = self::get_name($name);
            $this->container->input($name[1], $value);
        }
        $values = $this->get_values();
        echo json_encode($values);
        exit();
    }

    public function add($component, $hook_function=null){
        $this->container->add($component, $hook_function);
    }

    public function enable_auto_reload($time, $script){
        $this->auto_reload_time = $time;
        $this->auto_reload_script = $script;
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
        $script2 = '';
        if(is_array($script)){
            $script2 = $script[1];
            $script = $script[0];
        }
        if(strlen($script) > 0){
            $script = '(' . $script . '(update_' . $name . ', "' . $name . '"));';
        }
        return <<<JS
function update_$name(){
    (update_modui("$name", get_value_$name()));
}
function get_value_$name(){
    return ($get_value_script("$name"));
}
$script
$script2
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
