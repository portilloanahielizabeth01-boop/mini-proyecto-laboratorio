# laboratorio-pedidos — proyecto de práctica

[![CI](https://github.com/portilloanahielizabeth01-boop/mini-proyecto-laboratorio/actions/workflows/ci.yml/badge.svg)](https://github.com/portilloanahielizabeth01-boop/mini-proyecto-laboratorio/actions/workflows/ci.yml)

Sistema de gestión de pedidos de un laboratorio de análisis clínicos.
**PHP sin frameworks.** Corre en XAMPP; Composer se usa para el autoload y las herramientas de prueba.

> ⚠️ **Este proyecto está roto a propósito.**
> Cada archivo contiene deuda técnica sembrada, marcada con comentarios:
>
> ```php
> // ❌ MAL APLICADO: qué método está mal y qué principio viola
> // ✅ FORMA CORRECTA: qué patrón corresponde y cómo se estructura
> ```
>
> El objetivo no es que funcione mejor: ya funciona. El objetivo es que
> **se pueda cambiar sin miedo**.

---

## Cómo levantarlo

1. Copiar la carpeta dentro de `C:\xampp\htdocs\`.
2. (Recomendado) En la carpeta del proyecto, correr `composer install`.
   Si no tenés Composer, igual funciona: `public/index.php` trae un autoloader
   de respaldo.
3. (Opcional) Copiar `config/database.example.php` como `config/database.php`
   y completar las credenciales. Ese archivo no se sube al repositorio.
4. Iniciar Apache desde el panel de XAMPP (MySQL **no** hace falta).
5. Abrir: `http://localhost/mini-proyecto-laboratorio/public/index.php`

Acciones disponibles:

| URL | Qué hace |
|---|---|
| `public/index.php?accion=crear` | Crea un pedido (acepta `id`, `paciente`, `monto`, `tipo`) |
| `public/index.php?accion=listar` | Lista los pedidos |
| `public/index.php?accion=reporte` | Genera el reporte del día con firma, PDF y marca de agua |

---

## Pruebas e integración continua (IE3)

| Qué | Herramienta | Dónde |
|---|---|---|
| Pruebas unitarias y de integración | **PHPUnit 11** | `tests/` (configuración en `phpunit.xml`) |
| Casos de prueba documentados | Markdown | [`docs/casos-de-prueba.md`](docs/casos-de-prueba.md) |
| Lint de sintaxis | `php -l` | paso del workflow |
| Lint de estándares (PSR-12) | **PHP_CodeSniffer** (`phpcs`) | `phpcs.xml` |
| Integración continua | **GitHub Actions** | [`.github/workflows/ci.yml`](.github/workflows/ci.yml) |

Correr todo en tu máquina:

```bash
composer install       # instala PHPUnit y phpcs en vendor/
vendor/bin/phpcs       # lint: revisa el estilo PSR-12
vendor/bin/phpunit     # ejecuta los 40 casos de prueba
```

El workflow **CI** se ejecuta solo en cada `push` y en cada Pull Request, con
PHP 8.2 y 8.3: instala dependencias, corre el lint y ejecuta las pruebas. Si
algo falla, el commit o el PR queda marcado con ❌.

La persistencia está simulada en memoria para que el proyecto arranque sin
configurar MySQL. Eso **no** es parte de la deuda a corregir.

---

## Mapa de deudas

| Archivo | Síntoma sembrado | Patrón / principio | Ejercicio |
|---|---|---|---|
| `public/index.php` | Requires manuales, credenciales en el código, ruteo con `if` | Autoload + config externa + tabla de rutas | — |
| `src/Database/Connection.php` | Una conexión nueva por consulta; excepción silenciada | **Singleton** | — |
| `src/Notifications/NotificationSender.php` | `if` por tipo repetido en 3 archivos | **Factory** | Ej. 2 |
| `src/Pricing/PriceCalculator.php` | `switch` con todos los algoritmos + lógica duplicada | **Strategy** | Ej. 1 |
| `src/Events/OrderEvents.php` | Avisos encadenados a mano a clases concretas | **Observer** | Ej. 5 |
| `src/Legacy/LegacyNotifier.php` | Clase de terceros modificada + copia y pega | **Adapter** | Ej. 3 |
| `src/Reports/ReportGenerator.php` | Parámetros booleanos (`boolean trap`) | **Decorator** | Ej. 4 |
| `src/Services/OrderService.php` | Método que hace de todo | **Facade** + SRP | Ej. 6 |
| `src/Controllers/OrderController.php` | SQL + negocio + HTML en el controlador | **MVC** + SRP | Ej. 7 |
| `src/Models/Order.php` | Modelo que se persiste y calcula precios | **Repository** + Strategy | — |
| `views/orders.php` | Consulta, calcula y no escapa la salida | **MVC** | Ej. 8 |

---

## La medida de la deuda de este proyecto

El descuento de obra social (**0.7**) está escrito en **cinco archivos distintos**:

```
src/Models/Order.php
src/Pricing/PriceCalculator.php   (dos veces)
src/Services/OrderService.php
src/Controllers/OrderController.php
views/orders.php
```

Cuando el laboratorio lo cambie al 25%, ese número es exactamente
cuántos lugares hay que tocar y cuántas oportunidades hay de olvidarse uno.

**Ese es el punto de toda la unidad.**

---

## Mecánica de la clase práctica

```
1. Cada grupo toma UN archivo del mapa de deudas.       (5 min)
2. Lee los comentarios ❌ y ✅.                          (5 min)
3. Refactoriza en una rama propia: feat/patron-<nombre>  (20 min)
4. Abre un Pull Request que responda tres cosas:
     - qué deuda encontró (con la línea exacta)
     - qué patrón aplicó y por qué ese y no otro
     - qué consecuencia negativa tiene su propia solución
5. Otro grupo revisa el PR y comenta.                    (10 min)
```

El PR revisado por otro grupo es evidencia del TP Integrador.

---

## Reglas del refactor

- **No se rompe funcionalidad.** Antes y después, la app hace lo mismo.
- **Un patrón por rama.** Nada de una rama con seis cambios mezclados.
- **Se borra el código viejo.** Dejar el método anterior "por las dudas" es
  deuda nueva.
- **Se documenta la consecuencia negativa.** Un patrón sin contras analizadas
  es sobreingeniería esperando su turno.
