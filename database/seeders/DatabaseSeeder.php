<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\MaintenanceTask;
use App\Models\MonthlyReport;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $clientsData = [
            ['Clínica Dental Llum', 'Marta Soler', 'marta@clinicallum.test', '+34 960 120 401', 'Valencia', 'active', 'Prioridad: disponibilidad y formularios de cita.'],
            ['Estudio Norte Interiorismo', 'Álvaro Núñez', 'alvaro@estudionorte.test', '+34 965 230 118', 'Alicante', 'active', 'Revisar la galería de proyectos y optimizar imágenes cada mes.'],
            ['Valencia Training Club', 'Carla Ferrer', 'carla@vtclub.test', '+34 961 440 602', 'Valencia', 'active', 'Picos de tráfico en campañas de inscripción.'],
            ['Atelier Forma', 'Inés Vidal', 'ines@atelierforma.test', '+34 964 310 225', 'Castellón', 'active', 'Catálogo editorial actualizado por temporadas.'],
            ['NovaLegal Consultores', 'Diego Martí', 'diego@novalegal.test', '+34 963 512 087', 'Valencia', 'active', 'Contenido legal; no almacenar documentación sensible en notas.'],
            ['Casa Madera Studio', 'Sofía Ramos', 'sofia@casamadera.test', '+34 972 660 420', 'Girona', 'active', 'Tienda pequeña con sincronización de stock manual.'],
            ['Brava Events', 'Nuria Costa', 'nuria@bravaevents.test', '+34 937 202 919', 'Barcelona', 'inactive', 'Cuenta pausada tras finalizar la temporada de eventos.'],
            ['TecnoRiba Solutions', 'Héctor Riba', 'hector@tecnoriba.test', '+34 960 784 133', 'Paterna', 'active', 'Aplicación corporativa con despliegues coordinados.'],
        ];

        $clients = collect($clientsData)->map(fn ($data) => Client::create([
            'company_name' => $data[0], 'contact_name' => $data[1], 'email' => $data[2],
            'phone' => $data[3], 'city' => $data[4], 'status' => $data[5], 'notes' => $data[6],
        ]));

        $password = Hash::make('password');
        User::create(['name' => 'Luca Mazmishvili', 'email' => 'admin@webops.test', 'password' => $password, 'role' => 'admin', 'email_verified_at' => now()]);
        $tech1 = User::create(['name' => 'Elena Torres', 'email' => 'tech1@webops.test', 'password' => $password, 'role' => 'technician', 'email_verified_at' => now()]);
        $tech2 = User::create(['name' => 'Marc Gil', 'email' => 'tech2@webops.test', 'password' => $password, 'role' => 'technician', 'email_verified_at' => now()]);
        foreach ([0, 1, 2] as $index) {
            User::create([
                'name' => $clients[$index]->contact_name,
                'email' => 'cliente'.($index + 1).'@webops.test',
                'password' => $password,
                'role' => 'client',
                'client_id' => $clients[$index]->id,
                'email_verified_at' => now(),
            ]);
        }

        $websiteData = [
            [0, 'Clínica Llum', 'https://clinica-llum.test', 'WordPress', 'Cloudia Host', 18, 45, 'active', 'premium', 'stable'],
            [0, 'Portal de citas Llum', 'https://citas.clinica-llum.test', 'Laravel', 'Nube Levante', 18, 12, 'expiring', 'premium', 'review'],
            [1, 'Estudio Norte', 'https://estudio-norte.test', 'WordPress', 'Atlas Hosting', 75, 20, 'active', 'standard', 'stable'],
            [1, 'Archivo de proyectos', 'https://archivo.estudio-norte.test', 'Static HTML', null, 75, null, 'active', 'basic', 'stable'],
            [2, 'Valencia Training Club', 'https://valencia-training.test', 'PrestaShop', 'Iberia Cloud', 9, 28, 'expiring', 'premium', 'incident'],
            [2, 'Área de socios VTC', 'https://socios.valencia-training.test', 'PHP custom', 'Iberia Cloud', 9, 28, 'active', 'standard', 'review'],
            [3, 'Atelier Forma', 'https://atelier-forma.test', 'WordPress', 'Pixel Server', 110, 110, 'active', 'standard', 'stable'],
            [4, 'NovaLegal', 'https://novalegal.test', 'Laravel', 'Nube Segura', 31, 31, 'active', 'premium', 'stable'],
            [5, 'Casa Madera Shop', 'https://casa-madera.test', 'PrestaShop', 'Comercio Host', -2, 5, 'expired', 'premium', 'critical'],
            [6, 'Brava Events', 'https://brava-events.test', 'WordPress', 'Costa Hosting', 150, 150, 'unknown', 'none', 'review'],
            [7, 'TecnoRiba Corporate', 'https://tecnoriba.test', 'Laravel', 'DevCloud ES', 62, 14, 'active', 'standard', 'stable'],
            [7, 'Documentación TecnoRiba', 'https://docs.tecnoriba.test', 'Other', 'DevCloud ES', 62, 14, 'active', 'basic', 'stable'],
        ];

        $websites = collect($websiteData)->map(fn ($data) => Website::create([
            'client_id' => $clients[$data[0]]->id, 'name' => $data[1], 'url' => $data[2], 'technology' => $data[3],
            'hosting_provider' => $data[4], 'domain_expires_at' => today()->addDays($data[5]),
            'hosting_expires_at' => $data[6] === null ? null : today()->addDays($data[6]), 'ssl_status' => $data[7],
            'maintenance_plan' => $data[8], 'status' => $data[9], 'notes' => 'Entorno de demostración. No guardar credenciales reales en este campo.',
        ]));

        $ticketTitles = [
            'Formulario de contacto no entrega correos', 'Actualizar banner de campaña', 'Revisar lentitud en la portada',
            'Error al finalizar una reserva', 'Imagen principal recortada en móvil', 'Preparar redirecciones de URLs antiguas',
            'Comprobar aviso de certificado SSL', 'Productos sin miniatura en categoría', 'Revisión de permisos de usuarios',
            'Texto legal pendiente de publicar', 'Pico de errores 500 en administración', 'Optimizar Core Web Vitals',
            'El buscador no encuentra términos parciales', 'Actualizar horario en la web', 'Configurar copia externa semanal',
            'Problema con cupón promocional', 'Enlace roto en el pie de página', 'Importación de catálogo incompleta',
            'Revisar compatibilidad con PHP actual', 'Alta de nueva sección de servicios', 'Spam en formulario de presupuesto',
            'Corregir metadatos de página principal', 'Sesión expira antes de tiempo', 'Revisar consumo de almacenamiento',
            'Incidencia de acceso al panel',
        ];
        $statuses = ['open', 'in_progress', 'waiting_client', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        foreach ($ticketTitles as $i => $title) {
            $website = $websites[$i % $websites->count()];
            $status = $statuses[$i % count($statuses)];
            Ticket::create([
                'client_id' => $website->client_id, 'website_id' => $website->id,
                'assigned_to' => $i % 6 === 0 ? null : ($i % 2 === 0 ? $tech1->id : $tech2->id),
                'title' => $title,
                'description' => 'Solicitud registrada con contexto suficiente para reproducirla, valorar el impacto y comunicar el avance al cliente.',
                'priority' => $priorities[$i % count($priorities)], 'status' => $status,
                'due_date' => today()->addDays(($i % 13) - 4),
                'resolved_at' => in_array($status, ['resolved', 'closed'], true) ? now()->subDays($i % 8) : null,
                'created_at' => now()->subDays(25 - $i), 'updated_at' => now()->subDays(max(0, 12 - $i)),
            ]);
        }

        $taskNames = ['Verificar copia de seguridad', 'Aplicar actualizaciones seguras', 'Escaneo básico de seguridad', 'Optimizar caché y recursos', 'Revisar contenidos publicados', 'Auditar títulos y metadescripciones', 'Comprobar formularios y conversiones'];
        $categories = ['backups', 'updates', 'security', 'performance', 'content', 'seo', 'other'];
        $taskStatuses = ['pending', 'in_progress', 'completed', 'blocked'];
        for ($i = 0; $i < 35; $i++) {
            $status = $taskStatuses[$i % count($taskStatuses)];
            MaintenanceTask::create([
                'website_id' => $websites[$i % $websites->count()]->id,
                'assigned_to' => $i % 2 === 0 ? $tech1->id : $tech2->id,
                'title' => $taskNames[$i % count($taskNames)],
                'description' => 'Checklist operativo documentado para dejar constancia de la revisión y de cualquier acción posterior.',
                'category' => $categories[$i % count($categories)],
                'priority' => ['low', 'medium', 'high'][$i % 3], 'status' => $status,
                'scheduled_at' => now()->addDays(($i % 28) - 9)->setTime(10 + ($i % 6), 0),
                'completed_at' => $status === 'completed' ? now()->subDays($i % 5) : null,
            ]);
        }

        foreach (range(0, 5) as $i) {
            MonthlyReport::create([
                'client_id' => $clients[$i]->id, 'month' => now()->subMonth()->month, 'year' => now()->subMonth()->year,
                'summary' => 'El servicio se mantuvo operativo durante el periodo. Se atendieron solicitudes, revisiones preventivas y mejoras priorizadas con el cliente.',
                'completed_tasks_count' => 3 + $i, 'resolved_tickets_count' => 2 + ($i % 3), 'pending_tickets_count' => $i % 3,
                'recommendations' => 'Mantener las actualizaciones planificadas, revisar analítica y reservar una ventana para optimización de rendimiento el próximo mes.',
                'general_status' => ['good', 'good', 'attention', 'good', 'attention', 'critical'][$i],
            ]);
        }
    }
}
