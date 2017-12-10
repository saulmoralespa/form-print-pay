=== Form print pay ===
Contributors: saulmoralespa
Tags: form, furmulario, custom, personalizado, shortocode, pdf, paypal, imprimir, generate, generar, name, email, text, texatara, select, hidden, tel, nombre, correo electrónico, texto, comentarios, seleccionable, oculto, teléfono
Requires at least: 4.9.1
Tested up to: 4.9.1
Requires PHP: 5.6.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Imprime un pdf con los valores del fomulario.

== Description ==

Form print pay permite crear un formulario completamente personalizado muy parecido conctact 7.
Solo con la opción de inprimir los valores en un pdf e integrado con medio de pago paypal.

Recomendado para sitios que ofrecen algún tipo de asesoria y requiere enviar comprobante.


== Installation ==


1. Descarga el plugin
2. Ingresa al administrador de tu wordPress.
3. Ingresa a Plugins / Añadir-Nuevo / Subir-Plugin.
4. Busca el plugin descargado en tu equipo y súbelo como cualquier otro archivo.
5. Después de instalar el .zip lo puedes ver en la lista de plugins instalados , puedes activarlo o desactivarlo.
6. Para configurar el plugin debes ir a Form print pay
7. Para crear un formulario debe ir a shortcodes form y add new


== Frequently Asked Questions ==

= ¿ He creado guardado cambios del formulario, como lo muestro ? =

Asegúrese de llenar el campo "Texto documento impreso" y salve cambios por ultimo  dando click en publicar o Actualizar.
Ejemplo el nombre del formulario será parecido a: [fpp_form_print_pay id="3862"] este es igual al shortcode que debe inserta en una entrada o página.

= ¿ Es requerido que use un certificado ssl ? =

No.Pero es recomendable que lo considere usar si desea brindar seguridad al usuario en cuanto a la protección de sus datos

= ¿ Como pruebo su funcionamiento ? =

Debe ir a la configuración del plugin / paypal y establecer credenciales de pruebas, para esto debe haber creado una cuenta de paypal de pruebas https://developer.paypal.com/developer/accounts/

= ¿ Qué sucede cuando hay ordenes pendientes de paypal ? =

Si el estado de la orden es pendiente no se preocupe, el plugin actualiza las ordenes pendientes, pero si lo desea lo puede hacer manual en el menú "ordenes pendientes".

= ¿ Qué más debo tener en cuenta, que no me hayas dicho ? =

1.Los pdfs que se crean no se eliminan y los puedes encontrar en la instalación de wordpress directorio /uploads/form-print-pay/pdfs
2. Actualmente el medio de pago es paypal y es compatible con las monedas Euro, Dolar estadounidense y Peso mexicano, si desea añadir monedas u otro medio de pago pongase en contacto info@saulmoralespa.com
3. El contenido de los emails que reciben tanto el usuario y el adminstrador son editables pero no como el texto impreso del documento.
4. El estilo del formulario es básico puede mejor su aspecto con algunas reglas css.

== Screenshots ==

1. Ordenes pendientes a screenshot-1.png
2. Configuración medio de pago paypal corresponde a screenshot-2.png
3. Configuración email usuario y admin corresponde a screenshot-3.png
4. Configuración impreso pdf corresponde a screenshot-4.png
5. Creación de formulario a screenshot-5.png

== Changelog ==

= 1.0.0 =
* Initial stable release
