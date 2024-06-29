<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


require_once '../app/class/tienda.php';
require_once '../app/DB.php';

class ControladorTienda{


    function crearProductos(Request $request, Response $response, array $args): Response {
        $marca = $request->getParsedBody()['marca'];
        $precio = $request->getParsedBody()['precio'];
        $tipo = $request->getParsedBody()['tipo'];
        $modelo = $request->getParsedBody()['modelo'];
        $color = $request->getParsedBody()['color'];
        $stock = $request->getParsedBody()['stock'];

        $arraydeproductoExistente=self::chequearMarcaTipo($marca,$tipo);
        if(!empty($arraydeproductoExistente)){
            self::updetearPrecio($marca,$tipo,$precio,$stock);
        }
        else{
            $producto= new Tienda($marca, $precio, $tipo, $modelo, $color, $stock);
            self::insert($producto);
        }
        $response->getBody()->write(json_encode(['message' => 'producto Creado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200); // OK
    }
    function existenciaProductos(Request $request, Response $response, array $args): Response{
        $marca = $request->getParsedBody()['marca'];
        $tipo = $request->getParsedBody()['tipo'];
        $color = $request->getParsedBody()['color'];

       
        
        $arraydeproductoExistente=self::chequearExistencia($marca,$tipo,$color);
        var_dump($arraydeproductoExistente);
        if (!empty($arraydeproductoExistente)) {
            $response->getBody()->write(json_encode(['message' => 'Existe']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200); // OK
        
        }
        else{
            if (self::existeMarca($marca) && self::existeTipo($tipo)) {
                $mensajeError = 'No hay productos de la marca ' . $marca . ' y tipo ' . $tipo;
            } elseif (self::existeMarca($marca)) {
                $mensajeError = 'No hay productos del tipo ' . $tipo;
            } elseif (self::existeTipo($tipo)) {
                $mensajeError = 'No hay productos de la marca ' . $marca;
            } else {
                $mensajeError = 'Producto No existe';
            }

            $response->getBody()->write(json_encode(['error' => $mensajeError]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    
        
    }
    public function guardarproductoImagen($tipo,$modelo){
        $directorioCreado=false;
        if(!file_exists("ImagenesDeProductos/2024/")){
            mkdir("ImagenesDeProductos/2024/", 0777, true);
            $directorioCreado=true;
        }

        if(isset($_FILES['imagen'])) {//si hay una imagen que se extrae de FILES que no sea NULL
            $nombreArchivo = $_FILES['imagen']['name'];//extrae el nombre de mi archivo
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION); 
            $nuevoNombre = $tipo . "_" . $modelo . "." . $extension;
            $rutaArchivo = "ImagenesDeProductos/2024/" . $nuevoNombre;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaArchivo)) {
                echo "La imagen se cargó correctamente"."</br>"; 
            } else {
                echo "Error cargando imagen."."</br>"; 
            }
        } 
        else {
            echo "No hay imagen cargada."."</br>";
        }
    }
    public static function chequearExistencia($marca,$tipo,$color){
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM tienda WHERE producto_marca = :marca AND producto_tipo = :tipo AND producto_color = :color");
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':color', $color);
        $stmt->execute();
        $productosExistentes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return($productosExistentes);
    }
    public static function existeMarca($marca) {
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM tienda WHERE producto_marca = :marca");
        $stmt->bindParam(':marca', $marca);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function existeTipo($tipo) {
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM tienda WHERE producto_tipo = :tipo");
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function chequearMarcaTipo($marca,$tipo){
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM tienda WHERE producto_tipo = :tipo AND producto_marca = :marca");
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':marca', $marca);
        $stmt->execute();
        $productosExistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        var_dump($productosExistentes);   
        return($productosExistentes);

    }
    public function updetearPrecio($marca,$tipo,$nuevoPrecio,$nuevoStock){
        $conn = DB::Connect();
        try{$stmt = $conn->prepare("UPDATE tienda SET producto_precio = :nuevo_precio, producto_stock = producto_stock + :nuevo_stock 
                                WHERE producto_tipo = :tipo AND producto_marca = :marca");
        $stmt->bindParam(':nuevo_precio', $nuevoPrecio);
        $stmt->bindParam(':nuevo_stock', $nuevoStock);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':marca', $marca);
        $stmt->execute();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
}
    function traerProductos(Request $request, Response $response, array $args): Response {
        
        $listadeproductos = self::select();
        
        
        $productosArray = json_decode(json_encode($listadeproductos), true);
        $archivoCSV='productos.csv';
        ControladorUsuario::traerCSV($productosArray,$archivoCSV);
    

        $csvContenido = file_get_contents($archivoCSV);

    
        $response = $response->withHeader('Content-Type', 'text/csv') // Hay que setear los headers para que en el output pueda aparecer el csv
                            ->withHeader('Content-Disposition', 'attachment; filename="' . $archivoCSV . '"')
                            ->withStatus(200); // OK

        // Write CSV content to response body
        $response->getBody()->write($csvContenido);
        return $response;
    }

    public static function select(){
        $conn= DB::Connect();

        $select = "SELECT * FROM tienda";
        $stmt= $conn->prepare($select);
        
        try{
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return($productos);
        }
        catch (Exception $e){
            echo "Error al traer Productos" . $e->getMessage(); 

        }
    }
    public static function insert($producto) {
        try {
            $conn = DB::Connect();
            
            $producto_marca = $producto->getMarca();
            $producto_precio = $producto->getPrecio();
            $producto_tipo = $producto->getTipo();
            $producto_modelo = $producto->getModelo();
            $producto_color = $producto->getColor();
            $producto_stock = $producto->getStock();
            
            $stmt = $conn->prepare("INSERT INTO tienda (producto_marca, producto_tipo, producto_precio, producto_modelo, producto_color, producto_stock) 
                                VALUES (:producto_marca, :producto_tipo, :producto_precio, :producto_modelo, :producto_color, :producto_stock)");

     
            $stmt->bindParam(':producto_marca', $producto_marca); 
            $stmt->bindParam(':producto_tipo', $producto_tipo); 
            $stmt->bindParam(':producto_precio', $producto_precio); 
            $stmt->bindParam(':producto_modelo', $producto_modelo); 
            $stmt->bindParam(':producto_color', $producto_color); 
            $stmt->bindParam(':producto_stock', $producto_stock); 
            $stmt->execute();
        }
        catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    function updateprodtablaSQL(Request $request, Response $response, array $args): Response {
    
        
        $archivoCSV='productos.csv';


        $csvContenido = file_get_contents($archivoCSV);
        $arraynuevo=array();
        $contenidoArray= explode("\n",$csvContenido);
        array_pop($contenidoArray);//me saca el ultimo string vacio "" que aparece al final de cada CSV
        array_shift($contenidoArray);//me saca el primer array de strings que contiene los headers de mi CSV
        

        $conn = DB::Connect(); 
        $stmt1=$conn->prepare("DELETE FROM productos");
        $stmt1->execute();

        $stmtAlter = $conn->prepare("ALTER TABLE productos AUTO_INCREMENT = 1");
        $stmtAlter->execute();

        foreach ($contenidoArray as $strings){
            $arraydeString=explode(",",$strings);

            $stmt = $conn->prepare("INSERT INTO productos (producto_nombre, producto_categoria, producto_precio) VALUES (:producto_nombre, :producto_categoria, :producto_precio)");
             
            $stmt->bindParam(':producto_nombre', $arraydeString[1]); 
            $stmt->bindParam(':producto_categoria', $arraydeString[2]); 
            $stmt->bindParam(':producto_precio', $arraydeString[3]); 
        
            $stmt->execute();
        }
        return $response->withStatus(200);
    }
    
    



}


?>