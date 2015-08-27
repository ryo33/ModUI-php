<?php

class ModUIComponent{

    public function get_template_name($name){ return $name; }

    public function get_templates($name){ return [$this->get_template_name() => '']; }

    public function get_values($name){ return []; }

    public function get_scripts($name){ return []; }

    public function input($name, $value){}

}
