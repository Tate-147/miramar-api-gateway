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