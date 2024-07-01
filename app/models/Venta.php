<?php

class Venta
{
    public $id;
    public $email;
    public $nombre;
    public $tipo;
    public $talla;
    public $stock;
    public $fecha;
    public $numero_pedido;
    public $imagen;

    public function crearVenta()
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("INSERT INTO ventas (email, nombre, tipo, talla, stock, fecha, numero_pedido, imagen) VALUES (:email, :nombre, :tipo, :talla, :stock, :fecha, :numero_pedido, :imagen)");
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':talla', $this->talla, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $this->stock, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':numero_pedido', $this->numero_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':imagen', $this->imagen, PDO::PARAM_STR);
        $consulta->execute();

        return $objetoAccesoDato->obtenerUltimoId();
    }

    public static function obtenerVentasPorUsuario($email)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM ventas WHERE email = :email");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerVentasPorProducto($nombre, $tipo)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM ventas WHERE nombre = :nombre AND tipo = :tipo");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerVentaPorNumeroPedido($numero_pedido)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM ventas WHERE numero_pedido = :numero_pedido");
        $consulta->bindValue(':numero_pedido', $numero_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerVentasPorFecha($fecha)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM ventas WHERE fecha = :fecha");
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerIngresosPorDia($fecha = null)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        if ($fecha) {
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT SUM(stock * (SELECT precio FROM tienda WHERE tienda.nombre = ventas.nombre AND tienda.tipo = ventas.tipo)) as ingresos FROM ventas WHERE fecha = :fecha");
            $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        } else {
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT fecha, SUM(stock * (SELECT precio FROM tienda WHERE tienda.nombre = ventas.nombre AND tienda.tipo = ventas.tipo)) as ingresos FROM ventas GROUP BY fecha");
        }
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerProductoMasVendido()
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT nombre, tipo, SUM(stock) as total_vendido FROM ventas GROUP BY nombre, tipo ORDER BY total_vendido DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function modificarVenta($numero_pedido, $email, $nombre, $tipo, $talla, $stock)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("UPDATE ventas SET email = :email, nombre = :nombre, tipo = :tipo, talla = :talla, stock = :stock WHERE numero_pedido = :numero_pedido");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->bindValue(':talla', $talla, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $stock, PDO::PARAM_INT);
        $consulta->bindValue(':numero_pedido', $numero_pedido, PDO::PARAM_INT);
        $consulta->execute();
    }
}
