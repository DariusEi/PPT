<?php
/**
 * Template Name: Login
 *
 * Custom login page. Redirects to /dashboard/ if already logged in.
 */

if ( is_user_logged_in() ) {
    wp_safe_redirect( home_url( '/dashboard/' ) );
    exit;
}

get_header();
?>

<main id="primary" class="pt101-login-page">
  <div class="pt101-login-wrap">

    <div class="pt101-login-brand">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="pt101-login-logo">
        <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
      </a>
      <p class="pt101-login-tagline">Welcome back. Your trading education continues here.</p>
    </div>

    <div class="pt101-login-card">
      <h1 class="pt101-login-heading">Log in to your account</h1>

      <?php
      /* Show WP login errors (wrong password etc.) */
      $login_error = '';
      if ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) {
          $login_error = 'Incorrect email or password. Please try again.';
      } elseif ( isset( $_GET['login'] ) && 'empty' === $_GET['login'] ) {
          $login_error = 'Please enter your email and password.';
      }
      if ( $login_error ) :
      ?>
        <div class="pt101-login-error" role="alert"><?php echo esc_html( $login_error ); ?></div>
      <?php endif; ?>

      <?php
      wp_login_form( [
          'redirect'       => home_url( '/dashboard/' ),
          'label_username' => 'Email address',
          'label_password' => 'Password',
          'label_remember' => 'Keep me logged in',
          'label_log_in'   => 'Log in',
          'id_username'    => 'pt101-login-email',
          'id_password'    => 'pt101-login-password',
          'remember'       => true,
      ] );
      ?>

      <p class="pt101-login-forgot">
        <a href="<?php echo esc_url( wp_lostpassword_url( home_url( '/login' ) ) ); ?>">Forgot your password?</a>
      </p>
    </div>

    <p class="pt101-login-enroll">
      Don&rsquo;t have an account?
      <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>">Enroll in a course &rarr;</a>
    </p>

  </div>
</main>

<?php get_footer(); ?>
