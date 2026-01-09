# GlosJurBil
Glosario Jurídico Bilingüe con PHP 

##  Descripción
Plataforma web dinámica diseñada para la gestión y consulta de terminología jurídica bilingüe. El sistema permite a profesionales del derecho y traductores administrar un repositorio centralizado de términos, facilitando la búsqueda precisa y la categorización técnica de conceptos legales complejos.

##  Características Principales
Buscador Inteligente: Motor de búsqueda con filtros avanzados por categoría y orden alfabético.

Gestión de Diccionario (CRUD): Interfaz administrativa para crear, leer, actualizar y eliminar términos jurídicos.

Interfaz Adaptativa: Diseño limpio y funcional enfocado en la lectura y eficiencia de usuario.

## Gestión de Roles y Permisos 
El sistema implementa un control de acceso basado en roles para garantizar la integridad de la información y un flujo de trabajo colaborativo:

* **Administrador:** Control total del ecosistema, gestión de cuentas de usuario y auditoría de cambios.
* **Docente:** Moderador técnico con facultades para registrar, modificar y **validar/aprobar** términos propuestos por la comunidad estudiantil.
* **Estudiante:** Colaborador activo con capacidad de **proponer nuevos términos**, sujetos a revisión por expertos.
* **Público General:** Acceso de consulta y herramientas de exportación de datos.

## Funcionalidades Avanzadas
* **Workflow de Aprobación:** Implementación de estados para los términos (Pendiente, Aprobado, Rechazado), asegurando la calidad del contenido bilingüe.
* **Generador de Reportes Personalizados:** Módulo de exportación que permite al usuario seleccionar términos específicos y generar documentos descargables (PDF) para estudio fuera de línea.
* **Auditoría de Cambios:** Registro de historial para que los administradores supervisen las validaciones realizadas por los docentes.

 ## Tecnologías Utilizadas
Servidor Local: XAMPP v3.2.2

Backend: PHP 5.6.32

Base de Datos: MySQL / MariaDB (Importar desde /db/gls_jur_bil.sql)

Frontend: HTML5, CSS3, Bootstrap 5.3.3

Reportes: dompdf

## Roles y Credenciales de Prueba
El sistema implementa un control de acceso basado en roles
| Nombre | Correo | Contraseña | Rol |
| :--- | :--- | :--- | :--- |
| **admin** | admin@gmail.com | ad123 | Administrador |
| **docente** | docente@gmail.com | doc123 | Docente |
| **estudiante** | estudiante@gmail.com | est123 | Estudiante |

## Instalación y Configuración

* Clonar el repositorio: Copia el proyecto dentro de tu carpeta htdocs de XAMPP:

* git clone https://github.com/GiselM0on/GlosJurBil.git
* **Configurar la Base de Datos:**

* 1. Abre el panel de control de XAMPP e inicia Apache y MySQL.

* 2. Ve a http://localhost/phpmyadmin/.

* 3. Crea una nueva base de datos llamada gls_jur_bil.

* 4. Haz clic en la pestaña Importar y selecciona el archivo que está en: /db/gls_jur_bil.sql.

* **Configurar la conexión en PHP:**

* Localiza tu archivo de conexión ( conexion.php).

* **Asegúrate de que los parámetros coincidan con tu XAMPP:**

PHP

$host = "localhost";
$user = "root";
$password = ""; // Por defecto vacío en XAMPP
$database = "gls_jur_bil";
Acceso al sistema: Abre tu navegador y escribe: http://localhost/GlosJurBil

¡Listo! Ahora puedes correr tus pruebas en el entorno configurado.


