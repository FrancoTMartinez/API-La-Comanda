<?php

    class Estado
    {
        const ESPERANDO = 'Con cliente esperando pedido';
        const COMIENDO = 'Con cliente comiendo';
        const PAGANDO = 'Con cliente pagando';
        const CERRADA = 'cerrada';

        //Estados pedidos
        const PENDIENTE = 'pendiente';
        const PREPARACION = 'en preparacion';
        const LISTO = 'listo para servir';
        const ENTREGADO = 'entregado';
    }
?>

