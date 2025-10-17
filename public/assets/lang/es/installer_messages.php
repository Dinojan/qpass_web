<?php

return array (
  'title' => 'Instalador de la aplicación de pase de visitante',
  'next' => 'Siguiente paso',
  'back' => 'Anterior',
  'finish' => 'Instalar',
  'forms' => 
  array (
    'errorTitle' => 'Los siguientes errores ocurrieron:',
  ),
  'welcome' => 
  array (
    'templateTitle' => 'Bienvenido al instalador de la aplicación Visitor Pass',
    'title' => 'Sistema de gestión de pases de visitante',
    'message' => 'Asistente de instalación y configuración fácil del pase de visitante.',
    'next' => 'Comprobar requisitos',
  ),
  'requirements' => 
  array (
    'templateTitle' => 'Paso 1 | Requisitos del servidor',
    'title' => 'Requisitos del servidor',
    'next' => 'Comprobar permisos',
  ),
  'permissions' => 
  array (
    'templateTitle' => 'Paso 2 | Permisos',
    'title' => 'Permisos',
    'next' => 'Configurar entorno',
  ),
  'purchase-code' => 
  array (
    'templateTitle' => 'Paso 2 | Código de compra',
    'title' => 'Código de compra',
    'next' => 'Verificar tu código de compra',
    'form' => 
    array (
      'purchase_code_label' => 'Código de compra',
      'purchase_username_label' => 'Nombre de usuario de compra',
      'buttons' => 
      array (
        'verify' => 'Verificar código',
      ),
    ),
  ),
  'environment' => 
  array (
    'menu' => 
    array (
      'templateTitle' => 'Paso 3 | Configuración del entorno',
      'title' => 'Configuración del entorno',
      'desc' => 'Selecciona cómo deseas configurar el archivo <code>.env</code> de la aplicación.',
      'wizard-button' => 'Asistente de configuración',
      'classic-button' => 'Editor de texto clásico',
    ),
    'wizard' => 
    array (
      'templateTitle' => 'Paso 3 | Configuración del entorno | Asistente guiado',
      'title' => 'Asistente guiado para <code>.env</code>',
      'tabs' => 
      array (
        'environment' => 'Entorno',
        'database' => 'Base de datos',
        'application' => 'Aplicación',
      ),
      'form' => 
      array (
        'name_required' => 'Se requiere un nombre de entorno.',
        'app_name_label' => 'Nombre de la aplicación',
        'app_name_placeholder' => 'Nombre de la aplicación',
        'app_environment_label' => 'Entorno de la aplicación',
        'app_environment_label_local' => 'Local',
        'app_environment_label_developement' => 'Desarrollo',
        'app_environment_label_qa' => 'QA',
        'app_environment_label_production' => 'Producción',
        'app_environment_label_other' => 'Otro',
        'app_environment_placeholder_other' => 'Introduce tu entorno...',
        'app_debug_label' => 'Depuración de la aplicación',
        'app_debug_label_true' => 'Verdadero',
        'app_debug_label_false' => 'Falso',
        'app_log_level_label' => 'Nivel de registro de la aplicación',
        'app_log_level_label_debug' => 'depuración',
        'app_log_level_label_info' => 'información',
        'app_log_level_label_notice' => 'aviso',
        'app_log_level_label_warning' => 'advertencia',
        'app_log_level_label_error' => 'error',
        'app_log_level_label_critical' => 'crítico',
        'app_log_level_label_alert' => 'alerta',
        'app_log_level_label_emergency' => 'emergencia',
        'app_url_label' => 'URL de la aplicación',
        'app_url_placeholder' => 'URL de la aplicación',
        'db_connection_failed' => 'No se pudo conectar a la base de datos.',
        'db_connection_label' => 'Conexión de la base de datos',
        'db_connection_label_mysql' => 'mysql',
        'db_connection_label_sqlite' => 'sqlite',
        'db_connection_label_pgsql' => 'pgsql',
        'db_connection_label_sqlsrv' => 'sqlsrv',
        'db_host_label' => 'Host de la base de datos',
        'db_host_placeholder' => 'Host de la base de datos',
        'db_port_label' => 'Puerto de la base de datos',
        'db_port_placeholder' => 'Puerto de la base de datos',
        'db_name_label' => 'Nombre de la base de datos',
        'db_name_placeholder' => 'Nombre de la base de datos',
        'db_username_label' => 'Nombre de usuario de la base de datos',
        'db_username_placeholder' => 'Nombre de usuario de la base de datos',
        'db_password_label' => 'Contraseña de la base de datos',
        'db_password_placeholder' => 'Contraseña de la base de datos',
        'app_tabs' => 
        array (
          'more_info' => 'Más información',
          'broadcasting_title' => 'Difusión, caché, sesión y cola',
          'broadcasting_label' => 'Controlador de difusión',
          'broadcasting_placeholder' => 'Controlador de difusión',
          'cache_label' => 'Controlador de caché',
          'cache_placeholder' => 'Controlador de caché',
          'session_label' => 'Controlador de sesión',
          'session_placeholder' => 'Controlador de sesión',
          'queue_label' => 'Controlador de cola',
          'queue_placeholder' => 'Controlador de cola',
          'redis_label' => 'Controlador de Redis',
          'redis_host' => 'Host de Redis',
          'redis_password' => 'Contraseña de Redis',
          'redis_port' => 'Puerto de Redis',
          'mail_label' => 'Correo',
          'mail_driver_label' => 'Controlador de correo',
          'mail_driver_placeholder' => 'Controlador de correo',
          'mail_host_label' => 'Host de correo',
          'mail_host_placeholder' => 'Host de correo',
          'mail_port_label' => 'Puerto de correo',
          'mail_port_placeholder' => 'Puerto de correo',
          'mail_username_label' => 'Nombre de usuario de correo',
          'mail_username_placeholder' => 'Nombre de usuario de correo',
          'mail_password_label' => 'Contraseña de correo',
          'mail_password_placeholder' => 'Contraseña de correo',
          'mail_encryption_label' => 'Cifrado de correo',
          'mail_encryption_placeholder' => 'Cifrado de correo',
          'pusher_label' => 'Pusher',
          'pusher_app_id_label' => 'ID de aplicación de Pusher',
          'pusher_app_id_palceholder' => 'ID de aplicación de Pusher',
          'pusher_app_key_label' => 'Clave de aplicación de Pusher',
          'pusher_app_key_palceholder' => 'Clave de aplicación de Pusher',
          'pusher_app_secret_label' => 'Secreto de aplicación de Pusher',
          'pusher_app_secret_palceholder' => 'Secreto de aplicación de Pusher',
        ),
        'buttons' => 
        array (
          'setup_database' => 'Configurar base de datos',
          'setup_application' => 'Configurar aplicación',
          'install' => 'Instalar',
        ),
      ),
    ),
    'classic' => 
    array (
      'templateTitle' => 'Paso 3 | Configuración del entorno | Editor clásico',
      'title' => 'Editor clásico de entorno',
      'save' => 'Guardar .env',
      'back' => 'Usar el asistente de formulario',
      'install' => 'Guardar e instalar',
    ),
    'success' => 'La configuración de tu archivo .env se ha guardado.',
    'errors' => 'No se pudo guardar el archivo .env, por favor créalo manualmente.',
  ),
  'install' => 'Instalar',
  'installed' => 
  array (
    'success_log_message' => 'Laravel Installer instalado correctamente en ',
  ),
  'final' => 
  array (
    'title' => 'Instalación finalizada',
    'templateTitle' => 'Instalación finalizada',
    'finished' => 'La aplicación se ha instalado correctamente.',
    'migration' => 'Migración y salida de la consola de seed:',
    'console' => 'Salida de la consola de la aplicación:',
    'log' => 'Entrada del registro de instalación:',
    'env' => 'Archivo final .env:',
    'exit' => 'Haz clic aquí para salir',
  ),
  'updater' => 
  array (
    'title'   => 'Actualizador de Laravel',
    'welcome' => 
    array (
      'title'   => 'Bienvenido al actualizador',
      'message' => 'Bienvenido al asistente de actualización.',
    ),
    'overview' => 
    array (
      'title'           => 'Resumen',
      'message'         => 'Hay 1 actualización.|Hay :number actualizaciones.',
      'install_updates' => 'Instalar actualizaciones',
    ),
    'final' => 
    array (
      'title'    => 'Finalizado',
      'finished' => 'La base de datos de la aplicación se ha actualizado correctamente.',
      'exit'     => 'Haz clic aquí para salir',
    ),
    'log' => 
    array (
      'success_message' => 'Laravel Installer actualizado correctamente en ',
    ),
  ),
);

