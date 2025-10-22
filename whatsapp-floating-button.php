<?php
/*
Plugin Name: Botón Flotante de WhatsApp
Plugin URI: https://webmoderna.com.ar
Description: Muestra un botón flotante de WhatsApp configurable desde el panel de administración.
Version: 1.0
Author: Cr. Edgardo Raúl Galletto
Author URI: https://webmoderna.com.ar
License: GPL2
Text Domain: whatsapp-floating-button
Domain Path: /languages
*/

// Bloquear acceso directo
if ( !defined('ABSPATH') ) exit;

// Cargar traducciones
add_action('plugins_loaded', function() {
    load_plugin_textdomain('whatsapp-floating-button', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Registrar menú en el admin (visible para editores o superior)
add_action('admin_menu', function() {
    add_menu_page(
        __('Botón WhatsApp', 'whatsapp-floating-button'),
        __('Botón WhatsApp', 'whatsapp-floating-button'),
        'edit_pages', // Rol mínimo: Editor
        'whatsapp-floating-button',
        'wfb_settings_page',
        'dashicons-whatsapp',
        81
    );
});

// Registrar ajustes
add_action('admin_init', function() {
    register_setting('wfb_settings_group', 'wfb_phone');
    register_setting('wfb_settings_group', 'wfb_position_vertical');
    register_setting('wfb_settings_group', 'wfb_position_horizontal');
    register_setting('wfb_settings_group', 'wfb_offset_vertical');
    register_setting('wfb_settings_group', 'wfb_offset_horizontal');
});

// Página de configuración
function wfb_settings_page() { ?>
    <div class="wrap">
        <h1><?php _e('Configuración del Botón Flotante de WhatsApp', 'whatsapp-floating-button'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('wfb_settings_group'); ?>
            <?php do_settings_sections('wfb_settings_group'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Número de WhatsApp (con código de país)', 'whatsapp-floating-button'); ?></th>
                    <td>
                        <input type="text" name="wfb_phone" value="<?php echo esc_attr(get_option('wfb_phone')); ?>" placeholder="5491123456789" class="regular-text" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Posición Vertical', 'whatsapp-floating-button'); ?></th>
                    <td>
                        <select name="wfb_position_vertical">
                            <option value="bottom" <?php selected(get_option('wfb_position_vertical'), 'bottom'); ?>><?php _e('Abajo', 'whatsapp-floating-button'); ?></option>
                            <option value="top" <?php selected(get_option('wfb_position_vertical'), 'top'); ?>><?php _e('Arriba', 'whatsapp-floating-button'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Posición Horizontal', 'whatsapp-floating-button'); ?></th>
                    <td>
                        <select name="wfb_position_horizontal">
                            <option value="right" <?php selected(get_option('wfb_position_horizontal'), 'right'); ?>><?php _e('Derecha', 'whatsapp-floating-button'); ?></option>
                            <option value="left" <?php selected(get_option('wfb_position_horizontal'), 'left'); ?>><?php _e('Izquierda', 'whatsapp-floating-button'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Distancia Vertical (px)', 'whatsapp-floating-button'); ?></th>
                    <td><input type="number" name="wfb_offset_vertical" value="<?php echo esc_attr(get_option('wfb_offset_vertical', 20)); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Distancia Horizontal (px)', 'whatsapp-floating-button'); ?></th>
                    <td><input type="number" name="wfb_offset_horizontal" value="<?php echo esc_attr(get_option('wfb_offset_horizontal', 20)); ?>" /></td>
                </tr>
            </table>

            <?php submit_button(__('Guardar configuración', 'whatsapp-floating-button')); ?>
        </form>
    </div>
<?php }

// Mostrar el botón en el frontend
add_action('wp_footer', function() {
    $phone = get_option('wfb_phone');
    if (!$phone) return;

    $pos_v = get_option('wfb_position_vertical', 'bottom');
    $pos_h = get_option('wfb_position_horizontal', 'right');
    $off_v = intval(get_option('wfb_offset_vertical', 20));
    $off_h = intval(get_option('wfb_offset_horizontal', 20));

    $style = "{$pos_v}:{$off_v}px;{$pos_h}:{$off_h}px;";
    $whatsapp_url = "https://wa.me/{$phone}";
    ?>
    <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener" class="wfb-button" style="<?php echo esc_attr($style); ?>" title="<?php esc_attr_e('Enviar mensaje por WhatsApp', 'whatsapp-floating-button'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 24 24">
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.12.554 4.14 1.6 5.93L0 24l6.247-1.568A11.932 11.932 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm.002 21.65a9.6 9.6 0 01-4.9-1.35l-.35-.21-3.71.93.99-3.61-.24-.37a9.66 9.66 0 01-1.49-5.1c0-5.31 4.32-9.63 9.63-9.63 2.57 0 4.99 1 6.82 2.83A9.62 9.62 0 0121.63 12c0 5.31-4.32 9.65-9.63 9.65zM17.11 14.1c-.29-.15-1.69-.83-1.95-.93-.26-.1-.45-.15-.64.15s-.74.93-.9 1.12-.33.22-.62.07a7.84 7.84 0 01-2.3-1.41 8.64 8.64 0 01-1.6-2c-.17-.29 0-.45.13-.6.13-.13.29-.33.43-.49.14-.16.19-.27.29-.45.1-.18.05-.34-.02-.49-.07-.15-.64-1.55-.88-2.13-.23-.55-.47-.47-.64-.48h-.55c-.18 0-.49.07-.75.34s-.98.96-.98 2.34 1.01 2.7 1.15 2.88c.14.18 1.99 3.04 4.8 4.26.67.29 1.19.46 1.6.59.67.21 1.28.18 1.76.11.54-.08 1.69-.69 1.93-1.36.24-.67.24-1.24.17-1.36-.07-.12-.26-.19-.55-.33z"/>
        </svg>
    </a>
    <style>
        .wfb-button {
            position: fixed;
            background-color: rgba(37, 211, 102, 0.5);
            color: white;
            font-size: 40px;
            width: 45px;
            height: 45px;
            line-height: 45px;
            text-align: center;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            z-index: 9999;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }
        .wfb-button:hover {
            background-color: rgba(37, 211, 102, 1);
        }
        @media all and (max-width: 740px) {
            .wfb-button {
                bottom: 70px !important;
            }
        }
    </style>
    <?php
});
