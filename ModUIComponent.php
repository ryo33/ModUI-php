<?php

abstract class ModUIComponent{

    abstract public function get_template_name($name);

    abstract public function get_templates($name);

    abstract public function get_values($name);

    public function get_scripts($name){ return []; }

    abstract public function input($name, $value);

}
