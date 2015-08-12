<?php

abstract class ModUIComponent{

    abstract public function get_templates($name);

    abstract public function get_value($name);

    abstract public function get_script($name);

    abstract public function input($name, $value);

}
