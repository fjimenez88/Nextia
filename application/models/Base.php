<?php 
    class Base extends CI_Model{
        var $id;
        var $created_at;
        var $updated_at;

        function __construct() {
            parent::__construct();
        }

        private function set_created_at($date =""){
            if(empty($date)){
                $date= date("Y-m-d");
            }
            $this->created_at = $date;
            return  $this->created_at;
        }

        private  function set_updated_at($date = ""){
            if(empty($date)){
                $date= date("Y-m-d");
            }
            $this->updated_at = $date;
            return  $this->updated_at;
        }
    }
?>