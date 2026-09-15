<?php
/**
 * Gamilea login and registration.
 *
 * Presentation only: every WooCommerce action, nonce and field name is kept so authentication,
 * registration and third party integrations keep working exactly as they do upstream.
 *
 * @package WooCommerce\Templates
 * @version 9.9.0
 */
defined( 'ABSPATH' ) || exit;

$gamilea_registration = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );

do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( $gamilea_registration ) : ?>
<div class="u-columns col2-set" id="customer_login">
	<div class="u-column1 col-1 gamilea-auth-panel gamilea-auth-panel--login">
<?php endif; ?>

		<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="gamilea-auth-note"><?php esc_html_e( 'Todos los campos son obligatorios.', 'gamilea' ); ?></p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( 'Correo electrónico o usuario', 'gamilea' ); ?></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" placeholder="<?php esc_attr_e( 'correo@ejemplo.com', 'gamilea' ); ?>" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( 'Contraseña', 'gamilea' ); ?></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Mantener la sesión iniciada', 'gamilea' ); ?></span>
				</label>
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Iniciar sesión', 'gamilea' ); ?>"><?php esc_html_e( 'Iniciar sesión', 'gamilea' ); ?></button>
			</p>
			<p class="woocommerce-LostPassword lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( '¿Olvidaste la contraseña?', 'gamilea' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php if ( $gamilea_registration ) : ?>
	</div>

	<div class="u-column2 col-2 gamilea-auth-panel gamilea-auth-panel--register">

		<h2><?php esc_html_e( '¿Primera vez aquí?', 'gamilea' ); ?></h2>
		<p class="gamilea-auth-lead"><?php esc_html_e( 'Crea tu cuenta con tu correo y recibirás un enlace para elegir tu contraseña.', 'gamilea' ); ?></p>

		<ul class="gamilea-auth-benefits">
			<li><?php echo tienda_icon( 'check' ); ?><?php esc_html_e( 'Sigue tus pedidos en todo momento', 'gamilea' ); ?></li>
			<li><?php echo tienda_icon( 'check' ); ?><?php esc_html_e( 'Guarda tus direcciones de envío', 'gamilea' ); ?></li>
			<li><?php echo tienda_icon( 'check' ); ?><?php esc_html_e( 'Compra más rápido la próxima vez', 'gamilea' ); ?></li>
		</ul>

		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Nombre de usuario', 'gamilea' ); ?></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Correo electrónico', 'gamilea' ); ?></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" placeholder="<?php esc_attr_e( 'correo@ejemplo.com', 'gamilea' ); ?>" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Contraseña', 'gamilea' ); ?></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
				</p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="woocommerce-form-row form-row">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button woocommerce-button button gamilea-button--secondary<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Crear mi cuenta', 'gamilea' ); ?>"><?php esc_html_e( 'Crear mi cuenta', 'gamilea' ); ?></button>
			</p>

			<?php
			/* Moved below the button by inc/account.php: the privacy notice is fine print, not a step. */
			if ( function_exists( 'wc_privacy_policy_text' ) ) {
				echo '<div class="gamilea-auth-fineprint">';
				wc_privacy_policy_text( 'registration' );
				echo '</div>';
			}
			?>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>
</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
