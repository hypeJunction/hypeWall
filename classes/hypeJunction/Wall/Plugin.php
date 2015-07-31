<?php

namespace hypeJunction\Wall;

/**
 * Wall service provider
 *
 * @property-read \ElggPlugin                                $plugin
 * @property-read \hypeJunction\Wall\Config                  $config
 * @property-read \hypeJunction\Wall\HookHandlers            $hooks
 * @property-read \hypeJunction\Wall\Router		             $router
 * @property-read \hypeJunction\Wall\Services\Geopositioning $geo
 * @property-read \hypeJunction\Wall\Services\Notifications  $notifications
 * @property-read \hypeJunction\Wall\Services\Upgrades       $upgrades
 */
final class Plugin extends \hypeJunction\Plugin {

	/**
	 * {@inheritdoc}
	 */
	static $instance;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(\ElggPlugin $plugin) {

		$this->setValue('plugin', $plugin);
		$this->setFactory('config', function (Plugin $p) {
			return new \hypeJunction\Wall\Config($p->plugin);
		});
		$this->setFactory('hooks', function (Plugin $p) {
			return new \hypeJunction\Wall\HookHandlers($p->config, $p->router);
		});
		$this->setFactory('router', function (Plugin $p) {
			return new \hypeJunction\Wall\Router($p->config);
		});
		$this->setClassName('geo', Services\Geopositioning::CLASSNAME);
		$this->setClassName('notifications', Services\Notifications::CLASSNAME);
		$this->setClassName('upgrades', Services\Upgrades::CLASSNAME);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function factory() {
		if (null === self::$instance) {
			$plugin = elgg_get_plugin_from_id('hypeWall');
			self::$instance = new self($plugin);
		}
		return self::$instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {
		elgg_register_event_handler('init', 'system', array($this, 'init'));
		elgg_register_event_handler('upgrade', 'system', array($this->upgrades, 'upgrade'));
		if ($this->config->get('legacy_mode')) {
			require_once $this->plugin->getPath() . 'lib/functions.php';
		}
	}

	/**
	 * System init callback
	 * @return void
	 */
	public function init() {

		elgg_register_page_handler($this->router->getPageHandlerId(), array($this->router, 'handlePages'));
		elgg_register_plugin_hook_handler('entity:url', 'object', array($this->hooks, 'urlHandler'));

		elgg_register_entity_type('object', Post::SUBTYPE);

		elgg_extend_view('css/elgg', 'css/framework/wall/stylesheet');
		elgg_extend_view('js/initialize_elgg', 'js/framework/wall/config');

		// AJAX view to load URL previews
		elgg_register_ajax_view('output/wall/url');

		if (\hypeJunction\Integration::isElggVersionBelow('1.9.0')) {
			elgg_load_js('jquery.form');
			elgg_extend_view('js/elgg', 'js/framework/wall/legacy/status');

			// Display wall form
			elgg_extend_view('page/layouts/content/filter', 'framework/wall/container', 100);

			// Notifications
			register_notification_object('object', Post::SUBTYPE, elgg_echo('wall:new:notification:generic'));
			elgg_register_event_handler('publish', 'object', array($this->notifications, 'sendMessageLegacy'));
			elgg_register_plugin_hook_handler('object:notifications', 'object', array($this->notifications, 'disableDefaultHandlerLegacy'));
			elgg_register_plugin_hook_handler('notify:entity:message', 'object', array($this->notifications, 'formatMessageLegacy'));
		} else {

			// Display wall form
			elgg_extend_view('page/layouts/elements/filter', 'framework/wall/container', 100);

			// JS
			elgg_extend_view('js/elgg', 'js/framework/wall/elgg.js');

			// Notifications
			elgg_register_event_handler('publish', 'object', array($this->notifications, 'sendCustomNotifications'));

			elgg_register_notification_event('object', 'hjwall', array('publish'));
			elgg_register_notification_event('object', 'thewire', array('publish'));
			elgg_register_plugin_hook_handler('prepare', 'notification:publish:object:hjwall', array($this->notifications, 'formatMessage'));
			elgg_register_plugin_hook_handler('prepare', 'notification:publish:object:thewire', array($this->notifications, 'formatMessage'));
		}

		$action_path = $this->plugin->getPath() . '/actions/';

		elgg_register_action('wall/status', $action_path . 'wall/status.php');
		elgg_register_action('wall/photo', $action_path . 'wall/status.php');
		elgg_register_action('wall/file', $action_path . 'wall/status.php');
		elgg_register_action('wall/url', $action_path . 'wall/status.php');
		elgg_register_action('wall/content', $action_path . 'wall/status.php');

		elgg_register_action('wall/delete', $action_path . 'wall/delete.php');
		elgg_register_action('wall/remove_tag', $action_path . 'wall/remove_tag.php');

		elgg_register_action('wall/geopositioning/update', $action_path . 'wall/geopositioning/update.php', 'public');

		elgg_register_plugin_hook_handler('container_permissions_check', 'object', array($this->hooks, 'containerPermissionsCheck'));

		elgg_register_plugin_hook_handler('register', 'menu:river', array($this->hooks, 'riverMenuSetup'));
		elgg_register_plugin_hook_handler('register', 'menu:entity', array($this->hooks, 'entityMenuSetup'));
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', array($this->hooks, 'ownerBlockMenuSetup'));
		elgg_register_plugin_hook_handler('register', 'menu:user_hover', array($this->hooks, 'userHoverMenuSetup'));

		elgg_register_plugin_hook_handler('get_views', 'ecml', array($this->hooks, 'getECMLViews'));

		elgg_register_plugin_hook_handler('view', 'object/thewire', array($this->hooks, 'hijackWire'));
		elgg_register_plugin_hook_handler('view', 'river/object/thewire/create', array($this->hooks, 'hijackWireRiver'));

		elgg_register_widget_type('wall', elgg_echo('wall'), elgg_echo('wall:widget:description'));

		add_group_tool_option('wall', elgg_echo('wall:groups:enable'), false);
		elgg_extend_view('groups/tool_latest', 'framework/wall/group_module');
	}

}
