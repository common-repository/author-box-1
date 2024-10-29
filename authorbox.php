<?php
/*
Plugin Name: Author Box
Version: 1.1
Description: Adds an author box below text when viewing a single article.
Author: Eugen Paun	
Author URI: http://wptuts.ro/
Plugin URI: http://wptuts.ro/
License: GNU GPL v3 or later

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

Class Redecs_AuthorBox {

	public static function init() {
		global $wp_version;
		// Simple Author Box requires Wordpress 2.9 or grater
		if (version_compare($wp_version, "2.9", "<")) {
			return false;
		}
		self::addFilters();
		self::addActions();
		load_plugin_textdomain('authorbox', false, dirname(plugin_basename(__FILE__ )));
		return true;
	}

	public static function filterContactMethods($contactmethods) {
		//add
		$contactmethods['twitter'] = 'Twitter';
		$contactmethods['facebook'] = 'Facebook';
		// remove
		unset($contactmethods['yim']);
		unset($contactmethods['aim']);
		return $contactmethods;
	}

	public static function filterContent($content = '') {
		if( is_single() ) {
			$author = array();
			$author['name'] = get_the_author();
			$author['twitter'] = get_the_author_meta('twitter');
			$author['facebook'] = get_the_author_meta('facebook');
			$author['posts'] = (int)get_the_author_posts();
			ob_start();
			?>
			<div id="authorbox">
				<div class="authorbox-info">
					<?php echo get_avatar( get_the_author_email(), '60' ); ?>
					<h4><?php echo __('About the author', 'authorbox'); ?></h4>
					<p class="authorbox-text"><?php echo esc_attr(sprintf(__ngettext('%s wrote one article on this blog', '%s wrote %d articles on this blog', $author['posts'], 'authorbox'), get_the_author_firstname().' '.get_the_author_lastname(), $author['posts'])); ?>.</p>
					<p class="authorbox-meta"><?php echo get_the_author_meta('description'); ?></p>
					<ul>
						<li class="first"><a href="<?php echo get_the_author_meta('url'); ?>" title="<?php echo esc_attr(sprintf(__('Read %s&#8217;s blog', 'authorbox'), $author['name'])); ?>"><?php echo __("Blog"); ?></a></li>
						<?php if(!empty($author['twitter'])): ?>
						<li><a href="<?php echo $author['twitter']; ?>" title="<?php echo esc_attr(sprintf(__('Follow %s on Twitter', 'authorbox'), $author['name'])); ?>" rel="external">Twitter</a></li>
						<?php endif; ?>
						<?php if(!empty($author['facebook'])): ?>
						<li><a href="<?php echo $author['facebook']; ?>" title="<?php echo esc_attr(sprintf(__('Be %s&#8217;s friend on Facebook', 'authorbox'), $author['name'])); ?>" rel="external">Facebook</a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<?php
			$content .= ob_get_clean();
		}
		return $content;
	}

	public static function pluginCss() {
		if(file_exists(self::getPluginDir() . '/authorbox.css')) {
			wp_register_style('authorbox', self::getPluginUrl().'/authorbox.css');
			wp_enqueue_style('authorbox');
		}
	}

	private static function getPluginDir() {
		return WP_PLUGIN_DIR .'/'. dirname(plugin_basename(__FILE__));
	}

	private static function getPluginUrl() {
		return WP_PLUGIN_URL .'/'. dirname(plugin_basename(__FILE__));
	}

	private static function addFilters() {
		add_filter('user_contactmethods', array('Redecs_AuthorBox', 'filterContactMethods'));
		add_filter('the_content', array('Redecs_AuthorBox', 'filterContent'));
	}

	private static function addActions() {
		add_action('wp_print_styles', array('Redecs_AuthorBox', 'pluginCss'));
	}
}

if(!Redecs_AuthorBox::init()) {
	echo 'AuthorBox plugin requires WordPress 2.9 or higher. Please upgrade!';
}
