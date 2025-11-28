<?php
session_start();

// Conexión a la base de datos - RUTA CORRECTA
include "../conexion.php";

// Verificar si la conexión se estableció correctamente
if (!isset($cn) || $cn->connect_error) {
    die("Error de conexión a la base de datos: " . $cn->connect_error);
}

// Consulta a la base de datos
$sql = "SELECT id_termino, palabra FROM termino ORDER BY palabra";
$result = $cn->query($sql);

// Verificar errores de consulta
if (!$result) {
    die("Error en la consulta: " . $cn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Términos</title>

    <style>
        :root {
            --color-amarillo: #fff06dff;
            --color-azul-oscuro: #006694;
            --color-gris: #636466;
            --color-gris-claro: #f1f2f2;
            --color-naranja: #ff9a15;
            --color-azul-claro: #27a5df;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--color-gris-claro);
            display: flex;
            justify-content: center;
            padding: 40px;
            margin: 0;
        }

        .card {
            width: 600px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .titulo {
            font-size: 26px;
            font-weight: bold;
            color: var(--color-azul-oscuro);
            text-align: center;
            margin-bottom: 20px;
        }

        #buscador {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--color-azul-claro);
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .lista {
            max-height: 350px;
            overflow-y: auto;
            padding-right: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
        }

        .item {
            display: flex;
            align-items: center;
            padding: 10px;
            font-size: 17px;
            color: var(--color-gris);
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        .item:hover {
            background-color: #f9f9f9;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item input {
            margin-right: 12px;
            transform: scale(1.2);
        }

        .acciones {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-seleccionar {
            background: var(--color-amarillo);
            color: var(--color-gris);
        }
        .btn-seleccionar:hover {
            background: var(--color-naranja);
            color: white;
        }

        .btn-pdf {
            background: var(--color-azul-oscuro);
            color: white;
        }
        .btn-pdf:hover {
            background: var(--color-azul-claro);
        }

        .contador {
            text-align: center;
            margin: 10px 0;
            color: var(--color-gris);
            font-style: italic;
        }

        .sin-terminos {
            text-align: center;
            color: var(--color-gris);
            padding: 20px;
            font-style: italic;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="titulo">Selecciona los Términos</div>

    <input type="text" id="buscador" placeholder="Buscar término...">

    <div class="contador" id="contador">
        <?php
        if ($result->num_rows > 0) {
            echo "Total de términos: " . $result->num_rows;
        } else {
            echo "No hay términos disponibles";
        }
        ?>
    </div>

    <form action="reporte.php" method="POST">

        <div class="lista" id="listaTerminos">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="item">
                        <input type="checkbox" name="terminos[]" value="'.$row['id_termino'].'" id="term_'.$row['id_termino'].'">
                        <label for="term_'.$row['id_termino'].'">'.htmlspecialchars($row['palabra']).'</label>
                    </div>';
                }
            } else {
                echo '<div class="sin-terminos">No hay términos en la base de datos.</div>';
            }
            ?>
        </div>

        <div class="acciones">
            <button type="button" class="btn btn-seleccionar" onclick="seleccionarTodo()">Seleccionar todo</button>
            <button type="submit" class="btn btn-pdf" <?php echo ($result->num_rows == 0) ? 'disabled' : ''; ?>>Generar PDF</button>
        </div>

    </form>
</div>

<script>
    // 🔍 FILTRAR EN TIEMPO REAL
    document.getElementById("buscador").addEventListener("keyup", function() {
        let filtro = this.value.toLowerCase();
        let items = document.querySelectorAll(".item");
        let visibleCount = 0;

        items.forEach(item => {
            let texto = item.textContent.toLowerCase();
            if (texto.includes(filtro)) {
                item.style.display = "";
                visibleCount++;
            } else {
                item.style.display = "none";
            }
        });

        // Actualizar contador
        document.getElementById("contador").textContent = 
            visibleCount + " términos encontrados" + 
            (filtro ? " para: '" + filtro + "'" : "");
    });

    // ✔ SELECCIONAR/DESELECCIONAR TODO
    function seleccionarTodo() {
        let checks = document.querySelectorAll("input[type='checkbox']");
        let todosMarcados = Array.from(checks).every(c => c.checked);
        
        checks.forEach(c => {
            if (c.parentElement.style.display !== "none") {
                c.checked = !todosMarcados;
            }
        });

        // Actualizar texto del botón
        let btn = document.querySelector(".btn-seleccionar");
        btn.textContent = todosMarcados ? "Seleccionar todo" : "Deseleccionar todo";
    }

    // Contador de seleccionados
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                let selectedCount = document.querySelectorAll('input[type="checkbox"]:checked').length;
                let btn = document.querySelector(".btn-seleccionar");
                
                if (selectedCount > 0) {
                    btn.textContent = "Deseleccionar todo (" + selectedCount + " seleccionados)";
                } else {
                    btn.textContent = "Seleccionar todo";
                }
            });
        });
    });
</script>

</body>
</html>
<?php
// Cerrar conexión
if (isset($cn)) {
    $cn->close();
}
?>