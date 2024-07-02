<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


require_once '../app/class/usuario.php';
require_once '../app/DB.php';
require_once '../app/utils/AutentificadorJWT.php';

class ControladorUsuario{


    function registroUsuario(Request $request, Response $response, array $args): Response {
        $mail = $request->getParsedBody()['mail'];
        $nombre_usuario = $request->getParsedBody()['usuario'];
        $contraseña = $request->getParsedBody()['contraseña'];
        $perfil = $request->getParsedBody()['perfil'];
        
        if($mail == NULL || $nombre_usuario != NULL || $contraseña == NULL || $perfil == NULL ){
            $response->getBody()->write(json_encode(['message' => 'Uno o mas campos se encuentran vacíos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        else{
        $existenciaUsuario=self::chequearExistenciaUsuario($mail,$nombre_usuario);

        if(!empty($existenciaUsuario)){
            $response->getBody()->write(json_encode(['message' => 'Usuario o Mail ya se encuentra registrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        else{
            $usuario= new Usuario($mail,$nombre_usuario,$contraseña,$perfil);

        $resultado = self::insertRegistroUsuarios($usuario);
        if($resultado == true){
            $response->getBody()->write(json_encode(['message' => 'Registro creado con exito']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }else{
            $response->getBody()->write(json_encode(['message' => 'No se pudo realizar el registro']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        }
    }
        
}
function descargarUsuariosPDF(Request $request, Response $response, array $args): Response {
    $listadeventas = self::select();
    $ventasArray = json_decode(json_encode($listadeventas), true);

    
    $tempFile = tmpfile();
    $csvFilePath = stream_get_meta_data($tempFile)['uri'];

    $output = fopen($csvFilePath, 'w');
    if (!empty($ventasArray)) {
        $columnas = array_keys($ventasArray[0]);
        fputcsv($output, $columnas);
    }
    foreach ($ventasArray as $valores) {
        fputcsv($output, $valores);
    }
    fclose($output);

    
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 7);
    $imagePath = 'C:\xampp\htdocs\Parcial2\app\ImagenesDeUsuarios\2024\loquito_empleado_30 _ Jun _ 2024.JPG';
    $imagePath1 = 'C:\xampp\htdocs\Parcial2\app\ImagenesDeUsuarios\2024\sadasda_admin_29 _ Jun _ 2024.JPG';
    $imagePath2 = 'C:\xampp\htdocs\Parcial2\app\ImagenesDeUsuarios\2024\tobias23_admin_29 _ Jun _ 2024.JPG'; 

    
    $pdf->Image($imagePath, 137, 25, 20);
    $pdf->Image($imagePath1, 137, 40, 20);
    $pdf->Image($imagePath2, 137, 55, 20);

    // Open the CSV file
    if (($handle = fopen($csvFilePath, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            foreach ($data as $cell) {
                $pdf->Cell(25, 15, $cell, 1);
            }
            $pdf->Ln();
        }
        fclose($handle);
    }

   
    $pdfContent = $pdf->Output('S');

    
    $response = $response->withHeader('Content-Type', 'application/pdf')
                         ->withHeader('Content-Disposition', 'attachment; filename="usuario.pdf"')
                         ->withStatus(200);

    
    $response->getBody()->write($pdfContent);

    
    fclose($tempFile);

    return $response;
}
public static function select(){
    $conn= DB::Connect();

    $select = "SELECT * FROM usuarios";
    $stmt= $conn->prepare($select);
    
    try{
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return($usuarios);
    }
    catch (Exception $e){
        echo "Error al traer usuarios" . $e->getMessage(); 

    }
}
public function loginUsuario($request, $response, $args) {
            
            $parsedBody = $request->getParsedBody();
            $username = $parsedBody['username'];
            $password = $parsedBody['password'];
            $perfil = $parsedBody['perfil'];
            
            $conn = DB::Connect();
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE u_usuario = '$username' AND u_contraseña = '$password' AND u_perfil = '$perfil'");
            $stmt->execute();
            $vsExistentes = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($vsExistentes)) {
                $datos=(array('username' => $username, 'perfil' => $perfil));
                
                $jwt = AutentificadorJWT::CrearToken($datos);
                $payload = json_encode(array('jwt' => $jwt));
                $response->getBody()->write(json_encode($payload));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                
            } else {
                $response->getBody()->write(json_encode(['Error-> Usuario no registrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        }
public static function chequearExistenciaUsuario($mail,$usuario){
        $conn = DB::Connect();
        $stmt1 = $conn->prepare("SELECT * FROM usuarios WHERE u_mail = :mail OR u_usuario = :usuario ");
        $stmt1->bindParam(':mail', $mail);
        $stmt1->bindParam(':usuario', $usuario);

        $stmt1->execute();
        $usuarioExistente = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $usuarioExistente;
        var_dump($usuarioExistente);
}
public static function guardarusuarioImagen($nombre_usuario,$perfil,$fecha_imagen){
        $directorioCreado=false;
        if(!file_exists("ImagenesDeUsuarios/2024/")){
            mkdir("ImagenesDeUsuarios/2024/", 0777, true);
            $directorioCreado=true;
        }
        $fecha = str_replace('/','_',$fecha_imagen);
        

        if(isset($_FILES['foto'])) {//si hay una imagen que se extrae de FILES que no sea NULL
            $nombreArchivo = $_FILES['foto']['name'];//extrae el nombre de mi archivo
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION); 
            $nuevoNombre = $nombre_usuario . "_" . $perfil. "_". $fecha . "." . $extension;
            $rutaArchivo = "ImagenesDeUsuarios/2024/" . $nuevoNombre;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaArchivo)) {
                echo "La imagen se cargó correctamente"."</br>"; 
            } else {
                echo "Error cargando imagen."."</br>"; 
            }
        } 
        else {
            echo "No hay imagen cargada."."</br>";
        }
    }

    public static function insertRegistroUsuarios($usuario) {
        date_default_timezone_set("America/Argentina/Buenos_Aires"); 
        $u_fecha_de_alta = date("d / M / o");
      
        try {
          
      
          $u_mail = $usuario->getMail();
          $u_usuario = $usuario->getUsuario();
          $u_contraseña = $usuario->getContraseña(); // Ensure this matches the method name in your class
          $u_perfil = $usuario->getPerfil();
      
          var_dump($u_mail);
          var_dump($u_usuario);
          var_dump($u_contraseña);
          var_dump($u_perfil);
          var_dump($u_fecha_de_alta);

        $conn = DB::Connect();
         $stmt = $conn->prepare("INSERT INTO usuarios (u_mail, u_usuario, u_contraseña, u_perfil, u_fecha_de_alta) 
                                  VALUES (:u_mail, :u_usuario, '$u_contraseña', '$u_perfil' , '$u_fecha_de_alta')"); // Si no lo inyecto a esos valores no me deja insertarlos
      
          $stmt->bindParam(':u_mail', $u_mail); 
          $stmt->bindParam(':u_usuario', $u_usuario); 
          
      
          $stmt->execute();
      
          self::guardarusuarioImagen($u_usuario, $u_perfil, $u_fecha_de_alta);
          return true;
        } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
          return false;
        }
      }
    
    



}


?>