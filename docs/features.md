# Funcionalidades de Mazmi WebOps Desk

Este documento resume el comportamiento implementado. Describe el código actual y separa las mejoras futuras para no presentar funcionalidades simuladas como terminadas.

## Autenticación y roles

El acceso usa Laravel Breeze con sesiones, protección CSRF, contraseñas hasheadas y regeneración de sesión.

- **Administrador:** consulta y gestiona clientes, webs, tickets, mantenimiento y reportes. También asigna trabajo a técnicos.
- **Técnico:** consulta clientes y webs; su bandeja operativa muestra los tickets y tareas que tiene asignados. Puede actualizar sus estados, pero no eliminar datos críticos ni reasignar el trabajo.
- **Cliente:** consulta únicamente los registros vinculados a su empresa. Puede abrir tickets, pero no administrar clientes, webs o reportes.

Las consultas se acotan por usuario y las policies vuelven a comprobar cada recurso cuando se accede directamente mediante una URL.

## Dashboard

Las métricas se calculan con consultas reales:

- clientes activos y webs gestionadas;
- tickets abiertos y urgentes;
- tareas pendientes y atrasadas;
- dominios y hostings que vencen en 30 días;
- tickets recientes y próximos mantenimientos;
- distribución por estado y tecnología.

El administrador ve el escenario completo, el técnico ve su carga asignada y el cliente recibe datos vinculados a su empresa.

## Clientes

- Búsqueda por empresa, contacto o ciudad.
- Filtro por estado.
- Alta, edición y detalle agregado.
- Webs, tickets recientes y reportes dentro de la ficha.
- Borrado bloqueado si existen relaciones; se recomienda marcar el cliente como inactivo.

## Webs

- Filtros por tecnología, salud, plan y vencimiento próximo.
- Registro de hosting, SSL, dominio, fechas de renovación y notas.
- Detalle con tickets y tareas relacionados.
- Borrado bloqueado mientras tenga trabajo asociado.

## Tickets

- Búsqueda y filtros por estado, prioridad, cliente y técnico.
- Relación opcional con una web y asignación a un técnico.
- Cambio rápido de estado y registro automático de `resolved_at`.
- Los clientes crean tickets en estado abierto y no pueden autoasignarlos.
- Solo el administrador elimina tickets.

## Mantenimiento

- Categorías para backups, actualizaciones, seguridad, rendimiento, contenido, SEO y otros trabajos.
- Filtros de estado, prioridad, categoría, web y agenda.
- Vistas de tareas atrasadas y próximas.
- Acción de completado con registro de `completed_at`.
- El técnico solo modifica tareas asignadas.

## Reportes mensuales

- Listado filtrable por cliente, periodo y estado general.
- Un único reporte por cliente, mes y año.
- Resumen, indicadores y recomendaciones.
- Vista preparada para compartir o imprimir desde el navegador.

La exportación PDF no está implementada y figura como mejora futura.

## Experiencia de uso

- Sidebar en escritorio y navegación adaptada a móvil.
- Tablas convertidas en cards en pantallas pequeñas.
- Formularios divididos en secciones.
- Badges semánticos, mensajes flash, estados vacíos y confirmación antes de borrar.
- Filtros persistentes mediante parámetros de consulta.

## Datos demo

El seeder genera 6 usuarios, 8 clientes, 12 webs, 25 tickets, 35 tareas y 6 reportes. Todo el contenido es ficticio y no contiene credenciales reales.
