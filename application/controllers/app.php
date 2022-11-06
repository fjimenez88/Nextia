<?php
    class App extends CI_Controller{
        var $keySecret;
        var $jwt;
        function __construct()
        {
            parent::__construct();
            //error_reporting(0);

            header("Access-Control-Allow-Origin:*");
            header("Access-Control-Allow-Methods: GET, OPTIONS,POST,GET, PUT");
            header("Access-Control-Allow-Methods: Content-Type, Content-Length- Accept Encoding");
            $this->load->model('usuario');
            $this->load->model('bienes');
            $this->jwt = new JWT();
            
            $keySecret = "passwordSecret";
        }
        
        public function index(){
            echo "This is a laund Page";
        }
        
        public function registro(){
            if(isset($_POST)){
                $nombre = $this->input->post("nombre");
                $usuario = $this->input->post("usuario");
                $contrasena = $this->input->post("contrasena");

                if(empty($nombre)){
                    $data["success"] = false;
                    $data["msg"] = "Nombre no puede estar vacio";
                    echo json_encode($data);
                    return;
                }
                if(empty($usuario)){
                    $data["success"] = false;
                    $data["msg"] = "Usuario no puede estar vacio";
                    echo json_encode($data);
                    return;
                }

                if(empty($contrasena)){
                    $data["success"] = false;
                    $data["msg"] = "contrasena no puede estar vacio";
                    echo json_encode($data);
                    return;
                }

                $last_id  = $this->usuario->createUser($nombre,$usuario, $contrasena);            
                if($last_id > 0){
                    $data["success"] = true;
                    $data["msg"] = "Usuario Creado";
                }else{
                    $data["success"] = false;
                    $data["msg"] = "Error al registrar usuarios";
                }
            }else{
                $data["success"] = false;
                $data["msg"] = "informacion incompleta";
            }
            
            echo json_encode($data);
            return;
        }

        public function login(){
            $user=$this->input->post("usuario");
            $pass=$this->input->post("contrasena");

            $infoUser = $this->usuario->validaUsuario($user,$pass);
            if(count($infoUser) > 0){
                $time = time();
                $info = [
                    'iat' => date("d-m-Y H:i:s", $time),
                    'exp' => date("d-m-Y H:i:s", $time + 60),
                    'data' => $infoUser[0]
                ];
                $data = $this->jwt->encode($info,$this->keySecret,"HS256");
            }else{
                $data["success"] = false;
                $data["msg"] = "Usuario No encontrado";
            }

            echo json_encode($data);
            return;
        }

        private function validateToken($token){
            try{
                $verificacion =  $this->jwt->decode($token,$this->keySecret,array("HS256"));
                $verficicacion_json = $this->jwt->jsonEncode($verificacion);
                return $verficicacion_json;
            }catch(Exception $e){
                return false;
            }

        }

        private function validaVigenciaToken($headerToken= ""){
            $splitToken = explode(" ",$headerToken);
            $token = $splitToken[count($splitToken)-1];

            $respuesta = "";
            $data = $this->validateToken($token);
          
            if(!$data){
                $respuesta['valido'] = "false";
                $respuesta['mensaje'] = "Token Invalido";
            }else{
                $info = (array)$data;
                $info = json_decode($info[0],true);
                if(strtotime(date("d-m-Y H:i:s",time())) <= strtotime($info["exp"])){
                    $respuesta['valido'] = "true";
                    $respuesta['mensaje'] = "ok";
                    $respuesta['user'] = $info["data"];
                   
                }else{
                    $respuesta['valido'] = "false";
                    $respuesta['mensaje'] = "Token Expirado";
                }
            }
            return $respuesta;
        }
        
        function insert_bienes(){
            $data = $this->validaVigenciaToken($this->input->get_request_header("Authorization"));
                if($data["valido"] == "true"){
                    unset($data["valido"]);
                    unset($data["user"]["password"]);
                    $info["articulo"] = $_POST["articulo"];
                    $info["descripcion"] = $_POST["descripcion"];
                    $info["id_usuario"] = $data["user"]["id"];
                    $data["mensaje"] = $this->bienes->insert($info);
                }
            echo json_encode($data);
            return;
        }

        function update_bienes(){
            $data = $this->validaVigenciaToken($this->input->get_request_header("Authorization"));
                if($data["valido"] == "true"){
                    unset($data["valido"]);
                    unset($data["user"]["password"]);                    
                    $info[$_POST["campo"]] = $_POST["valor_campo"];
                    $data["mensaje"] = $this->bienes->update($_POST["id_articulo"],$info);
                }
            echo json_encode($data);
            return;
        }

        function delete_bienes(){
            $data = $this->validaVigenciaToken($this->input->get_request_header("Authorization"));
                if($data["valido"] == "true"){
                    unset($data["valido"]);
                    unset($data["user"]["password"]);                    
                    $data["mensaje"] = $this->bienes->delete($_POST["id_articulo"]);
                }
            echo json_encode($data);
            return;
        }

        //Se recive los parametros separados por coma para ejcutar la consulta
        function select_bienes(){
            $data = $this->validaVigenciaToken($this->input->get_request_header("Authorization"));
            if($data["valido"] == "true"){
                unset($data["valido"]);
                unset($data["user"]["password"]);                    
                $data["data"] = $this->bienes->get_data($_POST["id_articulo"]);
            }
            echo json_encode($data);
            return;
        }

        function cargacsv(){
            $data = $this->validaVigenciaToken($this->input->get_request_header("Authorization"));
            if($data["valido"] == "true"){
                unset($data["valido"]);
                unset($data["user"]["password"]);  
                $encabezado = 0;
                if(isset($_FILES["documento"])){
                    $handle  = fopen($_FILES["documento"]["tmp_name"],"r");
                    while (($datafile = fgetcsv($handle)) !== FALSE) {
                       if($encabezado <> 0){
                            // pre($datafile);
                        $info["id"] = $datafile[0];
                        $info["articulo"] = $datafile[1];
                        $info["descripcion"] =  $datafile[2];
                        $info["id_usuario"] = $data["user"]["id"];
                        $this->bienes->insert($info);
                       }
                       $encabezado +=1;
                    }   
                    $data["mensaje"] ="El documento se improto correctamente";
                }
            }
            echo json_encode($data);
            return;
           
        }

    }
?>