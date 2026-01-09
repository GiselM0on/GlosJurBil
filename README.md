# GlosJurBil
**Glosario Jurídico Bilingüe con PHP**

## Descripción
Plataforma web dinámica diseñada para la gestión y consulta de terminología jurídica bilingüe. El sistema permite a profesionales del derecho y traductores administrar un repositorio centralizado de términos, facilitando la búsqueda precisa y la categorización técnica de conceptos legales complejos.

---

##  Características Principales
* **Buscador Inteligente:** Motor de búsqueda con filtros avanzados por categoría y orden alfabético.
* **Gestión de Diccionario (CRUD):** Interfaz administrativa para crear, leer, actualizar y eliminar términos.
* **Interfaz Adaptativa:** Diseño limpio y funcional enfocado en la lectura técnica.
* **Workflow de Aprobación:** Implementación de estados (Pendiente, Aprobado, Rechazado) para asegurar la calidad.
* **Generador de Reportes:** Módulo de exportación a PDF mediante **dompdf**.
* **Auditoría de Cambios:** Registro de historial para supervisión de validaciones.

---

##  Tecnologías Utilizadas
* **Servidor Local:** XAMPP v3.2.2
* **Backend:** PHP 5.6.32
* **Base de Datos:** MySQL / MariaDB (Importar desde `/db/gls_jur_bil.sql`)
* **Frontend:** HTML5, CSS3, **Bootstrap 5.3.3**
* **Reportes:** dompdf

---

##  Roles y Credenciales de Prueba
El sistema implementa un control de acceso basado en roles para garantizar la integridad de la información:

| Nombre | Correo | Contraseña | Rol |
| :--- | :--- | :--- | :--- |
| **admin** | admin@gmail.com | ad123 | Administrador |
| **docente** | docente@gmail.com | doc123 | Docente |
| **estudiante** | estudiante@gmail.com | est123 | Estudiante |

---

##  Instalación y Configuración

### 1. Clonar el repositorio
Copia el proyecto dentro de tu carpeta `htdocs` de XAMPP:
```bash
git clone [https://github.com/GiselM0on/GlosJurBil.git](https://github.com/GiselM0on/GlosJurBil.git)
--- 
### 2. Configurar la Base de Datos
Abre el panel de control de XAMPP e inicia Apache y MySQL.

Ve a http://localhost/phpmyadmin/.

Crea una nueva base de datos llamada gls_jur_bil.

Haz clic en la pestaña Importar y selecciona el archivo localizado en: /db/gls_jur_bil.sql.
---
### 3. Configurar la conexión en PHP
Localiza tu archivo de conexión (conexion.php) y asegúrate de que los parámetros coincidan con tu entorno local:

PHP

<?php
$host = "localhost";
$user = "root";
$password = ""; // Por defecto vacío en XAMPP
$database = "gls_jur_bil";

$conexion = mysqli_connect($host, $user, $password, $database);
?>

---
### 4. Acceso al sistema
Abre tu navegador y escribe: http://localhost/GlosJurBil
