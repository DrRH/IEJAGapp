<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === CREAR PERMISOS ===
        $permissions = [
            // Académico
            'view_all_academico',
            'view_area_academico',

            // Casos de Convivencia
            'view_all_casos_convivencia',
            'view_assigned_casos_convivencia',
            'view_own_casos_convivencia',
            'view_student_casos_convivencia',
            'view_self_status_casos_convivencia',
            'create_casos_convivencia',
            'update_casos_convivencia',
            'delete_casos_convivencia',
            'assign_casos_convivencia',
            'reassign_casos_convivencia',
            'derive_casos_convivencia',
            'escalate_casos_convivencia',
            'approve_casos_convivencia',
            'close_casos_convivencia',
            'reopen_casos_convivencia',
            'archive_casos_convivencia',

            // Atención Psicosocial
            'view_all_atencion_psicosocial',
            'view_assigned_atencion_psicosocial',
            'create_atencion_psicosocial',
            'update_atencion_psicosocial',
            'derive_atencion_psicosocial',
            'close_atencion_psicosocial',

            // Actas
            'view_all_actas',
            'view_assigned_actas',
            'view_own_actas',
            'create_actas',
            'update_actas',
            'delete_actas',
            'sign_actas',
            'close_actas',
            'publish_actas',

            // Comités
            'view_all_comites',
            'create_comites',
            'schedule_comites',
            'convene_comites',
            'close_session_comites',

            // Seguimiento Docente
            'view_all_seguimiento_docente',
            'view_own_seguimiento_docente',
            'create_seguimiento_docente',
            'update_seguimiento_docente',

            // Reportes
            'view_all_reportes',
            'view_area_reportes',
            'view_own_reportes',
            'create_reportes',
            'export_reportes',

            // Usuarios
            'view_all_usuarios',
            'create_usuarios',
            'update_usuarios',
            'delete_usuarios',

            // Configuración
            'view_all_configuracion',
            'update_configuracion',

            // Auditoría
            'view_all_auditoria',
            'export_logs_auditoria',

            // Integraciones Google
            'view_all_integraciones_google',
            'view_area_integraciones_google',
            'view_own_integraciones_google',
            'configure_integraciones_google',
            'sync_drive',
            'sync_calendar',
            'sync_sheets',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // === CREAR ROLES Y ASIGNAR PERMISOS ===

        // RECTOR
        $rector = Role::firstOrCreate(['name' => 'rector']);
        $rector->syncPermissions(Permission::all()); // Acceso total

        // COORDINACIÓN ACADÉMICA
        $coordAcademica = Role::firstOrCreate(['name' => 'coordinacion_academica']);
        $coordAcademica->syncPermissions([
            // Académico
            'view_all_academico',
            'view_area_academico',
            // Convivencia (solo Tipo I)
            'view_all_casos_convivencia',
            'create_casos_convivencia',
            'update_casos_convivencia',
            'assign_casos_convivencia',
            'derive_casos_convivencia',
            'close_casos_convivencia',
            // Actas
            'view_all_actas',
            'create_actas',
            'update_actas',
            'sign_actas',
            'close_actas',
            'publish_actas',
            // Comités
            'view_all_comites',
            'create_comites',
            'schedule_comites',
            'convene_comites',
            'close_session_comites',
            // Seguimiento Docente
            'view_all_seguimiento_docente',
            'create_seguimiento_docente',
            'update_seguimiento_docente',
            // Reportes
            'view_area_reportes',
            'create_reportes',
            'export_reportes',
            // Integraciones
            'view_area_integraciones_google',
            'sync_drive',
            'sync_calendar',
            'sync_sheets',
        ]);

        // COORDINACIÓN DE CONVIVENCIA
        $coordConvivencia = Role::firstOrCreate(['name' => 'coordinacion_convivencia']);
        $coordConvivencia->syncPermissions([
            // Convivencia (Todos los tipos)
            'view_all_casos_convivencia',
            'create_casos_convivencia',
            'update_casos_convivencia',
            'delete_casos_convivencia',
            'assign_casos_convivencia',
            'reassign_casos_convivencia',
            'derive_casos_convivencia',
            'escalate_casos_convivencia',
            'approve_casos_convivencia',
            'close_casos_convivencia',
            'reopen_casos_convivencia',
            // Atención Psicosocial
            'view_all_atencion_psicosocial',
            'create_atencion_psicosocial',
            'update_atencion_psicosocial',
            'derive_atencion_psicosocial',
            'close_atencion_psicosocial',
            // Actas
            'view_all_actas',
            'create_actas',
            'update_actas',
            'sign_actas',
            'close_actas',
            'publish_actas',
            // Comités
            'view_all_comites',
            'create_comites',
            'schedule_comites',
            'convene_comites',
            'close_session_comites',
            // Reportes
            'view_area_reportes',
            'create_reportes',
            'export_reportes',
            // Integraciones
            'view_area_integraciones_google',
            'sync_drive',
            'sync_calendar',
            'sync_sheets',
        ]);

        // PSICÓLOGO
        $psicologo = Role::firstOrCreate(['name' => 'psicologo']);
        $psicologo->syncPermissions([
            'view_all_casos_convivencia',
            'view_assigned_casos_convivencia',
            'create_casos_convivencia',
            'update_casos_convivencia',
            'derive_casos_convivencia',
            'close_casos_convivencia',
            'view_all_atencion_psicosocial',
            'view_assigned_atencion_psicosocial',
            'create_atencion_psicosocial',
            'update_atencion_psicosocial',
            'derive_atencion_psicosocial',
            'close_atencion_psicosocial',
            'view_assigned_actas',
            'create_actas',
            'update_actas',
            'sign_actas',
            'view_own_reportes',
            'export_reportes',
            'view_own_integraciones_google',
            'sync_calendar',
        ]);

        // ORIENTADOR
        $orientador = Role::firstOrCreate(['name' => 'orientador']);
        $orientador->syncPermissions([
            'view_all_casos_convivencia',
            'view_assigned_casos_convivencia',
            'create_casos_convivencia',
            'update_casos_convivencia',
            'derive_casos_convivencia',
            'close_casos_convivencia',
            'view_assigned_atencion_psicosocial',
            'create_atencion_psicosocial',
            'update_atencion_psicosocial',
            'derive_atencion_psicosocial',
            'view_assigned_actas',
            'create_actas',
            'update_actas',
            'sign_actas',
            'view_own_reportes',
            'export_reportes',
            'view_own_integraciones_google',
            'sync_calendar',
        ]);

        // DOCENTE
        $docente = Role::firstOrCreate(['name' => 'docente']);
        $docente->syncPermissions([
            'view_own_casos_convivencia',
            'create_casos_convivencia',
            'view_own_actas',
            'sign_actas',
            'view_own_seguimiento_docente',
            'view_own_reportes',
            'export_reportes',
            'view_own_integraciones_google',
            'sync_calendar',
        ]);

        // ACUDIENTE
        $acudiente = Role::firstOrCreate(['name' => 'acudiente']);
        $acudiente->syncPermissions([
            'view_student_casos_convivencia',
        ]);

        // ESTUDIANTE
        $estudiante = Role::firstOrCreate(['name' => 'estudiante']);
        $estudiante->syncPermissions([
            'view_self_status_casos_convivencia',
        ]);

        // ADMINISTRATIVO TI
        $administrativo = Role::firstOrCreate(['name' => 'administrativo']);
        $administrativo->syncPermissions([
            'view_all_usuarios',
            'create_usuarios',
            'update_usuarios',
            'view_all_configuracion',
            'update_configuracion',
            'view_all_integraciones_google',
            'configure_integraciones_google',
            'sync_drive',
            'sync_calendar',
            'sync_sheets',
            'view_all_auditoria',
            'export_logs_auditoria',
        ]);

        $this->command->info('✅ Roles y permisos creados exitosamente.');
        $this->command->info('📋 Roles creados: ' . Role::count());
        $this->command->info('🔐 Permisos creados: ' . Permission::count());
    }
}
