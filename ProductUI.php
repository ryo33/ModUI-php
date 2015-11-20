<?php

abstract class ProductUI extends ModUIComponent{

    public function get_template_name($name){ return $this->get_component()->get_template_name($name); }

    public function get_templates($name){ return $this->get_component()->get_templates($name); }

    public function get_values($name){ return $this->get_component()->get_templates($name); }

    public function get_scripts($name){ return $this->get_component()->get_templates($name); }

    public function input($name, $value){ $this->get_component()->input($name, $value); }

    protected abstract function get_component();

}
