<?php
require_once './models/Producto.php';

class ProductoController
{
    public function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $nombre = $params['nombre'];
        $precio = $params['precio'];
        $tipo = $params['tipo'];
        $talla = $params['talla'];
        $color = $params['color'];
        $stock = $params['stock'];
        $imagen = $_FILES['imagen'];

        $producto = Producto::obtenerProducto($nombre, $tipo, $color);
        if ($producto) {
            $nuevoStock = $producto->stock + $stock;
            Producto::modificarProducto($producto->id, $nombre, $precio, $tipo, $talla, $color, $nuevoStock);
        } else {
            $nuevoProducto = new Producto();
            $nuevoProducto->nombre = $nombre;
            $nuevoProducto->precio = $precio;
            $nuevoProducto->tipo = $tipo;
            $nuevoProducto->talla = $talla;
            $nuevoProducto->color = $color;
            $nuevoProducto->stock = $stock;
            $nuevoProducto->crearProducto();
            $producto = Producto::obtenerProducto($nombre, $tipo, $color); // Obtener el producto recién creado para actualizar su imagen
        }

        // Crear el directorio si no existe
        $directorio = './Fotos/2024/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // Guardar la imagen en la dirección especificada
        $nombreArchivo = $nombre . '_' . $tipo . '.jpg';
        $rutaDestino = $directorio . $nombreArchivo;

        if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            Producto::guardarImagen($producto->id, $rutaDestino);
            $payload = json_encode(array("mensaje" => "Producto cargado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error al guardar la imagen"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $productos = Producto::obtenerTodos();
        $payload = json_encode($productos);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $nombre = $args['nombre'];
        $tipo = $args['tipo'];
        $color = $args['color'];
        $producto = Producto::obtenerProducto($nombre, $tipo, $color);
        if ($producto) {
            $payload = json_encode($producto);
        } else {
            $payload = json_encode(array("mensaje" => "Producto no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

