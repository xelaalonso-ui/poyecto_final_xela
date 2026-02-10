<?php
require_once "db_connection.php";
class usuario
{
    
    private $id;
    private $nombre;
    private $fecha_nacimiento;
    private $correo;
    private $clave;
   
    private $nombre_imagen;
    private $imagen;
    private $tipo;
 private $rol;



  
    function crearUsuario($nombre, $fecha_nacimiento, $correo, $clave,$nombre_imagen,$imagen,$tipo,$rol)
    {
        $this->nombre = $nombre;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->correo = $correo;
        $this->clave = $clave;
     
        $this->nombre_imagen = $nombre_imagen;
        $this->imagen= $imagen;
        $this->tipo=$tipo; 
          $this->rol = $rol;
    }
   
    function editarUsuario($id,$nombre, $fecha_nacimiento, $correo, $clave,$nombre_imagen,$imagen,$tipo)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->correo = $correo;
        $this->clave = $clave;
     
        $this->nombre_imagen = $nombre_imagen;
        $this->imagen= $imagen;
        $this->tipo=$tipo; 
         
    }

    
function obtenerUsuario($id){
    global $db;

    $sql="SELECT * FROM usuarios WHERE id=?";
    $stmt=mysqli_prepare($db,$sql);
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);

    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

   
    function insertar()
    {
       
        include("db_connection.php");

        
        $query = "insert into usuario(nombre,fecha_nacimiento,correo,clave,nombre_imagen,imagen,tipo_imagen,rol) values ('" . $this->nombre . "','" . $this->fecha_nacimiento . "','" . $this->correo . "','" . $this->clave . "','" . $this->nombre_imagen . "','".$this->imagen."','".$this->tipo."','".$this->rol."');";

        if (!mysqli_query($db, $query)) {
            exit(mysqli_error($db));
        }
        mysqli_close($db);

        return $query;
    }


    
    function rellenardatos()
    {
       
        include("db_connection.php");

        $query = "select * FROM usuario WHERE id=" . $this->id;

        
        if (!$result = mysqli_query($db, $query)) {
            exit(mysqli_error($db));
        } else {
            $fila = mysqli_fetch_assoc($result);
                $this->nombre = $fila['nombre'];
                $this->fecha_nacimiento = $fila['fecha'];
                $this->correo = $fila['correo'];
                $this->clave = $fila['clave'];
                $this->imagen = $fila['imagen'];
                $this->rol = $fila['rol'];
        }

        mysqli_free_result($result);

        mysqli_close($db);

        return $data;
    }

   
    function eliminar(){

        include_once('db_connection.php');

   $id=$_GET['id']; 
    
    
    
    
    $query="DELETE FROM usuario WHERE id=$this->id;";
    if(!mysqli_query($db,$query)){
        exit(mysqli_error($db));
    }
    
    mysqli_close($db);
    }

    function editar(){
        
        include_once('db_connection.php');
        

        $modificarClave='';
        if($this->clave<>''){
            $modificarClave=",clave='".$this->clave."'";
        }
        $modificarimagen='';
        if($this->nombre_imagen<>''){
            $modificarimagen=",imagen=.'".$this->imagen."' ,tipo_imagen='".$this->imagen."', nombre_imagen='".$this->nombre_imagen."'";
        }

        $query="UPDATE usuario SET nombre='".$this->nombre."',fecha_nacimiento='".$this->fecha_nacimiento."',correo='".$this->correo ."'$modificarClave  $modificarimagen WHERE id='".$this->id."';";
        if(!mysqli_query($db,$query)){
            exit(mysqli_error($db));
        }
        
        mysqli_close($db);
    }
    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of nombre
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @return  self
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get the value of fecha_nacimiento
     */ 
    public function getFecha_nacimiento()
    {
        return $this->fecha_nacimiento;
    }

    /**
     * Set the value of fecha_nacimiento
     *
     * @return  self
     */ 
    public function setFecha_nacimiento($fecha_nacimiento)
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

        return $this;
    }

    /**
     * Get the value of correo
     */ 
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set the value of correo
     *
     * @return  self
     */ 
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get the value of clave
     */ 
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * Set the value of clave
     *
     * @return  self
     */ 
    public function setClave($clave)
    {
        $this->clave = $clave;

        return $this;
    }

    /**
     * Get the value of rol
     */ 
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Set the value of rol
     *
     * @return  self
     */ 
    public function setRol($rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get the value of imagen
     */ 
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set the value of imagen
     *
     * @return  self
     */ 
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }
}
