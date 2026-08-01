<?php
/**
 * Ticket de Venta
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?= $venta['id_venta'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 10px;
            width: 280px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        .info {
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .products {
            margin-bottom: 15px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
        }
        .product-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .product-name {
            flex: 1;
        }
        .product-qty {
            width: 40px;
            text-align: center;
        }
        .product-price {
            width: 60px;
            text-align: right;
        }
        .totals {
            margin-bottom: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
        @media print {
            body {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BODEGA ARMANDO TORNERO</h1>
        <p>RUC: 20123456789</p>
        <p>Av. Principal 123</p>
        <p>Tel: 955212016</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span>Ticket #:</span>
            <span><?= str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT) ?></span>
        </div>
        <div class="info-row">
            <span>Fecha:</span>
            <span><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></span>
        </div>
        <div class="info-row">
            <span>Cliente:</span>
            <span><?= htmlspecialchars($venta['nombre_cliente'] ?? 'Consumidor Final') ?></span>
        </div>
        <div class="info-row">
            <span>Vendedor:</span>
            <span><?= htmlspecialchars($venta['nombres']) ?></span>
        </div>
    </div>

    <div class="products">
        <?php foreach ($detalles as $detalle): ?>
        <div class="product-row">
            <span class="product-name"><?= htmlspecialchars($detalle['nombre_producto']) ?></span>
            <span class="product-qty"><?= $detalle['cantidad'] ?></span>
            <span class="product-price"><?= number_format($detalle['subtotal'], 2) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>S/ <?= number_format($venta['subtotal'], 2) ?></span>
        </div>
        <div class="total-row">
            <span>IGV (18%):</span>
            <span>S/ <?= number_format($venta['igv'], 2) ?></span>
        </div>
        <?php if ($venta['descuento'] > 0): ?>
        <div class="total-row">
            <span>Descuento:</span>
            <span>- S/ <?= number_format($venta['descuento'], 2) ?></span>
        </div>
        <?php endif; ?>
        <div class="total-row final">
            <span>TOTAL:</span>
            <span>S/ <?= number_format($venta['total'], 2) ?></span>
        </div>
        <div class="total-row">
            <span>Método Pago:</span>
            <span><?= ucfirst($venta['metodo_pago']) ?></span>
        </div>
    </div>

    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p><?= date('d/m/Y H:i') ?></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
