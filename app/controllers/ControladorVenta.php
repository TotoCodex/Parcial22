<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


require_once '../app/class/venta.php';
require_once '../app/DB.php';

class ControladorVenta{


    function crearVenta(Request $request, Response $response, array $args): Response {
        $mail = $request->getParsedBody()['mail'];
        $marca = $request->getParsedBody()['marca'];
        $tipo = $request->getParsedBody()['tipo'];
        $modelo = $request->getParsedBody()['modelo'];
        $color = $request->getParsedBody()['color'];
        $stock = $request->getParsedBody()['stock'];
        

        $arraydevExistente=self::chequearExistencia($marca,$tipo,$modelo,$color);
        var_dump($arraydevExistente);
        if(!empty($arraydevExistente)){
            if (($arraydevExistente['producto_stock']) > 0){
                self::updetearStock($marca,$tipo,$modelo,$color,$stock);
                $venta= new Venta($mail,$marca, $tipo, $modelo, $color, $stock);
                self::insertVenta($venta);
                date_default_timezone_set("America/Argentina/Buenos_Aires"); 
                $fecha_imagen = date("d / M / o");
                self::guardarventaImagen($marca,$tipo,$modelo,$mail,$fecha_imagen);
                $response->getBody()->write(json_encode(['message' => 'Venta Creado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
            else
            {
                $response->getBody()->write(json_encode(['message' => 'Stock Inexistente']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
        }
        else{
            $response->getBody()->write(json_encode(['message' => 'Producto no Encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    
        
    }
    function existenciaProductos(Request $request, Response $response, array $args): Response{
        $marca = $request->getParsedBody()['marca'];
        $tipo = $request->getParsedBody()['tipo'];
        $color = $request->getParsedBody()['color'];

       
        
        $arraydeproductosExistente=self::chequearExistencia($marca,$tipo,$color);
        var_dump($arraydeproductosExistente);
        if (!empty($arraydeproductosExistente)) {
            $response->getBody()->write(json_encode(['message' => 'Existe']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200); // OK
        
        }
        else{
            if (self::existeMarca($marca) && self::existeTipo($tipo)) {
                $mensajeError = 'No hay Productos de la marca ' . $marca . ' y tipo ' . $tipo;
            } elseif (self::existeMarca($marca)) {
                $mensajeError = 'No hay Productos del tipo ' . $tipo;
            } elseif (self::existeTipo($tipo)) {
                $mensajeError = 'No hay Productos de la marca ' . $marca;
            } else {
                $mensajeError = 'Producto No existe';
            }

            $response->getBody()->write(json_encode(['error' => $mensajeError]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    
        
    }
    function productosVendidos(Request $request, Response $response, array $args): Response {
        
        $fecha = $request->getQueryParams()['fecha'];
        if($fecha == NULL){
            date_default_timezone_set("America/Argentina/Buenos_Aires"); 
            $fecha= date("d / M / o");
            $fecha=explode('/',$fecha);
            
            $fecha_a=($fecha[0]-1);
            $listadeVentas = self::chequearProductosVendidos($fecha_a." /". $fecha[1]. "/".$fecha[2]);
            if(!empty($listadeVentas)){
                $response->getBody()->write(json_encode([$listadeVentas]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
            else{
                $response->getBody()->write(json_encode(['error=> Registro de Ventas NO EXISTE']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
        }
        else{
            $listadeVentas = self::chequearProductosVendidos($fecha); // SOLO FUNCIONA SI LE DOY A UN DIA POR EJEMPLO 30 y no existe me muetra las ventas del 29
            if(empty($listadeVentas)){
                $response->getBody()->write(json_encode(['error=> Registro de Ventas NO EXISTE']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            $response->getBody()->write(json_encode([$listadeVentas]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        }
    function ventasPorUsuario(Request $request, Response $response, array $args): Response {
        
            $usuario = $request->getQueryParams()['usuario'];

            $listadodeusuarios = self::chequearVentasPorUsuario($usuario);
            if(empty($listadodeusuarios)){
            $response->getBody()->write(json_encode(['ERROR => No se ha encontrado ventas a nombre del usuario ingresado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            else{
            $response->getBody()->write(json_encode([$listadodeusuarios]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
            
            
           
            }
    function ventasPorProducto(Request $request, Response $response, array $args): Response {
        
                $tipo = $request->getQueryParams()['tipo'];
    
                $listadodeproductos = self::chequearVentasPorProducto($tipo);
                if(empty($listadodeproductos)){
                $response->getBody()->write(json_encode(['ERROR => No se ha encontrado ventas del tipo ingresado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                }
                else{
                $response->getBody()->write(json_encode([$listadodeproductos]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                }
                
                
               
                }
    function productosEntreValores(Request $request, Response $response, array $args): Response {
        
                    $precio_minimo = $request->getQueryParams()['precio_minimo'];
                    $precio_maximo = $request->getQueryParams()['precio_maximo'];
        
                    $listadodeproductos = self::chequearProductosEntreValores($precio_minimo,$precio_maximo);
                    if(empty($listadodeproductos)){
                    $response->getBody()->write(json_encode(['ERROR => No se ha encontrado productos en el rango de precio ingresado']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                    }
                    else{
                    $response->getBody()->write(json_encode([$listadodeproductos]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                    }
                    
                    
                   
                    }
    function ventasIngresos(Request $request, Response $response, array $args): Response {
        
        $fecha = $request->getQueryParams()['fecha'];
                        
            
                        $ganancia = self::chequearVentasIngresos($fecha);
                        $response->getBody()->write(json_encode(['La ganancia total es de: '. $ganancia . " pesos"]));
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                       
                        }
    function productoMasVendido(Request $request, Response $response, array $args): Response {
            
        $arraycontenedordeStocks = self::chequearProductoMasVendido();
        $productomasVendido = self::buscarProductoMasStock($arraycontenedordeStocks);
        $response->getBody()->write(json_encode([$productomasVendido]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                                           
    }
    function ventasModificar(Request $request, Response $response, array $args): Response {
        $numero_pedido = $request->getParsedBody()['numero_pedido'];
        $mail = $request->getParsedBody()['mail'];
        $marca = $request->getParsedBody()['marca'];
        $tipo = $request->getParsedBody()['tipo'];
        $modelo = $request->getParsedBody()['modelo'];
        $cantidad = $request->getParsedBody()['cantidad'];



        $resultado=self::updetearVentas($numero_pedido,$mail,$marca,$tipo,$modelo,$cantidad);
        if($resultado == 'Actualizado'){
            self::updetearStockporModificaciondeVenta($marca,$tipo,$modelo,$cantidad,$numero_pedido);
            $response->getBody()->write(json_encode([$resultado]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        else{
            $response->getBody()->write(json_encode([$resultado]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
          

    }
    function descargarVentasCSV(Request $request, Response $response, array $args): Response {
        $listadeventas = self::select();
        $ventasArray = json_decode(json_encode($listadeventas), true);
    
        
        $response = $response->withHeader('Content-Type', 'text/csv')
                             ->withHeader('Content-Disposition', 'attachment; filename="ventas.csv"')
                             ->withStatus(200); // OK
    
        
        $output = fopen('php://output', 'w');//escribe el CSV en la repsueta directamente
    
        if (!empty($ventasArray)) {
            $columnas = array_keys($ventasArray[0]);//me extrae las keys de cada array asociativo para que no tenga que hacelro manual y pueda generalizar
            fputcsv($output, $columnas);
        }
    
        foreach ($ventasArray as $valores) {
            fputcsv($output, $valores);
        }
    
        fclose($output);
        
        return $response;
    }

    public static function select(){
        $conn= DB::Connect();

        $select = "SELECT * FROM ventas";
        $stmt= $conn->prepare($select);
        
        try{
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return($productos);
        }
        catch (Exception $e){
            echo "Error al traer ventas" . $e->getMessage(); 

        }
    }
    public function guardarventaImagen($marca,$tipo,$modelo,$mail,$fecha_imagen){
        $directorioCreado=false;
        if(!file_exists("ImagenesDeVenta/2024/")){
            mkdir("ImagenesDeVenta/2024/", 0777, true);
            $directorioCreado=true;
        }
        $fecha = str_replace('/','_',$fecha_imagen);
        $usuario = explode('@', $mail);
        $usuario=$usuario[0];

        if(isset($_FILES['imagen'])) {//si hay una imagen que se extrae de FILES que no sea NULL
            $nombreArchivo = $_FILES['imagen']['name'];//extrae el nombre de mi archivo
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION); 
            $nuevoNombre = $marca . "_" . $tipo. "_". $modelo . "_" . $usuario . "_" . $fecha . "." . $extension;
            $rutaArchivo = "ImagenesDeVenta/2024/" . $nuevoNombre;
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
    public static function chequearExistencia($marca,$tipo,$modelo,$color){
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM tienda WHERE producto_marca = :marca AND producto_tipo = :tipo AND producto_color = :color AND producto_modelo = :modelo");
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':modelo', $modelo);

        $stmt->execute();
        $vsExistentes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return($vsExistentes);
    }
    public static function updetearStock($marca,$tipo,$modelo,$color,$stock){
        $conn = DB::Connect();
        try{$stmt = $conn->prepare("UPDATE tienda SET producto_stock = producto_stock - :stock_a_restar
                                WHERE producto_tipo = :tipo AND producto_marca = :marca AND producto_modelo = :modelo AND producto_color = :color");
        
        $stmt->bindParam(':stock_a_restar', $stock);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':color', $color);
        $stmt->execute();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
}
    public static function updetearStockporModificaciondeVenta($marca,$tipo,$modelo,$stock){
        
        $conn=DB::Connect();
        try{$stmt = $conn->prepare("UPDATE tienda 
                                    SET producto_stock = producto_stock - :stock_a_restar
                                    WHERE producto_tipo = :tipo AND producto_marca = :marca AND producto_modelo = :modelo ");
        
        $stmt->bindParam(':stock_a_restar', $stock);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':modelo', $modelo);
        
        $stmt->execute();
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public static function buscarProductoMasStock($arraycontenedordeStocks) {
        $maxStock = 0;
        $productoMasStock = null;
    
        foreach ($arraycontenedordeStocks as $producto) {
            if ($producto['total_stock'] > $maxStock) {
                $maxStock = $producto['total_stock'];
                $productoMasStock = $producto;
            }
        }
    
        return $productoMasStock;
    }
    public static function updetearVentas($numero_pedido,$mail,$marca,$tipo,$modelo,$cantidad){
        $conn = DB::Connect();
        $stmt1 = $conn->prepare("SELECT * FROM tienda WHERE producto_marca= :producto_marca AND producto_tipo = :producto_tipo AND producto_modelo = :producto_modelo AND producto_stock - :producto_stock > 0  ");
        $stmt1->bindParam(':producto_stock', $cantidad);
        $stmt1->bindParam(':producto_tipo', $tipo);
        $stmt1->bindParam(':producto_marca', $marca);
        $stmt1->bindParam(':producto_modelo', $modelo);
        $stmt1->execute();
        $productosdisponiblesaModificar = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        

        if (!empty($productosdisponiblesaModificar)) {
            try {
                $conn = DB::Connect();
                $stmt = $conn->prepare("UPDATE ventas 
                                        SET v_mail = :v_mail, v_marca = :v_marca, v_tipo = :v_tipo, v_modelo = :v_modelo, v_stock = :v_stock
                                        WHERE v_pedido = :v_pedido");
        
                $stmt->bindParam(':v_pedido', $numero_pedido);
                $stmt->bindParam(':v_mail', $mail);
                $stmt->bindParam(':v_stock', $cantidad);
                $stmt->bindParam(':v_tipo', $tipo);
                $stmt->bindParam(':v_marca', $marca);
                $stmt->bindParam(':v_modelo', $modelo);
        
                $stmt->execute();
        
                if ($stmt->rowCount() == 0) {
                    return "No existe numero de pedido";
                } else {
                    return "Actualizado";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false;
            }
        }
        else{
            return 'Producto no disponible en catalogo o sin Stock suficiente'; 
        }
        
       
        
    }
    public static function chequearProductosVendidos($fecha){
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM ventas WHERE v_fecha = :fecha");
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $productosVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return($productosVendidos);
    }
    public static function chequearVentasIngresos($fecha){
     if (!empty($fecha)){
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM ventas WHERE v_fecha = :fecha");
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $productosVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ganancia=0;
        foreach($productosVendidos as $values){
            $stmt1 = $conn->prepare("SELECT producto_precio FROM tienda WHERE producto_marca = :marca AND producto_tipo = :tipo AND producto_modelo = :modelo AND producto_color = :color");
            $stmt1->bindParam(':marca', $values['v_marca']);
            $stmt1->bindParam(':tipo', $values['v_tipo']);
            $stmt1->bindParam(':modelo', $values['v_modelo']);
            $stmt1->bindParam(':color', $values['v_color']);
           
            $stmt1->execute();
            $precio = $stmt1->fetch(PDO::FETCH_ASSOC);
            var_dump($precio['producto_precio']);
            $ganancia+=$precio['producto_precio'];
        }
       return $ganancia;
     }
     else{
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM ventas");
        $stmt->execute();
        $productosVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ganancia=0;
        foreach($productosVendidos as $values){
            $stmt1 = $conn->prepare("SELECT producto_precio FROM tienda WHERE producto_marca = :marca AND producto_tipo = :tipo AND producto_modelo = :modelo AND producto_color = :color");
            $stmt1->bindParam(':marca', $values['v_marca']);
            $stmt1->bindParam(':tipo', $values['v_tipo']);
            $stmt1->bindParam(':modelo', $values['v_modelo']);
            $stmt1->bindParam(':color', $values['v_color']);
           
            $stmt1->execute();
            $precio = $stmt1->fetch(PDO::FETCH_ASSOC);
            
            $ganancia+=$precio['producto_precio']*$values['v_stock'];
        }
       return $ganancia;
     }       
    }
    public static function chequearProductoMasVendido(){
        
           $conn = DB::Connect();
           $stmt = $conn->prepare("SELECT DISTINCT v_marca, v_tipo, v_modelo FROM ventas ");
           $stmt->execute();
           $productosVendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
           
           $arraycontenedordeStocks=array();
            foreach($productosVendidos as $values){
               $stmt1 = $conn->prepare("SELECT SUM(v_stock) AS total_stock
                                        FROM ventas 
                                        WHERE v_marca = :marca 
                                        AND v_tipo = :tipo 
                                        AND v_modelo = :modelo 
                                        ");
               $stmt1->bindParam(':marca', $values['v_marca']);
               $stmt1->bindParam(':tipo', $values['v_tipo']);
               $stmt1->bindParam(':modelo', $values['v_modelo']);
               
            
               $stmt1->execute();
               $total= $stmt1->fetch(PDO::FETCH_ASSOC);
               $arraycontenedordeStocks[] = [
                'v_marca' => $values['v_marca'],
                'v_tipo' => $values['v_tipo'],
                'v_modelo' => $values['v_modelo'],
                'total_stock' => $total['total_stock']
            ];
        }
    
        return $arraycontenedordeStocks;
    }
    public static function chequearVentasPorUsuario($usuario){
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM ventas WHERE v_mail = :mail");
        $stmt->bindParam(':mail', $usuario);
        $stmt->execute();
        $ventasporusuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return($ventasporusuarios);
    }
    public static function chequearVentasPorProducto($tipo){
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM ventas WHERE v_tipo = :tipo");
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        $ventasporproductos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return($ventasporproductos);
    }
    public static function chequearProductosEntreValores($precio_minimo,$precio_maximo){
        $conn = DB::Connect();
        $stmt = $conn->prepare("SELECT * FROM tienda WHERE producto_precio > :precio_minimo AND producto_precio < :precio_maximo");
        $stmt->bindParam(':precio_minimo', $precio_minimo);
        $stmt->bindParam(':precio_maximo', $precio_maximo);
        $stmt->execute();
        $productosentreValores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return($productosentreValores);
    }
    public static function insertVenta($venta) {
        date_default_timezone_set("America/Argentina/Buenos_Aires"); 
        $v_fecha = date("d / M / o");
        $v_pedido = rand(10000, 50000);
    
        try {
            $conn = DB::Connect();
    
            $v_mail = $venta->getMail();
            $v_marca = $venta->getMarca();
            $v_tipo = $venta->getTipo();
            $v_modelo = $venta->getModelo();
            $v_color = $venta->getColor();
            $v_stock = $venta->getStock();
    
            $stmt = $conn->prepare("INSERT INTO ventas (v_mail, v_marca, v_tipo, v_modelo, v_color, v_stock, v_fecha, v_pedido) 
                                    VALUES (:v_mail, :v_marca, :v_tipo, :v_modelo, :v_color, :v_stock, :v_fecha, :v_pedido)");
    
            $stmt->bindParam(':v_mail', $v_mail); 
            $stmt->bindParam(':v_marca', $v_marca); 
            $stmt->bindParam(':v_tipo', $v_tipo); 
            $stmt->bindParam(':v_modelo', $v_modelo); 
            $stmt->bindParam(':v_color', $v_color); 
            $stmt->bindParam(':v_stock', $v_stock); 
            $stmt->bindParam(':v_fecha', $v_fecha); 
            $stmt->bindParam(':v_pedido', $v_pedido); 
            $stmt->execute();
            
            return true;
        }
        catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    
    



}


?>