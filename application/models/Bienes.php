<?php 
    class bienes extends base{
        var $table;
        var $articulo;
        var $descripcion;
        var $usuario_id;

        function __construct() {
            parent::__construct();
            $this->table = "bienes";
        }

        function get_data($id){
            $sql = "Select * from ". 
            $this->table. " 
            where 
            id in (".$id.")";
            $query = $this->db->query($sql);
            return $query->result_array();
        }


        function insert($data){
            $data["created_at"] = $this->set_created_at();
            $data["updated_at"] = $this->set_updated_at();
            $this->db->insert($this->table,$data);
            if($this->db->insert_id() > 0){
                return "Elemento guardado";
            }else{
                return "Error al guardar elemento";
            }
             
        }

        function update($id,$data){
            $data["updated_at"] = $this->set_updated_at();
            $this->db->where('id',$id);
            $this->db->update($this->table,$data);
            
            $error = $this->db->error();            
            if((int)$error['code']==0){
                return "Elemento Actualizado";
            }else{
                return "Error al actualizaco elemento";
            }     
        }

        function delete ($id){
           
            $this->db->where('id',$id);
            $this->db->delete($this->table);
            
            $error = $this->db->error();            
            if((int)$error['code']==0){
                return "Elemento Eliminado";
            }else{
                return "Error al eliminar elemento";
            }     
        }

        private function set_created_at($date =""){
            if(empty($date)){
                $date= date("Y-m-d H:i:s");
            }
            $this->created_at = $date;
            return  $this->created_at;
        }

        private  function set_updated_at($date = ""){
            if(empty($date)){
                $date= date("Y-m-d H:i:s");
            }
            $this->updated_at = $date;
            return  $this->updated_at;
        }

        
    }