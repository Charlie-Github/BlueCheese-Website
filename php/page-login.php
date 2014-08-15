<?php
/**
 * Template Name: 前台登录
 */
  
if(!isset($_SESSION))
  session_start();
  
if( isset($_POST['ludou_token']) && ($_POST['ludou_token'] == $_SESSION['ludou_token'])) {
  $error = '';
  $secure_cookie = false;
  $user_name = sanitize_user( $_POST['log'] );
  $user_password = $_POST['pwd'];
  if ( empty($user_name) || ! validate_username( $user_name ) ) {
    $error .= '<strong>错误</strong>：请输入有效的用户名。<br />';
    $user_name = '';
  }
  
  if( empty($user_password) ) {
    $error .= '<strong>错误</strong>：请输入密码。<br />';
  }
  
  if($error == '') {
    // If the user wants ssl but the session is not ssl, force a secure cookie.
    if ( !empty($user_name) && !force_ssl_admin() ) {
      if ( $user = get_user_by('login', $user_name) ) {
        if ( get_user_option('use_ssl', $user->ID) ) {
          $secure_cookie = true;
          force_ssl_admin(true);
        }
      }
    }
      
    if ( isset( $_GET['r'] ) ) {
      $redirect_to = $_GET['r'];
      // Redirect to https if user wants ssl
      if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
        $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
    }
    else {
      $redirect_to = admin_url();
    }
    
	$redirect_to ="http://54.191.253.95/wordpress";
	
    if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
      $secure_cookie = false;
    
    $creds = array();
    $creds['user_login'] = $user_name;
    $creds['user_password'] = $user_password;
    $creds['remember'] = !empty( $_POST['rememberme'] );
    $user = wp_signon( $creds, $secure_cookie );
    if ( is_wp_error($user) ) {
      $error .= $user->get_error_message();
    }
    else {
      unset($_SESSION['ludou_token']);
      wp_safe_redirect($redirect_to);
    }
  }

  unset($_SESSION['ludou_token']);
}

$rememberme = !empty( $_POST['rememberme'] );
  
$token = md5(uniqid(rand(), true));
$_SESSION['ludou_token'] = $token;

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php
				/**
				 * The template used for displaying page content in page.php
				 *
				 * @package WordPress
				 * @subpackage Twenty_Twelve
				 * @since Twenty Twelve 1.0
				 */
				?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header">
							<?php if ( ! is_page_template( 'page-templates/front-page.php' ) ) : ?>
							<?php the_post_thumbnail(); ?>
							<?php endif; ?>
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header>

						<div class="entry-content">
							<?php the_content(); ?>
							<?php if(!empty($error)) {
							echo '<p class="ludou-error">'.$error.'</p>';
							}
							if (!is_user_logged_in()) { ?>
							<form name="loginform" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" class="ludou-login">
								<p>
								  <label for="log">用户名<br />
									<input type="text" name="log" id="log" class="input" value="<?php if(!empty($user_name)) echo $user_name; ?>" size="20" />
								  </label>
								
								<p>
								  <label for="pwd">密码(至少6位)<br />
									<input id="pwd" class="input" type="password" size="20" value="" name="pwd" />
								  </label>
								</p>
								
								<p class="forgetmenot">
								  <label for="rememberme">
									<input name="rememberme" type="checkbox" id="rememberme" value="1" <?php checked( $rememberme ); ?> />
									记住我
								  </label>
								</p>
								
								<p class="submit">
								  <input type="hidden" name="redirect_to" value="<?php if(isset($_GET['r'])) echo $_GET['r']; ?>" />
								  <input type="hidden" name="ludou_token" value="<?php echo $token; ?>" />
								  <input type="submit" name="wp-submit" id="wp-submit" value="登录" />
								</p>
							</form>
							<?php } else {
							echo '<p class="ludou-error">您已登录！（<a href="'.wp_logout_url().'" title="登出">登出？</a>）</p>';
							} ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
						</div><!-- .entry-content -->
						<footer class="entry-meta">
							<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
						</footer><!-- .entry-meta -->
					</article><!-- #post -->
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>