<?php

abstract class ModUIComponent{

    abstract public function get_template_name($name);

    abstract public function get_templates($name);

    public function get_values($name){ return []; }

    public function get_scripts($name){ return []; }

    public function input($name, $value){}

}
