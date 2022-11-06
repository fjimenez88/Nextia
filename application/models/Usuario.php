<?php 
require(APPPATH.'models/Base.php');
class usuario extends Base{
        var $table = "usuarios" ;
        var $nombre;
        var $usuario;
        var $contrasena;

        function __construct(){
            parent::__construct(); 
            $this->load->helper("app_helper");
        }

        function createUser($Nombre = "",$Usuario ="",$contrasena = ""){
            $data["name"] = $Nombre;
            $data["user"] = $Usuario;
            $data["password"] = MD5($contrasena);
            $data["created_at"] = $this->set_created_at();
            $data["updated_at"] = $this->set_updated_at();

            $this->db->insert($this->table,$data);
            return $this->db->insert_id();
        }


        function validaUsuario($usuario = "",$contrasena = ""){

            $sql = "Select * from ". 
                            $this->table. " 
                            where 
                            user = '".$usuario."' and 
                            password = '" .md5($contrasena) ."'";
          
            $query = $this->db->query($sql);
            return $query->result_array();
            
        }
        private function set_created_at($date =""){
            if(empty($date)){
                $date= date("Y-m-d  H:i:s");
            }
            $this->created_at = $date;
            return  $this->created_at;
        }

        private  function set_updated_at($date = ""){
            if(empty($date)){
                $date= date("Y-m-d  H:i:s");
            }
            $this->updated_at = $date;
            return  $this->updated_at;
        }

    }
?>