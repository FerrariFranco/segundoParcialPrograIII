<?php

require_once './models/Venta.php';
require_once './models/Producto.php';

class VentaController
{
    public function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $email = $params['email'];
        $nombre = $params['nombre'];
        $tipo = $params['tipo'];
        $color = $params['color'];
        $talla = $params['talla'];
        $stock = $params['stock'];
        $fecha = date('Y-m-d');
        $numero_pedido = rand(1000, 9999); // Generar un número de pedido aleatorio
        $imagen = $params['imagen'];

        $producto = Producto::obtenerProducto($nombre, $tipo, $color);

        if ($producto && $producto->stock >= $stock) {
            // Crear una nueva venta
            $nuevaVenta = new Venta();
            $nuevaVenta->email = $email;
            $nuevaVenta->nombre = $nombre;
            $nuevaVenta->tipo = $tipo;
            $nuevaVenta->talla = $talla;
            $nuevaVenta->stock = $stock;
            $nuevaVenta->fecha = $fecha;
            $nuevaVenta->numero_pedido = $numero_pedido;
            $nuevaVenta->imagen = $_FILES["imagen"];
            $nuevaVenta->crearVenta();

            $nuevoStock = $producto->stock - $stock;
            Producto::modificarProducto($producto->id, $producto->nombre, $producto->precio, $producto->tipo, $producto->talla, $producto->color, $nuevoStock);

            $payload = json_encode(array("mensaje" => "Venta registrada con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "Stock insuficiente o producto no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarVentasPorUsuario($request, $response, $args)
    {
        $email = $args['email'];
        $ventas = Venta::obtenerVentasPorUsuario($email);
        $payload = json_encode($ventas);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarVentasPorProducto($request, $response, $args)
    {
        $nombre = $args['nombre'];
        $tipo = $args['tipo'];
        $ventas = Venta::obtenerVentasPorProducto($nombre, $tipo);
        $payload = json_encode($ventas);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarVentasPorFecha($request, $response, $args)
    {
        $fecha = $args['fecha'] ?? date('Y-m-d', strtotime('-1 day'));
        $ventas = Venta::obtenerVentasPorFecha($fecha);
        $payload = json_encode($ventas);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarIngresosPorDia($request, $response, $args)
    {
        $fecha = $args['fecha'] ?? null;
        $ingresos = Venta::obtenerIngresosPorDia($fecha);
        $payload = json_encode($ingresos);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarProductoMasVendido($request, $response, $args)
    {
        $producto = Venta::obtenerProductoMasVendido();
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarVenta($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $numero_pedido = $params['numero_pedido'];
        $email = $params['email'];
        $nombre = $params['nombre'];
        $tipo = $params['tipo'];
        $talla = $params['talla'];
        $stock = $params['stock'];

        $venta = Venta::obtenerVentaPorNumeroPedido($numero_pedido);

        if ($venta) {
            Venta::modificarVenta($numero_pedido, $email, $nombre, $tipo, $talla, $stock);
            $payload = json_encode(array("mensaje" => "Venta modificada con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "Venta no encontrada"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
