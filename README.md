# Sistema de Gestión Hospitalaria - 8nsb
# DALE CTRL SHIFT V PARA DARLE FORMATO AL README 
Este proyecto es un sistema integral de gestión hospitalaria diseñado para administrar las operaciones clínicas y administrativas de un centro de salud. Permite el control detallado de pacientes, personal médico, infraestructura hospitalaria, servicios clínicos, ingresos, egresos, consultas, estudios y procedimientos médicos.

---

## Características Principales

- **Gestión de Pacientes:** Control de ingresos, egresos y consultas médicas.
- **Cuerpo Médico:** Administración de expedientes de médicos y sus especialidades.
- **Infraestructura:** Gestión de áreas, departamentos, consultorios, habitaciones, quirófanos y laboratorios.
- **Servicios Clínicos:** Control de tipos de servicios, estudios, procedimientos quirúrgicos y laboratorios.
- **Seguridad:** Sistema de autenticación con roles de usuario como Administrador, Recepción, Médico, Laboratorio y Quirófano.

---

## Tecnologías Utilizadas

- **Backend:** PHP API RESTful.
- **Frontend:** HTML5, CSS3 y JavaScript.
- **Base de Datos:** MySQL / MariaDB.
- **Servidor Local Recomendado:** XAMPP.

---

## Requisitos Previos

Antes de ejecutar el proyecto, asegúrate de contar con:

1. **XAMPP** instalado.
2. **PHP 7.4 o superior**.
3. **MySQL / MariaDB** activo.
4. Un navegador web.
5. El proyecto ubicado dentro de la carpeta `htdocs`.

---

## Instalación y Configuración

### 1. Clonar o copiar el proyecto

Coloca la carpeta del proyecto dentro del directorio `htdocs` de XAMPP.

Ejemplo de ruta:

```txt
C:\xampp\htdocs\8nsb-Proyectos-De-Software
```

---

### 2. Iniciar XAMPP

Abre el Panel de Control de XAMPP e inicia los módulos:

```txt
Apache
MySQL
FileZilla
```

---

### 3. Configurar la base de datos

1. Abre **phpMyAdmin** desde tu navegador.
2. Crea una base de datos llamada:

```sql
hospital_db
```

3. Importa el archivo principal:

```txt
hospital_db.sql
```

Este archivo contiene:

- Estructura de la base de datos.
- Creación de tablas.
- Llaves primarias.
- Llaves foráneas.
- Roles iniciales.
- Usuario administrador inicial.

> **Nota importante:**  
> Si el archivo SQL está comentado, selecciona todo el contenido con `Ctrl + A` y descoméntalo con `Ctrl + K + U`.  
> Para volverlo a comentar puedes usar `Ctrl + K + C`.

---

### 4. Poblar la base de datos

Después de importar `hospital_db.sql`, importa el archivo:

```txt
poblar_db.sql
```

Este script agrega datos de prueba para poder utilizar el sistema con información inicial.

El archivo `poblar_db.sql` inserta datos como:

- Hospitales.
- Áreas.
- Departamentos.
- Consultorios.
- Habitaciones.
- Laboratorios.
- Quirófanos.
- Especialidades.
- Médicos.
- Usuarios.
- Roles.
- Tipos de servicios.
- Consultas.
- Estudios.
- Ingresos.
- Egresos.
- Procedimientos quirúrgicos.

---

## Verificar la Conexión a la Base de Datos

Antes de ejecutar el sistema, revisa el archivo:

```txt
config/database.php
```

Verifica que el nombre de la base de datos coincida con el nombre que creaste en phpMyAdmin.

La configuración debe quedar similar a esta:

```php
private string $host = "localhost";
private string $db_name = "hospital_db";
private string $username = "root";
private string $password = "";
```

Si tu base de datos tiene otro nombre, cambia el valor de:

```php
private string $db_name = "hospital_db";
```

Por el nombre correcto de tu base de datos.

---

## Usuario Administrador

El archivo `hospital_db.sql` ya inserta automáticamente el usuario administrador inicial, por lo que no es necesario ejecutar un script adicional para crearlo.

### Credenciales de acceso

```txt
Usuario: admin
Contraseña: 123456
```

Con este usuario se puede ingresar al sistema con permisos de administrador.

---

## Cómo Correr el Proyecto

### Opción 1: Usando el acceso rápido en Windows

Si estás en Windows y usas XAMPP con las rutas por defecto, puedes ejecutar en la terminal:

```bash
./run.bat
```

---

### Opción 2: Manualmente

1. Abre el Panel de Control de XAMPP.
2. Inicia los módulos de **Apache** y **MySQL**.
3. Abre tu navegador.
4. Ingresa a la siguiente ruta:

```txt
http://localhost/8nsb-Proyectos-De-Software/frontend/login.html
```

5. Inicia sesión con las credenciales:

```txt
Usuario: admin
Contraseña: 123456
```

---

## Estructura del Proyecto

```txt
8nsb-Proyectos-De-Software/
│
├── api/
│   └── Lógica del servidor organizada por módulos CRUD.
│
├── config/
│   └── Configuración de la conexión a la base de datos.
│
├── frontend/
│   └── Interfaz de usuario en HTML, CSS y JavaScript.
│
├── helpers/
│   └── Utilidades para autenticación, sesiones y respuestas JSON.
│
├── hospital_db.sql
│   └── Script principal de estructura de la base de datos e inserción del usuario administrador.
│
├── poblar_db.sql
│   └── Script para insertar datos de prueba en la base de datos.
│
└── run.bat
    └── Archivo para ejecutar el proyecto rápidamente en Windows.
```

---

## Archivos SQL Importantes

### `hospital_db.sql`

Este archivo crea la estructura principal de la base de datos `hospital_db`.

Incluye:

- Creación de tablas.
- Llaves primarias.
- Llaves foráneas.
- Roles iniciales.
- Usuario administrador inicial.

También incluye el acceso inicial:

```txt
Usuario: admin
Contraseña: 123456
```

---

### `poblar_db.sql`

Este archivo se utiliza después de importar `hospital_db.sql`.

Sirve para llenar la base de datos con información de prueba realista para poder navegar y probar el sistema.

Incluye datos para:

- Hospitales.
- Áreas.
- Departamentos.
- Consultorios.
- Habitaciones.
- Laboratorios.
- Quirófanos.
- Especialidades.
- Médicos.
- Usuarios.
- Roles.
- Tipos de servicios.
- Consultas.
- Estudios.
- Ingresos.
- Egresos.
- Procedimientos quirúrgicos.

---

## Notas Importantes

- La base de datos debe llamarse `hospital_db`, a menos que también se modifique el nombre dentro de `config/database.php`.
- Si cambias el nombre de la base de datos en phpMyAdmin, también debes cambiarlo en el archivo `config/database.php`.
- Primero se debe importar `hospital_db.sql`.
- Después se debe importar `poblar_db.sql`.
- No es necesario ejecutar `crear_admin.php`, porque el administrador ya viene insertado en el script principal de la base de datos.
- Para iniciar sesión, usa el usuario `admin` y la contraseña `123456`.

---

## Acceso al Sistema

URL local del sistema:

```txt
http://localhost/8nsb-Proyectos-De-Software/frontend/login.html
```

Credenciales iniciales:

```txt
Usuario: admin
Contraseña: 123456
```

---

## Descripción General

El **Sistema de Gestión Hospitalaria - 8nsb** permite administrar de manera centralizada la información operativa y clínica de un hospital.

Su objetivo es facilitar el control de usuarios, médicos, áreas hospitalarias, servicios clínicos, consultas, ingresos, egresos, estudios y procedimientos quirúrgicos mediante una aplicación web conectada a una base de datos MySQL.

---

## Nombre del Sistema

El sistema también puede identificarse como **Hospital HIS**.

Las siglas **HIS** significan:

```txt
Hospital Information System
```

En español:

```txt
Sistema de Información Hospitalaria
```

Esto hace referencia a un sistema encargado de centralizar, organizar y gestionar información hospitalaria, como pacientes, médicos, consultas, ingresos, egresos, servicios y procedimientos.