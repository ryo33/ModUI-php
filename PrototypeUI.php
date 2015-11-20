<?php

class PrototypeUI extends ModUIComponent{

    public function __construct($args){
        foreach($args as $key => $arg){
            $this->$key = $arg;
        }
    }

    public function get_template_name($name){
        return (isset($this->template_name))? call_user_func($this->template_name, $this, $name): $name;
    }

    public function get_templates($name){
        return call_user_func($this->templates, $this, $name);
    }

    public function get_values($name){
        return (isset($this->values))? call_user_func($this->values, $this, $name): [];
    }

    public function get_scripts($name){
        return (isset($this->scripts))? call_user_func($this->scripts, $this, $name): [];
    }

    public function input($name, $value){
        if(isset($this->input)) call_user_func($this->input, $this, $name, $value);
    }

}
