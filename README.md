# Sistema Contable Web – Portfolio

Sistema contable y de gestión desarrollado como **proyecto de portfolio**, enfocado en **lógica de negocio real**, **integridad contable** y **buenas prácticas** en aplicaciones web con **PHP y MySQL**.

El sistema cubre el circuito completo de un comercio: **ventas, compras, stock, facturación y contabilidad**, con generación automática de **asientos contables**, **Libro Diario** y **Libro Mayor**.

---

## Qué resuelve este sistema

- Centraliza la operación diaria (ventas y stock)
- Automatiza la contabilidad básica
- Mantiene trazabilidad entre operación y contabilidad
- Reduce errores manuales mediante validaciones y reglas de negocio

---

## Funcionalidades Principales

### Operación
- **Ventas**
  - Registro con detalle de productos
  - Cálculo automático de totales
  - Impacto directo en stock
- **Compras**
  - Registro de compras
  - Actualización automática de inventario
- **Stock**
  - Gestión de productos
  - Control de existencias y movimientos
- **Facturación**
  - Emisión y registro de comprobantes

### Contabilidad
- **Asientos contables automáticos**
  - Cada venta y compra genera su asiento correspondiente
  - Validación de balance (**Debe = Haber**)
- **Libro Diario**
  - Registro cronológico de asientos
  - Filtros por período
- **Libro Mayor**
  - Movimientos y saldos por cuenta
- **Plan de cuentas**
  - Configurable según el negocio

### Seguridad y Calidad
- **Sistema de login**
- **Roles de usuario** (administrador / operador)
- **Validaciones de datos y reglas de negocio**
- **Trazabilidad de operaciones** (usuario y fecha)

---

## Tecnologías Utilizadas

- **PHP** – lógica de negocio y backend
- **MySQL** – persistencia de datos
- **phpMyAdmin** – administración de base de datos
- **Bootstrap** – interfaz responsive

---

## Enfoque Técnico

Este proyecto prioriza:

- Modelado correcto de datos
- Separación entre lógica, datos e interfaz
- Reglas contables consistentes
- Automatización de procesos críticos
- Código claro y mantenible

No utiliza frameworks pesados, con el objetivo de demostrar dominio de **PHP puro**, **SQL** y **lógica de negocio**.

---

## Estructura del Proyecto (orientativa)

config/ -> configuración y conexión a base de datos
controllers/ -> lógica de negocio
models/ -> acceso a datos
views/ -> interfaz de usuario
database/ -> scripts SQL
public/ -> punto de entrada de la aplicación


---

## Flujo Contable

### Venta
1. Se registra la venta
2. Se descuenta stock
3. Se genera el asiento contable
4. Se registra en Libro Diario
5. Se refleja en Libro Mayor

### Compra
1. Se registra la compra
2. Se incrementa stock
3. Se genera el asiento contable
4. Se actualizan los libros contables

---

## Objetivo del Proyecto

Demostrar capacidad para diseñar e implementar un **sistema de gestión real**, integrando:

- Operación comercial
- Contabilidad básica correcta
- Persistencia de datos
- Validaciones y control

Orientado a pequeños y medianos comercios.

---

## Estado del Proyecto

Proyecto funcional, con mejoras en curso orientadas a:
- Reportes contables avanzados
- Exportación de información
- Auditoría y control

---
