<?php

class ModUI{

    private $container;
    private $name;
    private $lwte;

    const SEPARATOR = '_';

    public function __construct($name, $container, $lwte='lwte'){
        $this->name = $name;
        $this->container = $container;
        $this->lwte = $lwte;
    }

    public function display($update_script, $auto_reload_script=null, $auto_reload_time=0){
        $templates = $this->container->get_templates($this->name);
        $values = $this->get_values();
        $script = self::get_script($this->name, $this->container->get_scripts($this->name));
        $script .= <<<JS
function _modui_update(name, value){
    !$update_script(name, value, _modui_update_auto);
}
_modui_modified = false;
function _modui_modify(){
    _modui_modified = true;
}
function _modui_update_auto(new_data){
    _modui_modified = false;
    _modui_update_auto_body("{$this->name}", new_data._template_name, _modui_old_values, new_data, null);
    _modui_old_values = new_data;
}
function _modui_update_auto_body(name, template_name, old_data, new_data, new_data2){
    if(new_data != null && old_data != null && new_data._name == name && old_data._name == name &&
            new_data._template_name != undefined && new_data._template_name == old_data._template_name){
        for(var key in old_data){
            _modui_update_auto_body(key, new_data._template_name, old_data[key], new_data[key], new_data);
        }
    }else{
        if(!is_equal(old_data, new_data)){
            document.getElementById(new_data2._name + "--span").innerHTML = {$this->lwte}.useTemplate(template_name, new_data2);
        }
    }
}
function is_equal(old_data, new_data){
    if(old_data instanceof Object){
        for(var key in old_data){
            if(!(key in new_data)) return false;
            if(!is_equal(old_data[key], new_data[key])) return false;
        }
        return true;
    }else{
        return (old_data == new_data);
    }
}
JS;
        if($auto_reload_time !== 0 && $auto_reload_script !== null){
            $old_values = json_encode($values);
            $script .= <<<JS
_modui_old_values = $old_values;
setInterval(function(){
    !$auto_reload_script(_modui_update_auto);
}, $auto_reload_time);
JS;
        }
        return ['templates' => $templates, 'values' => $values, 'script' => $script];
    }

    public function get_values(){
        $values = $this->container->get_values($this->name);
        $values['_name'] = $this->name;
        $values['_template_name'] = $this->name;
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

    public static function get_lwte_use($template_name, $name){
        return "<span id=\"$name--span\">{use $template_name $name}</span>";
    }

    public static function get_child_name($base, $name){
        return $base . self::SEPARATOR . $name;
    }

    public static function get_script($name, $scripts){
        $value_script = isset($scripts['value']) ? $scripts['value'] : 'function(selector){return null;}';
        $event_script = isset($scripts['event']) ? $scripts['event'] : '';
        $other_script = isset($scripts['other']) ? $scripts['other'] : '';
        if(strlen($event_script) > 0){
            $event_script = "($event_script(\"$name\", _modui_update_$name, _modui_modify));";
        }
        return <<<JS
function _modui_update_$name(){
    _modui_update("$name", _modui_get_value_$name());
}
function _modui_get_value_$name(){
    return !$value_script("$name");
}
$event_script
$other_script
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
