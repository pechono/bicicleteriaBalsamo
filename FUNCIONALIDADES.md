# Funcionalidades — Bicicletería Bálsamo (Web)

Registro de todo lo que hace el sistema web, ordenado por sección del menú.
*(No incluye la app móvil, que consume su propia API `/api/mobile`.)*

Roles: **Admin** (ve todo) y **Mecánico/Usuario** (acceso limitado: Taller y consulta de artículos). Las secciones marcadas 🔒 son solo de Admin.

---

## 1. Dashboard
- Pantalla de inicio del sistema tras iniciar sesión.

---

## 2. Venta
Punto de venta y cuentas corrientes de clientes minoristas.

### 🚀 Venta Express
- Buscador de artículos por palabras sueltas, código y abrev-código (ej. `DalS-11115bk`).
- Carrito de venta: agregar / modificar cantidad / quitar artículos (botones con íconos).
- Modal de **cantidad** y de **descuento** por artículo (diseño limpio, con desglose).
- Gestión de cliente integrada: buscar, seleccionar o cargar cliente nuevo (apellido+nombre; DNI opcional).
- Tipos de venta (efectivo, tarjeta, débito, cuenta corriente, etc.).
- Cálculo de total con descuentos por ítem.
- **Suelto**: si un artículo suelto está bajo/terminado y su caja tiene stock, el número de stock es un botón que **abre otra caja** (descuenta 1 caja, suma N unidades) preguntando antes.
- Genera comprobante de venta (PDF) y descuenta stock.
- Marca de artículos en **oferta**.

### 💳 Venta Card (por Categoría)
- Venta navegando por categorías de artículos.
- Mismo módulo de gestión de cliente que Venta Express.
- Carrito con modales de cantidad y descuento.

### 📊 Cuenta Corriente
- Registro y consulta de deudas de clientes con cuenta corriente.

### 💰 Pago Cuenta Corriente
- Registrar pagos/abonos a la cuenta corriente de un cliente.

---

## 3. Servicio (Taller)
Flujo completo de reparación de bicicletas.

### 🚲 Ingresar Bicicleta
- Alta de ingreso de una bici al taller (cliente, datos de la bici, trabajo solicitado).
- Genera comprobante de ingreso (A5).

### 🔧 Registro Servicio (Egreso / Terminar)
- **Terminar bici**: cargar artículos y mano de obra usados en la reparación (modales de cantidad y descuento), asignar mecánico y montos.
- **Entregar**: cerrar la reparación con costos fijos + extras, generar la operación/venta y el comprobante.
- Envío del comprobante por **WhatsApp**.
- Mano de Obra: se cobra como servicio (categoría "Servicio"), admite tarjeta/débito.

### 📅 Calendario
- Vista de calendario de servicios/turnos del taller.

### 💰 Cuenta Mecánico
- Cuenta de trabajos y montos por mecánico (para liquidar su parte).

---

## 4. Stock
Inventario, pedidos y actualización de precios.

### 📦 Ver Stock
- Listado de artículos activos con búsqueda y filtro por categoría (tabla en desktop, tarjetas en mobile).
- Semáforo de **stock bajo** (rojo cuando stock ≤ mínimo).
- **Editar** artículo (modal completo): nombre, código, categoría, proveedor, precio costo, precio venta, descuento, detalles, stock y stock mínimo. Registra historial de precio si cambia.
- **Activar** artículos inactivos (con grupo, margen y stock en un paso).
- **Quitar** (desactivar) artículo.
- **Suelto**: badge `🔓 Suelto ×N` / `📦 Caja`; botón **Generar suelto** (crea el artículo suelto vinculado, código `¬S`) y **Abrir caja** (pasa 1 caja a N unidades).

### 📝 Pedido a Proveedor
- Armar un pedido de reposición a un proveedor.

### ✅ Pedidos Realizados
- Historial de pedidos hechos, con detalle ("Ver pedido").

### 🖨️ Imprimir Stock
- Listado de stock imprimible (PDF/impresión).

### 🧾 Actualizar desde factura
- Al recibir una factura: elegir proveedor + código, sumar lo recibido al stock y/o actualizar el precio (semáforo ▲/▼, ajusta la venta manteniendo el margen).
- Si el código no está en stock pero sí en el catálogo, ofrece **darlo de alta** (con grupo, categoría e IVA).

---

## 5. Estadísticas

### 📊 Más Vendidos
- Informe de artículos más vendidos (unidades / períodos).

---

## 6. Operaciones

### 📋 Operaciones
- Listado de operaciones registradas (ventas, servicios, etc.).

### 💰 Ventas
- Listado/historial de ventas realizadas.

---

## 7. Mayorista
Módulo de venta a clientes mayoristas.

### 🛒 Venta Mayorista
- Punto de venta mayorista (precios con % mayorista por artículo o por grupo).

### 👤 Clientes (mayoristas)
- Alta y gestión de clientes mayoristas.

### 📋 Cuenta Corriente (mayorista)
- Cuenta corriente y saldos de clientes mayoristas.

---

## 🔒 8. Administración

### Gestión

#### 📦 Artículo (Artículos por Grupo)
- Crear grupos por proveedor y **asignar artículos a un grupo** (modal "Ingresar artículo" con la lista de artículos sin grupo). Sirve para los aumentos por grupo.

#### 📥 Importar Lista de Precios
- Importar la lista de un proveedor desde **Excel** (ej. Dal Santo). Soporta USD con cotización.

#### 🗂️ Catálogo de listas
- Ver todo lo importado (`lista_articulos`), filtrar por proveedor, ver pendientes.
- **Pasar a artículos** (promover): crea el artículo activo con grupo, categoría, stock y precio (costo + IVA + % del grupo).
- **Recalcular por cotización** (ítems en USD) y **actualizar precios** de los que ya están en stock (mantiene el margen).

#### 📂 Categorías
- Crear, renombrar y eliminar categorías de artículos ("Servicio" y "General" protegidas; al borrar, sus artículos pasan a General).

#### 🔧 Mano de Obra
- Vista dedicada para los servicios de mano de obra (categoría "Servicio").

#### 💰 Cambio de Precio — Artículos
- Cambiar precios de artículos individualmente.

#### 📊 Cambio de Precio — Grupo
- Aplicar un cambio de precio a todo un grupo de artículos de una vez (aumentos).

#### 🏭 Proveedor
- Alta/gestión de proveedores (nombre, abreviatura, IVA incluido, etc.) y creación de grupos.

#### 👥 Gestionar Usuarios
- Alta y administración de usuarios y roles del sistema.

### Ofertas

#### 🏷️ Ofertas
- Listado de ofertas.

#### ✨ Crear
- Crear una oferta sobre artículos.

#### ⚙️ Operaciones (Ofertas)
- Activar/desactivar y gestionar ofertas.

---

## 9. Usuario

### 👥 Cambiar Password
- Cambio de contraseña del usuario logueado (perfil).

---

## 10. Cierre de Caja
- Cierre de caja diario: totales por medio de pago, total de mano de obra aparte + el del contador (MdO tarjeta/débito incluidos).

---

## Funcionalidades transversales
Presentes en varios módulos, no en un ítem del menú:

- **WhatsApp**: envío de comprobantes y listas en PDF a clientes/proveedores (servidor Node local + ngrok).
- **PDFs**: comprobantes de venta, ingreso (A5), pedido a proveedor, stock (dompdf).
- **Códigos QR**: se generan por artículo (BaconQrCode).
- **Búsqueda inteligente**: por palabras sueltas en cualquier orden + abrev-código, en Stock/Venta/Mayorista/Pedido/Oferta/Grupos/Taller.
- **Precios en enteros** (sin centavos).
- **Roles**: Admin vs Mecánico (menú y accesos por rol).
- **Tema y responsive**: paleta brand configurable + tablas que pasan a tarjetas en mobile.
- **Backups**: comando `backup:db` (dump gzip nativo, sin mysqldump) programado diario.
- **Catálogo → activo**: arquitectura de `lista_articulos` (catálogo importado) separado de `articulos` (stock activo), con flujo de promoción.
- **Caja ↔ Suelto**: artículo caja cerrada genera su suelto (código `¬S`), con unidades por caja y "abrir caja".

---

*Documento generado como referencia. Actualizar a medida que se agreguen funcionalidades.*
