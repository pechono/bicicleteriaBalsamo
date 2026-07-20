# Relevamiento técnico — Bicicletería Bálsamo (Web)

Inventario por sección: **ruta → vista → componente Livewire → acciones (métodos)**.
Sirve como mapa para documentar y mantener el proyecto. *(No incluye la app móvil `/api/mobile`.)*

Convención: las acciones son los métodos públicos que ejecutan algo (click/submit). Se omiten `render()`, `mount()`, `updating*/updated*` (reactivos) y helpers de consulta salvo que sean relevantes.

---

## Rutas públicas (sin login)

| Ruta | Archivo | Qué hace |
|---|---|---|
| `GET /` | `welcome` | Landing |
| `GET /app` — `app.download` | `app.download` | Descarga de la app móvil |
| `GET /portal/{token}` — `portal.mayorista` | `App\Http\Controllers\PortalController@show` | **Portal del cliente mayorista**: stock sin precio + cuenta corriente (si habilitada) |
| `GET /comprobante/mobile/{operacion}/{hash}` | closure → `ReportVentaO` | PDF de comprobante (desde app, hash) |
| `GET /comprobante-ingreso/mobile/{nro}/{hash}` | closure → `ReporIngreso` | PDF de ingreso (desde app, hash) |
| `GET /mobile/ingreso/{token}` — `mobile.ingreso.qr` | closure | Acceso por QR del mecánico |
| `GET /register` → redirect `/login` | — | Registro deshabilitado |

---

## 1. Dashboard
- **Ruta**: `GET /dashboard` — `dashboard`
- **Componente**: `App\Livewire\DashboardPanel`
- Panel de inicio.

---

## 2. Venta

### Venta Express
- **Ruta**: `GET /venta/express` — `venta.ventaExpress`
- **Componente**: `App\Livewire\Venta\Express\VentaExpress`
- **Acciones**:
  - `addCar($id)` / `modCar($id)` / `deletCar($id)` — agregar / modificar / quitar del carrito
  - `save($idart,$stock)` / `updateSave($idart,$stock)` — guardar cantidad (alta / edición)
  - `descuentoArt($id)` / `saveDescuento($idart)` — abrir y guardar descuento por artículo
  - `tipoVenta()` — elegir medio de pago
  - `PreguntaConfirmarVenta()` / `ConfirmarVenta()` — confirmar y registrar la venta
  - `cancelarOperacion()` — cancelar/limpiar el carrito
  - `seleccionarCliente($id)` / `limpiarCliente()` / `abrirModalCliente()` / `confirmarClienteAdd()` / `saveCliente()` — gestión de cliente
  - `abrirCajaVenta($sueltoId)` — **abrir una caja** cuando el suelto está bajo (descuenta 1 caja, suma unidades)
  - Helpers: `Total()`, `Ofeta($id)`, `stockInsufisinte($id)`, `estaEnCarrito($id)`

### Venta Card (por categoría)
- **Ruta**: `GET /venta/card` — `venta.ventaCard`
- **Componente**: `App\Livewire\Venta\Categoria\VentaCart`
- **Acciones**: `addCar` / `modCar` / `deletCar` / `save` / `updateSave` / `descuentoArt` / `saveDescuento` / `tipoVenta` / `ConfirmarVenta` / gestión de cliente (`seleccionarCliente`, `limpiarCliente`, `confirmarClienteAdd`, `abrirModalCliente`, `saveCliente`) / `cancelarOperacion`.

### Cuenta Corriente (minorista)
- **Ruta**: `GET /venta/cuentacorriente` — `venta.cuentaCorriente`
- **Componente**: `App\Livewire\Venta\CuentaCorriente`
- **Acciones**: `mostrar()`, `modalCuenta($id)`, `PonerPrecio($op)`, `eliminar($op)`, `confirmarCuenta()`, `ConfirmarPago()`, `cancelar()`.

### Pago Cuenta Corriente
- **Ruta**: `GET /venta/listcuentacorriente` — `venta.listCuentaCorriente`
- **Componente**: `App\Livewire\Venta\ListCuentaCorriente`
- **Acciones**: `modalCuenta($id)`, `pagar()`, `entregaCuentaCorriente()`, `PonerPrecio($op)`, `eliminar($op)`, `confirmarCuenta()`, `ConfirmarPago()`, `mostrar()`, `cancelar()`.

---

## 3. Servicio (Taller)

### Ingresar Bicicleta
- **Ruta**: `GET /servicio/ingresar` — `service.ingresarBike`
- **Componente**: `App\Livewire\Service\IngresarBike`
- **Acciones**: `buscarCliente()`, `seleccionarCliente($id)`, `confirmarClienteAdd()`, `saveCliente()`, `agregarProceso($id)` / `quitarProceso($id)` / `guardarSeleccion()`, `guardarTipo()` / `guardarMarca()` (alta de tipo/marca de bici), `guardarIngreso()` — registra el ingreso.
- Comprobante de ingreso: `GET /servicio/ingreso-imp/{nro}` — `service.ingresoImp` → `App\Livewire\Service\IngresoImp` (`imprimirComprobante()`, `enviarWhatsApp()`, `actualizarFechaRetiro()`).

### Registro Servicio (Egreso / Terminar)
- **Ruta base**: `GET /servicio/egreso` — `service.egresoBici`
- **Terminar** `GET /servicio/egreso/terminar/{nro}` — `service.egresoTerminar` → `App\Livewire\Service\EgresoTerminar`
  - **Acciones**: `addCar`/`modCar`/`deletCar`/`save`/`updateSave`/`descuentoArt`/`saveDescuento`/`tipoVenta`/`ConfirmarVenta`, `procesosCargar($nro)`, `cancelarOperacion()`.
- **Entregar** `GET /servicio/terminar/venta/{nro}` — `service.terminarVentaProceso` → `App\Livewire\Service\TerminarVentaProceso`
  - **Acciones**: mismas del carrito + `desdeProcesos($id)`, `ConfirmarVenta()`.

### Calendario
- **Ruta**: `GET /servicio/calendario` — `service.calendarioServicios`
- **Componente**: `App\Livewire\Service\CalendarioServicios`
- **Acciones**: `semanaAnterior()` / `semanaSiguiente()`, `abrirModal($nroId)` / `cerrarModal()`.

### Cuenta Mecánico
- **Ruta**: `GET /servicio/cuenta-mecanico` — `service.cuentaMecanico`
- **Componente**: `App\Livewire\Service\CuentaMecanico`
- **Acciones**: `agregarItem()`, `confirmarCierre($mecanicoId)`, `cerrarSemana()`, `cancelarCierre()`.

---

## 4. Stock

### Ver Stock
- **Ruta**: `GET /stock` — `stock.index`
- **Componente**: `App\Livewire\Stock\StockLivewire`
- **Acciones**:
  - `confirmarArticuloEdit($id)` → `preguntaCambiarStock($id)` → `CambiarStock($id)` — editar artículo completo (nombre, código, categoría, proveedor, precios, descuento, detalles, stock)
  - `confirmarArticuloDeletion($id)` → `deleteArticulo()` — desactivar
  - `ActivarArticuloEdit($id)` → `ConfirmarActivar()` — activar (con grupo/margen/stock)
  - `abrirGenerarSuelto($id)` → `guardarSuelto()` — **generar suelto** desde una caja
  - `abrirCaja($sueltoId)` — **abrir caja** (1 caja → N unidades)
  - `sortby($field)` — ordenar

### Pedido a Proveedor
- **Ruta**: `GET /stock/pedido` — `stock.pedido`
- **Componente**: `App\Livewire\Stock\PedidoLivewire`
- **Acciones**: `addCar($id)` / `ModCar($id)` / `elimCar($id)` / `borrarCar()`, `crearPedido()`, `modPedido($id)`, `confirmarElimin()`.
- **Confirmar**: `GET /stock/pedido/confirmar` — `stock.confirmarPedido` → `App\Livewire\Stock\ConfirmarPedido` (`guardarPedido()`).
- **Imprimir pedido**: `GET /stock/pedido/pedido/{id}` — `pedidoImprimir` → `PrintPedido@generateReport`.

### Pedidos Realizados
- **Ruta**: `GET /stock/pedidorealizados` — `stock.pedidoRealizado`
- **Componente**: `App\Livewire\Stock\PedidoRealizados`
- **Acciones**: `verPed($id)`, `enviarWhatsApp()`, `cerrarModal()`.

### Imprimir Stock
- **Ruta**: `GET /stock/stock` — `stockImprimir` → `App\Livewire\Print\StockImprimir@generateReport` (PDF).

### Actualizar desde factura
- **Ruta**: `GET /stock/actualizar-factura` — `stock.actualizarFactura`
- **Componente**: `App\Livewire\Stock\ActualizarFactura`
- **Acciones**: `buscar()` — busca por proveedor+código; `guardar()` — suma stock / actualiza precio; `darDeAlta()` — alta desde el catálogo si no está en stock (con grupo, categoría, IVA).

---

## 5. Estadísticas

### Más Vendidos
- **Ruta**: `GET /informes/masvendidos` — `informes.masVendidos`
- **Componente**: `App\Livewire\Informes\MasVendidos`
- **Acciones**: `setPeriodo($p)`, `actualizarDatos()`, `exportarExcel()`, `exportarPDF()`.

---

## 6. Operaciones

### Operaciones
- **Ruta**: `GET /operacion/list` — `operacion.list`
- **Componente**: `App\Livewire\Operacion\Operacionlivewire`
- **Acciones**: `tipoVenta()`, `PreguntaConfirmarVenta()`, `ConfirmarVenta()`, `confirmarClienteAdd()`, `saveCliente()`, `cancelarOperacion()`.
- **Imprimir**: `GET /operacion/operacion/info-op-imprimir/{datos}` — `infoOpImprimir` → `PrintOperacion@generateReport`.

### Ventas (listado)
- **Ruta**: `GET /venta/list` — `venta.list`
- **Componente**: `App\Livewire\Venta\ListVenta`
- **Acciones**: `limpiarFiltros()`, `exportarPDF()`, `mostrarResumen()`, cancelaciones de filtro (`cancelarDE/D/M/A`).

---

## 7. Mayorista

### Venta Mayorista
- **Ruta**: `GET /mayorista` — `mayorista.index`
- **Componente**: `App\Livewire\Mayorista\VentaMayorista`
- **Acciones**: `agregarAlCarrito($id)`, `quitarDelCarrito($i)`, `actualizarCantidad($i,$c)`, `actualizarPorcentaje($i,$p)`, `seleccionarCliente($id)`, `confirmar()`, `procesarVenta()`.

### Clientes (mayoristas)
- **Ruta**: `GET /mayorista/clientes` — `mayorista.clientes`
- **Componente**: `App\Livewire\Mayorista\ClientesMayorista`
- **Acciones**: `nuevo()`, `editar($id)`, `guardar()` (incluye flag **cuenta_corriente_habilitada**), `enviarAcceso($id)` — **envía por WhatsApp el link del portal**, `confirmarEliminar($id)` / `eliminar()`.

### Cuenta Corriente (mayorista)
- **Ruta**: `GET /mayorista/cuenta-corriente` — `mayorista.cuentaCorriente`
- **Componente**: `App\Livewire\Mayorista\CuentaCorrienteMayorista`
- **Acciones**: `seleccionarCliente($id)`, `cargarMovimientos()`, `abrirPago()`, `registrarPago()`.

---

## 🔒 8. Administración

### Gestión

| Ítem | Ruta | Componente | Acciones principales |
|---|---|---|---|
| Artículo (grupos) | `GET /proveedor/grupoarticulo` — `proveedor.articuloGrupo` | `App\Livewire\Proveedor\GrupoArticulos` | `seleccionarGrupo`, `abrirIngresar`/`cerrarIngresar`, `agregar($id)` / `quitar($id)`, `crearGrupo` |
| Artículo (alta CRUD) | `GET /articulo/grupo` — `articulo.articuloGrupo` | `App\Livewire\Articulolivewire` | `confirmarArticuloAdd`/`saveArticulo`, `confirmarArticuloEdit`/`updateArticulo`, `confirmarArticuloDeletion`/`deleteArticulo`, `ActivarArticuloEdit`/`ConfirmarActivar`, `addCategoria`/`saveCategoria`, `calcular` |
| Importar Lista de Precios | `GET /articulo/importar-lista` — `articulo.importarLista` | `App\Livewire\Articulo\ImportarLista` | `analizar($parser)`, `confirmar($parser)`, `cancelar()` |
| Catálogo de listas | `GET /articulo/catalogo` — `articulo.catalogo` | `App\Livewire\Articulo\Catalogo` | `abrirPromover($id)` / `confirmarPromover()` / `usarPublico()`, `recalcularCotizacion()`, `actualizarPrecios()` |
| Categorías | `GET /articulo/categorias` — `articulo.categorias` | `App\Livewire\Articulo\Categorias` | `crear()`, `editar($id)`/`guardarEdicion()`, `eliminar($id)` |
| Mano de Obra | `GET /articulo/mano-de-obra` — `articulo.manoDeObra` | `App\Livewire\Articulo\ManoDeObra` | `nuevo()`, `editar($id)`, `guardar()`, `toggleActivo($id)` |
| Cambio Precio Artículos | `GET /gestion/precio/preciocambiar` — `gestion.precio.precioCambiar` | `App\Livewire\Gestion\Precio\CambiarPrecio` | `cambiarPrecio($id)`, `calcular()`, `nuevoPrecio($id)` |
| Cambio Precio Grupo | `GET /gestion/precio/preciogrupo` — `gestion.precio.precioGrupo` | `App\Livewire\Gestion\Precio\PrecioGrupo` | `cambiarPorcentaje($id)`, `cambiarPrecio()`, `cambiarPrecioGrupo($id)`, `activar()`/`noActivar()` |
| Proveedor | `GET /proveedor` — `proveedor.proveedor` | (ver `ArticuloGrupo`/proveedor) | alta/gestión de proveedores y grupos |
| Crear grupo | `GET /proveedor/creargrupo` — `proveedor.crearGrupo` | `App\Livewire\Proveedor\CrearGrupo` | crear grupo de proveedor |
| Gestionar Usuarios | `GET /gestion/user` — `admin.gestionUsuario` | `App\Livewire\Admin\GestionUser` | `ingresarUsuario`/`addUser`, `editarUsuario($id)`/`updateUser`, `toggleUserStatus($id)` |

### Ofertas

| Ítem | Ruta | Componente | Acciones principales |
|---|---|---|---|
| Ofertas (listado) | `GET /oferta/list` — `oferta.ofertaList` | `App\Livewire\Oferta\OfertaLivewire` | listado |
| Crear | `GET /oferta/crear` — `oferta.ofertaCreate` | `App\Livewire\Oferta\OfertaCreate` | `addOferta($id)` / `deleteOferta($id)`, `precioOfertaCambiar($id)`/`cambiar()`, `CrearOferta()` / `ConfirmarOferta()` |
| Operaciones (ofertas) | `GET /oferta/gestion` — `oferta.ofertaGestion` | `App\Livewire\Oferta\OfertaGestion` / `OfertaActivar` | `publicarOferta($id)`, `actualizarOferta($id)`, `terminarPconVenta($id)` / `terminarPSinVenta($id)` |

---

## 9. Usuario
- **Ruta**: `GET /gestion/user` (perfil / cambio de password) — Jetstream `profile` + `App\Livewire\Admin\ChangePassword` (`updatePassword()`).

---

## 10. Cierre de Caja
- **Ruta**: `GET /cierre` — `cierre.cierreCaja`
- **Componente**: `App\Livewire\Cierre\CierreCaja`
- **Acciones**: `realizarCierre()`, `cerrarModal()`.

---

## Otros / transversales

| Función | Dónde |
|---|---|
| Clientes (minorista) | `GET /cliente` — `cliente.index` → `App\Livewire\Clientelivewire` (`saveCliente`, `editCliente`, `confirmarClienteDeletion`/`deleteCliente`) |
| Cargar imágenes | `GET /imagenes/cargar` — `imagenes.imagenes` → `App\Livewire\Imagenes\CargarImagenes` |
| Comprobante de venta (PDF) | `GET /report/comprobante/{operacion}` — `comprobante` → `ReportVentaO@generateReport`; WhatsApp: `venta.reporte.whatsapp` → `ReportVentaController@enviarWhatsApp` |
| **WhatsApp (envío)** | Trait `App\Livewire\Traits\WithWhatsApp` (`sendWhatsAppMessage`, `sendWhatsAppPdf`) → tabla `whats_app_queues` → comando `ProcesarColaWhatsApp` (server Node) |
| **Backups** | Comando `backup:db` (Kernel, diario) |
| Búsqueda | `App\Support\Busqueda::palabras()` (palabras sueltas + abrev-código) |
| Parser de listas | `App\Support\ListaPreciosParser` (Excel) |

---

*Generado como relevamiento del proyecto. Mantener actualizado al agregar rutas/acciones.*
