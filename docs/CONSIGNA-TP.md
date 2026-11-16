# TP Integrador — Unidades 1 y 2
## Repositorios de software + Patrones de diseño

**Metodología de Sistemas II — TUP — UTN FRRe (sede Formosa)**
Comisiones 1 y 2 · 2º cuatrimestre 2026

| | |
|---|---|
| **Modalidad** | Grupal |
| **Tipo de instancia** | Formativa — se evalúa por criterios de logro, sin nota numérica |
| **Fecha de entrega** | **25/09/2026** |
| **Canal de consultas** | Slack de la cátedra, lunes a viernes de 8:30 a 20:00 |

Este TP fusiona los trabajos previstos para las Unidades 1 y 2 en una sola
entrega integradora, y se realiza sobre el mismo proyecto que van a hacer
evolucionar durante todo el cuatrimestre.

---

## 1. Idea del trabajo

Un sistema no se muere porque no funciona. Se muere porque **nadie se anima
a cambiarlo**.

El trabajo consiste en tomar un proyecto con deuda técnica, **diagnosticarla
con evidencia**, pagarla aplicando patrones de diseño, y hacerlo con un flujo
de trabajo de repositorio que otro equipo pueda auditar.

Las dos unidades van juntas porque la deuda vive en dos capas:

```
DEUDA DE PROCESO  (Unidad 1)   →  cómo colaboramos sobre el código
DEUDA DE DISEÑO   (Unidad 2)   →  cómo está organizado el código
```

---

## 2. Punto de partida

Cada grupo elige **una** de estas dos opciones:

**Opción A — Proyecto de la cátedra.**
Fork del repositorio `laboratorio-pedidos`. Está roto a propósito y trae el
mapa de deudas y los ejercicios ya identificados.

**Opción B — Proyecto propio.**
El proyecto del grupo, preferentemente el que van a usar como Trabajo Final
Integrador. Requiere que primero elaboren su propio mapa de deudas.

La Opción B es más trabajo al inicio y menos al final del cuatrimestre,
porque los TP siguientes se acumulan sobre el mismo repositorio.

---

## 3. Entregables

### 3.1 Repositorio

- Público, o privado con acceso para la cátedra.
- Historial real: **no se acepta un único commit inicial con todo adentro**.
- Sin credenciales versionadas. `config/` en `.gitignore` y un
  `config/database.example.php` con valores vacíos.

### 3.2 Refactor: mínimo 4 patrones

Cada patrón, **una rama y un Pull Request propios**:

```
feat/patron-strategy
feat/patron-factory
feat/patron-observer
feat/patron-facade
```

Al menos uno de los cuatro debe ser **estructural** (Adapter, Decorator o Facade).

Cada PR debe responder tres preguntas en su descripción:

1. **Qué deuda encontré** — archivo y línea, no una opinión general.
2. **Qué patrón apliqué y por qué ese** — qué otro consideré y por qué lo descarté.
3. **Qué consecuencia negativa tiene mi solución** — todo patrón tiene una.

### 3.3 `docs/DEUDA-TECNICA.md`

Una tabla con esta forma:

| Síntoma observado | Evidencia (archivo:línea) | Tipo de deuda | Patrón aplicado | Consecuencia asumida |
|---|---|---|---|---|
| El descuento está escrito en 5 archivos | `Order.php:112`, … | Diseño | Strategy | 3 clases nuevas por un `switch` de 10 líneas |

Mínimo **6 filas**, de las cuales al menos **2** deben ser deuda de proceso
(Unidad 1) y no de código.

### 3.4 `README.md`

Tiene que permitir que **otro grupo levante el proyecto sin preguntarles nada**.
Ese es el criterio de aprobación: se prueba con otro grupo.

### 3.5 Historial de commits

Convención obligatoria:

```
feat:     funcionalidad nueva
fix:      corrección de un error
refactor: cambia la estructura sin cambiar el comportamiento
docs:     documentación
```

```
❌ git commit -m "arreglo"
✅ git commit -m "refactor(pricing): reemplazar switch por PricingStrategy"
```

---

## 4. Flujo de trabajo obligatorio (Unidad 1)

```
main          ●───────────────●──────────────●
               \             / \            /
feat/strategy   ●───●───●───●   \          /
                                 \        /
feat/observer                     ●──●───●
```

Reglas:

1. **Nadie escribe directamente en `main`.**
2. Una rama por patrón, con nombre descriptivo.
3. Un PR por rama, con la descripción de las tres preguntas del punto 3.2.
4. **Cada PR revisado y aprobado por otro integrante**, nunca por el autor.
5. Los conflictos se resuelven en Git y se documenta cómo, no se pisan archivos.

La revisión cruzada no es burocracia: es el mecanismo por el que el
conocimiento deja de vivir en la cabeza de una sola persona.

---

## 5. Criterios de logro

Cada criterio se califica como **logrado / en proceso / no logrado**.

| Criterio | Se considera logrado cuando… |
|---|---|
| **Diagnóstico** | La deuda se identifica con evidencia concreta del código, no con opinión |
| **Aplicación** | El patrón resuelve el problema real; no está puesto para cumplir la consigna |
| **Justificación** | El grupo explica la consecuencia negativa de su propia solución |
| **Colaboración** | El historial muestra ramas, PR y revisiones cruzadas reales entre integrantes |
| **Reproducibilidad** | Otro grupo levanta el proyecto siguiendo solo el README |
| **Integración** | El trabajo cubre las dos capas: proceso y diseño |

Al ser una instancia formativa, un criterio **en proceso** se puede volver a
presentar corregido antes del TP siguiente.

---

## 6. Errores que invalidan la entrega

- Un solo commit con todo el proyecto.
- Todos los commits del mismo autor cuando el grupo tiene varios integrantes.
- Patrones aplicados sin que exista el problema que resuelven (sobreingeniería).
- Código viejo comentado "por las dudas" al lado del refactor.
- Credenciales versionadas.

---

## 7. Ejercicios guiados (Opción A)

Los ejercicios están marcados dentro de cada archivo del proyecto.

| # | Archivo | Consigna | Patrón |
|---|---|---|---|
| 1 | `Pricing/PriceCalculator.php` | Implementar `PrepaidStrategy` sin modificar `PriceCalculator` | Strategy |
| 2 | `Notifications/NotificationSender.php` | Agregar WhatsApp tocando un solo archivo además de la clase nueva | Factory |
| 3 | `Legacy/LegacyNotifier.php` | Eliminar el método agregado a la clase de terceros y la copia duplicada | Adapter |
| 4 | `Reports/ReportGenerator.php` | Reemplazar las banderas booleanas por decoradores encadenables | Decorator |
| 5 | `Events/OrderEvents.php` | Agregar `SmsObserver` sin tocar el sujeto | Observer |
| 6 | `Services/OrderService.php` | Dejar el método en menos de 10 líneas sin perder funcionalidad | Facade |
| 7 | `Controllers/OrderController.php` | Dejar `create()` sin SQL, sin reglas de negocio y sin `echo` | MVC + SRP |
| 8 | `views/orders.php` | Dejar la vista sin lógica y con la salida escapada | MVC |

Con 4 de los 8 alcanza para la entrega. Los 8 son el proyecto completo.

---

## 8. Pregunta de cierre

La misma con la que arrancó la unidad, ahora sobre el proyecto de ustedes:

> **¿Cuántos archivos tienen que tocar para agregar un tipo de paciente nuevo?**

Si al terminar el TP ese número bajó y pueden explicar por qué,
el trabajo está logrado.
