# Concurso de Materiales

Sistema web para gestionar **concursos de materiales** con **login** y control por **roles**, donde:
- el **Administrador** crea concursos y publica el material ganador,
- los **Usuarios** proponen materiales para concursar,
- los **Supervisores** votan las propuestas.

> Proyecto orientado a portfolio, enfocado en **lógica de negocio**, **roles y permisos** y un flujo completo de principio a fin.

---

## Funcionalidades

### Autenticación y Roles
- Login con sesión
- Roles:
  - **ADMIN**: gestiona concursos y publica ganador
  - **SUPERVISOR**: vota propuestas
  - **USUARIO**: propone materiales

### Concursos
- Creación y administración de concursos (ADMIN)
- Estado del concurso (ej.: abierto / cerrado / publicado)
- Publicación del ganador al finalizar (ADMIN)

### Propuestas y Votación
- Usuarios cargan propuestas de materiales por concurso (USUARIO)
- Supervisores votan propuestas (SUPERVISOR)
- Cálculo de propuesta ganadora por cantidad de votos

---

## Reglas de Negocio (importantes)

- Un **USUARIO** solo puede **proponer** materiales (no votar ni publicar ganador).
- Un **SUPERVISOR** solo puede **votar** (no crear concursos).
- Un concurso **cerrado** no acepta nuevas propuestas ni votos.
- El **ADMIN** publica el ganador **una vez finalizado** el concurso.
- La propuesta ganadora es la que obtiene **más votos** (en empate: definir criterio en el código o documentación).

---

## Tecnologías

- **Java** (proyecto estructurado en paquetes como `controller`, `entity`, `repository`, `config`)  
- Arquitectura por capas: controller → business/service → repository → entity

> El repositorio muestra estructura típica de aplicación Java con capas (`controller/`, `repository/`, `entity/`, etc.).  

---

## Estructura del Proyecto (orientativa)

- `config/` → configuración de la app  
- `controller/` → endpoints / controladores  
- `business/` → lógica de negocio / servicios  
- `entity/` → entidades del dominio  
- `repository/` → acceso a datos (persistencia)  
- `uploadingFiles/` → manejo de archivos (si aplica)
---

## Casos de Uso

### Administrador
1. Inicia sesión
2. Crea un concurso
3. Monitorea propuestas y votos
4. Cierra el concurso
5. Publica el material ganador

### Usuario
1. Inicia sesión
2. Selecciona un concurso abierto
3. Carga una propuesta de material

### Supervisor
1. Inicia sesión
2. Revisa propuestas
3. Vota las propuestas que considere

---

## Objetivo del Proyecto

Demostrar capacidad para:
- modelar un sistema con **roles y permisos reales**
- implementar un **flujo completo** (crear → proponer → votar → cerrar → publicar)
- organizar un backend por capas con responsabilidades claras

---

## Mejoras posibles (Roadmap)

- Criterio de desempate configurable
- Auditoría (quién votó, cuándo, historial de cambios)
- Panel de métricas por concurso
- Restricciones adicionales: 1 voto por supervisor por propuesta, o 1 voto total por concurso, etc.
- Tests de negocio (unitarios) sobre cálculo de ganador y reglas de estado

---

