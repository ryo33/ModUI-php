<?php

class NormalContainer extends ModUIContainer{

    public function get_template_name($name){
        return $name;
    }

    protected function get_template($name){
        $template = '';
        foreach($this->components as $key => $component){
            $template .= ModUI::get_lwte_use($component->get_template_name(ModUI::get_child_name($name, $key)), ModUI::get_child_name($name, $key));
        }
        return $template;
    }

    protected function get_update_script($name){
        $script = '';
        return $script;
    }

}
