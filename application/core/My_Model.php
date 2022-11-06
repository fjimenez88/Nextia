<?php 
    class My_Model extends CI_Model{
        var $id;
        var $created_at;
        var $updated_at;

        function __construct() {
            parent::__construct();
        }
    }
?>