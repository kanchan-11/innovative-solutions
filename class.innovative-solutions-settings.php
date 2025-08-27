<?php
if(!class_exists('Innovative_Solutions_Settings')){
    class Innovative_Solutions_Settings{
        public static $options;
        public function __construct(){
            self::$options = get_option('innovative_solutions_options');
        }
    }
}
?>