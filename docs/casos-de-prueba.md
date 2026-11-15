# Casos de prueba — laboratorio-pedidos

Documento de casos de prueba del **IE3 (segundo parcial)** de Metodología de Sistemas II.

Cada caso sigue la estructura vista en la Unidad 5 (*Testing de software*): **identificador, descripción, precondiciones, datos de entrada, pasos y resultado esperado**. Por cada funcionalidad hay al menos un caso **positivo (+)**, que sigue el camino feliz, y al menos uno **negativo (−)**, que usa datos inválidos o inesperados y verifica que el sistema responda bien al error.

Todos los casos están **automatizados con PHPUnit**: el número `CP-xx` aparece en el nombre de cada prueba, así que se puede rastrear desde este documento hasta el código y hasta la salida del workflow de GitHub Actions.

## Cómo ejecutarlos

```bash
composer install        # instala PHPUnit y PHP_CodeSniffer
vendor/bin/phpunit      # ejecuta los 40 casos
```

También se ejecutan solos en cada `push` y en cada Pull Request (ver `.github/workflows/ci.yml`).

## Resumen

| Funcionalidad | Patrón / técnica | Casos | Positivos | Negativos | Archivo de prueba |
|---|---|---|---|---|---|
| **F1** Cálculo de precio según el tipo de paciente | Strategy | CP-01 a CP-07 | 6 | 1 | `tests/Unit/Pricing/PricingStrategyTest.php` |
| **F2** Creación y envío de notificaciones | Factory | CP-08 a CP-10 | 2 | 1 | `tests/Unit/Notifications/NotificationSenderTest.php` |
| **F3** Conexión única a la base de datos | Singleton | CP-11 a CP-15 | 2 | 3 | `tests/Unit/Database/ConnectionTest.php` |
| **F4** Modelo de pedido (validación y encapsulamiento) | Encapsulamiento | CP-16 a CP-19 | 1 | 3 | `tests/Unit/Models/OrderTest.php` |
| **F5** Guardado y listado de pedidos | Repository | CP-20 a CP-21 | 1 | 1 | `tests/Unit/Repositories/OrderRepositoryTest.php` |
| **F6** Avisos al crear un pedido | Observer | CP-22 a CP-24 | 2 | 1 | `tests/Unit/Events/OrderSubjectTest.php` |
| **F7** Uso del sistema de avisos heredado | Adapter | CP-25 a CP-26 | 1 | 1 | `tests/Unit/Legacy/LegacyNotifierAdapterTest.php` |
| **F8** Generación de reportes | Decorator | CP-27 a CP-29 | 2 | 1 | `tests/Unit/Reports/ReportDecoratorTest.php` |
| **F9** Crear un pedido completo | Facade | CP-30 a CP-32 | 1 | 2 | `tests/Unit/Services/OrderServiceTest.php` |
| **F10** Controlador y vistas | MVC | CP-33 a CP-36 | 3 | 1 | `tests/Unit/Controllers/OrderControllerTest.php` |
| **F11** Tabla de rutas | Autoload + tabla de rutas | CP-37 a CP-38 | 1 | 1 | `tests/Unit/Routing/RouterTest.php` |
| **F12** Sistema completo de punta a punta (smoke test) | Integración | CP-39 a CP-40 | 1 | 1 | `tests/Integration/PublicIndexTest.php` |
| **Total** | | **40** | **23** | **17** | |

Prioridad: todos los casos son de prioridad **alta**, porque cubren las funciones que se refactorizaron con patrones (lo que se puede romper al cambiar el diseño).

Tipos de prueba (según la Unidad 5): F1 a F11 son pruebas **unitarias**; F9 y F10 combinan varias piezas reales y funcionan además como pruebas de **integración**; F12 es una prueba de **humo (smoke)** de punta a punta. Al correr en cada push, todas funcionan como pruebas de **regresión**: avisan si un cambio rompe algo que ya andaba.

## F1 · Cálculo de precio según el tipo de paciente

Patrón: **Strategy** · Prueba automatizada: `tests/Unit/Pricing/PricingStrategyTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-01** | Positivo (+) | Paciente particular paga el monto completo | Ninguna | Estrategia `PrivatePatientStrategy`, monto = 15000 | 1. Crear `PriceCalculator` con la estrategia.<br>2. Llamar a `calculate(15000)`. | Devuelve **15000** |
| **CP-02** | Positivo (+) | Paciente con obra social paga el 70% | Ninguna | Estrategia `InsuranceStrategy` (30% por defecto), monto = 15000 | 1. Crear `PriceCalculator` con la estrategia.<br>2. Llamar a `calculate(15000)`. | Devuelve **10500** |
| **CP-03** | Positivo (+) | Paciente jubilado paga el 50% | Ninguna | Estrategia `RetireeStrategy`, monto = 15000 | 1. Crear `PriceCalculator` con la estrategia.<br>2. Llamar a `calculate(15000)`. | Devuelve **7500** |
| **CP-04** | Positivo (+) | Prepaga (agregada sin modificar `PriceCalculator`) paga el 80% | Ninguna | Estrategia `PrepaidStrategy`, monto = 15000 | 1. Crear `PriceCalculator` con la estrategia.<br>2. Llamar a `calculate(15000)`. | Devuelve **12000** |
| **CP-05** | Positivo (+) | El precio con IVA reutiliza la estrategia (el descuento no se duplica) | Ninguna | Estrategia `InsuranceStrategy`, monto = 15000 | 1. Crear `PriceCalculator` con la estrategia.<br>2. Llamar a `calculateWithVat(15000)`. | Devuelve **12705** (10500 × 1,21) |
| **CP-06** | Positivo (+) | El resolver devuelve la estrategia que corresponde a cada tipo | Ninguna | Tipos `particular`, `obra_social`, `jubilado`, `prepaga` | 1. Crear `PricingStrategyResolver`.<br>2. Pedir la estrategia de cada tipo. | Devuelve `PrivatePatient`, `Insurance`, `Retiree` y `Prepaid` respectivamente |
| **CP-07** | Negativo (−) | Un tipo de paciente desconocido es rechazado | Ninguna | Tipo = `vip` | 1. Crear `PricingStrategyResolver`.<br>2. Llamar a `forPatientType('vip')`. | Lanza `InvalidArgumentException`: *Tipo de paciente no soportado: vip* |

## F2 · Creación y envío de notificaciones

Patrón: **Factory** · Prueba automatizada: `tests/Unit/Notifications/NotificationSenderTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-08** | Positivo (+) | La fábrica crea el tipo pedido, sin importar mayúsculas | Ninguna | Tipos `email` y `SMS` | 1. Llamar a `NotificationSender::make('email')`.<br>2. Llamar a `NotificationSender::make('SMS')`. | Devuelve `EmailNotification` y `SmsNotification` |
| **CP-09** | Positivo (+) | Enviar por email devuelve true y muestra el aviso | Ninguna | Tipo `email`, destino `juan@mail.com`, mensaje `Pedido 7 creado` | 1. Llamar a `NotificationSender::send(...)`. | Devuelve `true` y muestra `[EMAIL] para juan@mail.com: Pedido 7 creado` |
| **CP-10** | Negativo (−) | Un tipo de notificación no soportado es rechazado | Ninguna | Tipo = `whatsapp` | 1. Llamar a `NotificationSender::make('whatsapp')`. | Lanza `InvalidArgumentException`: *Tipo de notificación no soportado: whatsapp* |

## F3 · Conexión única a la base de datos

Patrón: **Singleton** · Prueba automatizada: `tests/Unit/Database/ConnectionTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-11** | Positivo (+) | `getInstance()` devuelve siempre la misma conexión | Tablas en memoria vacías | — | 1. Llamar dos veces a `Connection::getInstance()`.<br>2. Comparar los objetos. | Es **el mismo objeto** (`assertSame`) |
| **CP-12** | Positivo (+) | Lo que se inserta se lee desde otra referencia | Tablas vacías | Tabla `orders`, fila `['id' => 1]` | 1. Insertar con una llamada a `getInstance()`.<br>2. Leer con otra llamada a `getInstance()`. | `all('orders')` devuelve `[['id' => 1]]` |
| **CP-13** | Negativo (−) | No se puede crear la conexión con `new` | Ninguna | — | 1. Inspeccionar el constructor de `Connection`. | El constructor es **privado** |
| **CP-14** | Negativo (−) | No se puede clonar la conexión | Ninguna | — | 1. Intentar `clone Connection::getInstance()`. | Lanza `Error` (clonado bloqueado) |
| **CP-15** | Negativo (−) | Insertar una fila vacía lanza un error en vez de ocultarlo | Tablas vacías | Tabla `orders`, fila `[]` | 1. Llamar a `insert('orders', [])`. | Lanza `InvalidArgumentException` (antes la excepción se tragaba) |

## F4 · Modelo de pedido (validación y encapsulamiento)

Patrón: **Encapsulamiento** · Prueba automatizada: `tests/Unit/Models/OrderTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-16** | Positivo (+) | Un pedido válido conserva sus datos | Ninguna | id 1, `Juan Perez`, 15000, `obra_social` | 1. Crear `new Order(...)`.<br>2. Leer sus propiedades. | Las propiedades tienen los valores ingresados |
| **CP-17** | Negativo (−) | No se puede crear un pedido con monto negativo | Ninguna | monto = −50000 | 1. Crear `new Order(1, 'Juan Perez', -50000, 'particular')`. | Lanza `InvalidArgumentException`: *El monto no puede ser negativo.* |
| **CP-18** | Negativo (−) | No se puede crear un pedido sin paciente | Ninguna | paciente = `'   '` | 1. Crear el pedido con paciente vacío. | Lanza `InvalidArgumentException`: *El paciente es obligatorio.* |
| **CP-19** | Negativo (−) | Un pedido creado no se puede modificar | Pedido válido creado | `$order->amount = -50000` | 1. Crear un pedido válido.<br>2. Intentar cambiar el monto. | Lanza `Error` (propiedad `readonly`) |

## F5 · Guardado y listado de pedidos

Patrón: **Repository** · Prueba automatizada: `tests/Unit/Repositories/OrderRepositoryTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-20** | Positivo (+) | Un pedido guardado aparece en el listado con su total | Conexión vacía | Pedido 3, `Ana Gomez`, 22000, `particular`, total 22000 | 1. `save($pedido, 22000)`.<br>2. `findAll()`. | Devuelve una fila con id, paciente, monto, tipo y total |
| **CP-21** | Negativo (−) | Sin pedidos guardados el listado está vacío | Conexión vacía | — | 1. `findAll()` sin guardar nada. | Devuelve `[]`, sin error |

## F6 · Avisos al crear un pedido

Patrón: **Observer** · Prueba automatizada: `tests/Unit/Events/OrderSubjectTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-22** | Positivo (+) | Se avisa a todos los observadores suscriptos, en orden | `OrderSubject` con `EmailObserver` y `DashboardObserver` | Pedido 5 | 1. Suscribir los dos observadores.<br>2. `notify($pedido)`. | Muestra primero el aviso de email y después el del dashboard |
| **CP-23** | Positivo (+) | Se suma un observador nuevo sin modificar `OrderSubject` | `OrderSubject` vacío | Observador espía, pedido 9 | 1. Suscribir el espía.<br>2. `notify($pedido)`. | El espía recibió el pedido 9 |
| **CP-24** | Negativo (−) | Sin observadores, notificar no falla ni muestra nada | `OrderSubject` vacío | Pedido 1 | 1. `notify($pedido)` sin suscriptos. | No hay salida, no hay error y `count()` = 0 |

## F7 · Uso del sistema de avisos heredado

Patrón: **Adapter** · Prueba automatizada: `tests/Unit/Legacy/LegacyNotifierAdapterTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-25** | Positivo (+) | El adapter traduce `send()` a `sendMessage()` | Ninguna | destino `recepcion`, mensaje `Pedido creado` | 1. Crear `LegacyNotifierAdapter(new LegacyNotifier())`.<br>2. Llamar a `send(...)`. | Devuelve `true` y muestra `[LEGACY] recepcion: Pedido creado` |
| **CP-26** | Negativo (−) | El sistema heredado solo no cumple nuestro contrato | Ninguna | — | 1. Verificar si `LegacyNotifier` implementa `NotificationInterface`.<br>2. Verificar el adapter. | `LegacyNotifier` **no** lo implementa y el adapter **sí** |

## F8 · Generación de reportes

Patrón: **Decorator** · Prueba automatizada: `tests/Unit/Reports/ReportDecoratorTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-27** | Positivo (+) | El reporte base no tiene agregados | Ninguna | Contenido `Pedido 1` | 1. `new BasicReport('Pedido 1')`.<br>2. `generate()`. | `Reporte: Pedido 1` |
| **CP-28** | Positivo (+) | Se encadenan firma, PDF y marca de agua | Ninguna | Contenido `Pedido 1` | 1. Envolver el reporte con los tres decoradores.<br>2. `generate()`. | `Reporte: Pedido 1 + firma digital + PDF + marca de agua` |
| **CP-29** | Negativo (−) | Envolver en otro orden da otro resultado | Ninguna | Contenido `X` | 1. Armar firma→PDF y PDF→firma.<br>2. Comparar los resultados. | Los resultados son **distintos** (consecuencia documentada del patrón) |

## F9 · Crear un pedido completo

Patrón: **Facade** · Prueba automatizada: `tests/Unit/Services/OrderServiceTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-30** | Positivo (+) | Un pedido de obra social calcula 10500, se guarda y se avisa | Conexión vacía, observador espía suscripto | Pedido 1, `Juan Perez`, 15000, `obra_social` | 1. `createOrder($pedido)`.<br>2. Revisar el total, el listado y el espía. | Total **10500**, 1 pedido guardado y el espía avisado |
| **CP-31** | Negativo (−) | Un pedido con monto cero no se guarda ni se avisa | Conexión vacía, espía suscripto | Pedido 2, monto 0 | 1. `createOrder($pedido)`. | Lanza *Monto inválido*, el listado queda vacío y nadie es avisado |
| **CP-32** | Negativo (−) | Un tipo de paciente desconocido no guarda nada | Conexión vacía, espía suscripto | Pedido 3, tipo `vip` | 1. `createOrder($pedido)`. | Lanza `InvalidArgumentException`, el listado queda vacío y nadie es avisado |

## F10 · Controlador y vistas

Patrón: **MVC** · Prueba automatizada: `tests/Unit/Controllers/OrderControllerTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-33** | Positivo (+) | Crear un pedido muestra el paciente y el total | Conexión vacía | id 1, `Juan Perez`, 15000, `obra_social` | 1. `create($datos)`. | El HTML contiene *Pedido creado*, *Paciente: Juan Perez* y *Total: $ 10500* |
| **CP-34** | Negativo (−) | Un nombre con código HTML se muestra escapado | Conexión vacía | paciente = `<script>alert(1)</script>` | 1. `create($datos)`. | El HTML **no** contiene `<script>`, sino `&lt;script&gt;…` (sin XSS) |
| **CP-35** | Positivo (+) | El listado muestra los pedidos guardados | Pedido de Ana Gomez guardado | — | 1. `index()`. | El HTML contiene `Ana Gomez` y `22000` |
| **CP-36** | Positivo (+) | El reporte del día sale decorado | Ninguna | — | 1. `report()`. | `Reporte: Pedidos del dia + firma digital + PDF + marca de agua` |

## F11 · Tabla de rutas

Patrón: **Autoload + tabla de rutas** · Prueba automatizada: `tests/Unit/Routing/RouterTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-37** | Positivo (+) | Una acción registrada ejecuta su controlador | Router con la acción `listar` | Acción `listar` | 1. `add('listar', ...)`.<br>2. `dispatch('listar')`. | Devuelve la respuesta del controlador |
| **CP-38** | Negativo (−) | Una acción que no existe responde *Accion no encontrada* | Router vacío | Acción `borrar` | 1. `dispatch('borrar')`. | Devuelve `Accion no encontrada` (index.php responde 404) |

## F12 · Sistema completo de punta a punta (smoke test)

Patrón: **Integración** · Prueba automatizada: `tests/Integration/PublicIndexTest.php`

| ID | Tipo | Descripción | Precondiciones | Datos de entrada | Pasos | Resultado esperado |
|---|---|---|---|---|---|---|
| **CP-39** | Positivo (+) | `accion=listar` muestra los dos pedidos de ejemplo | Ninguna (datos de demo) | `?accion=listar` | 1. Ejecutar `public/index.php` en un proceso PHP aparte. | Aparecen Juan Perez (10500) y Ana Gomez (22000) |
| **CP-40** | Negativo (−) | `accion=crear` con monto 0 muestra error y no crea el pedido | Ninguna | `?accion=crear&monto=0` | 1. Ejecutar `public/index.php`. | Muestra *Error: Monto inválido* y **no** muestra *Pedido creado* |

## Resultados obtenidos

Última ejecución: **40 de 40 casos pasan** (✅), con PHP 8.2 y 8.3. La evidencia es la ejecución del workflow **CI** en la pestaña *Actions* del repositorio.
