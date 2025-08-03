# API Gateway: MiraMar 🚪

Este servicio actúa como un **API Gateway** y es el único punto de entrada (Single Point of Entry) para todo el sistema de la agencia MiraMar. Su función principal es actuar como un **proxy inverso**, recibiendo las peticiones de los clientes y redirigiéndolas al microservicio interno correspondiente (`miramar-productos` o `miramar-ventas-clientes`).

---

## Diagrama de Arquitectura

![Diagrama de Flujo](/img/DiagramaFlujo.jpg "Diagrama de Flujo")

---

## Tecnologías Utilizadas ⚙️

* **Framework**: Lumen (PHP)
* **Cliente HTTP**: Guzzle para el reenvío de peticiones.
* **Gestor de Dependencias**: Composer

---

## Instalación

1.  **Clonar el repositorio**
    ```bash
    git clone https://github.com/Tate-147/miramar-api-gateway.git
    cd miramar-api-gateway
    ```

2.  **Instalar dependencias**
    ```bash
    composer install
    ```
    *(No se requiere configuración de base de datos ni migraciones para este servicio).*

---

## Ejecución 🚀

Para que el Gateway funcione, los microservicios de backend **deben estar corriendo** en sus respectivos puertos.

Inicia los 3 servicios, cada uno en una terminal separada:

1.  **Servicio de Productos:**
    ```bash
    # En la carpeta miramar-productos
    php -S localhost:8001 -t public
    ```
2.  **Servicio de Ventas y Clientes:**
    ```bash
    # En la carpeta miramar-ventas-clientes
    php -S localhost:8002 -t public
    ```
3.  **API Gateway:**
    ```bash
    # En la carpeta miramar-api-gateway
    php -S localhost:8000 -t public
    ```

---

## Reglas de Enrutamiento

El Gateway redirige las peticiones de la siguiente manera:

| Petición Entrante al Gateway | Microservicio de Destino |
| ---------------------------- | ------------------------ |
| `http://localhost:8000/servicios[/**]` | `http://localhost:8001/servicios[/**]` |
| `http://localhost:8000/paquetes[/**]` | `http://localhost:8001/paquetes[/**]` |
| `http://localhost:8000/clientes[/**]` | `http://localhost:8002/clientes[/**]` |
| `http://localhost:8000/ventas[/**]`   | `http://localhost:8002/ventas[/**]`   |

---

## Uso de la API

A partir de ahora, todas las interacciones con el sistema se deben realizar a través del Gateway en el puerto `8000`.

#### **Ejemplo 1: Obtener todos los servicios**
* **Petición:** `GET http://localhost:8000/servicios`
* **Flujo:** Gateway (`:8000`) recibe la petición y la reenvía a `miramar-productos` (`:8001`).

#### **Ejemplo 2: Registrar una venta**
* **Petición:** `POST http://localhost:8000/ventas`
* **Flujo:** Gateway (`:8000`) recibe la petición y la reenvía a `miramar-ventas-clientes` (`:8002`), el cual a su vez contactará a `miramar-productos` (`:8001`) para obtener los costos.

---

## Docker 🐋

Se incluye el Dockerfile (archivo con las instrucciones para construir la imagen) y el docker-compose.yml (archivo orquestador, define que contenedores se ejecutan, como se conectan entre sí y que recursos necesitan)

## Ejecución con Docker y Docker Compose 🐳

El proyecto está completamente contenerizado, lo que permite levantar toda la arquitectura de microservicios con un único comando.

### Prerrequisitos
* Tener **Docker** instalado.
* Tener **Docker Compose** instalado.

### Estructura de Carpetas
Para que `docker-compose.yml` funcione correctamente, se debe respetar la siguiente estructura de carpetas, donde los tres proyectos se encuentran al mismo nivel:

```
proyecto-miramar/
├── miramar-api-gateway/
│   ├── docker-compose.yml
│   └── Dockerfile
│   └── ... (otros archivos del gateway)
│
├── miramar-productos/
│   └── Dockerfile
│   └── ... (archivos del servicio de productos)
│
└── miramar-ventas-clientes/
    └── Dockerfile
    └── ... (archivos del servicio de ventas)
```

### Archivos de Configuración
Antes de ejecutar, asegúrate de que los siguientes archivos estén configurados como se indica:

1.  **`Dockerfile`**: Cada uno de los 3 proyectos debe tener su propio `Dockerfile` en la raíz.

2.  **`GatewayController.php` en `miramar-api-gateway`**: Las URLs de los servicios deben apuntar a los nombres de los contenedores y sus puertos internos.
    ```php
    $baseUri = 'http://productos_app:8080';
    $baseUri = 'http://ventas_app:8080';
    ```

3.  **`VentaSeeder.php` en `miramar-ventas-clientes`**: La URL de los productos debe apuntar al nombre del contenedor y su puerto interno.
    ```php
    $productosServiceUrl = 'http://productos_app:8080';
    ```
### Ejecución
Para levantar todo el sistema, sigue estos pasos:

1.  Abre una terminal y navega a la carpeta raíz del `miramar-api-gateway` (donde se encuentra el archivo `docker-compose.yml`).
2.  Ejecuta el siguiente comando:
    ```bash
    docker-compose up --build
    ```

Este comando leerá el archivo `docker-compose.yml`, construirá las imágenes de tus tres servicios, creará los contenedores y lo pondrá todo en marcha. La primera vez puede tardar unos minutos.

### Probar la Aplicación
Una vez que los contenedores estén corriendo, toda la aplicación estará disponible a través del puerto del API Gateway:

* **URL Base:** `http://localhost:8000`
* **Ejemplo:** Una petición `GET` a `http://localhost:8000/clientes` será redirigida por el gateway al servicio `miramar-ventas-clientes` y te devolverá la lista de clientes.

Para detener todos los servicios, simplemente presiona `Ctrl+C` en la terminal donde ejecutaste el comando.