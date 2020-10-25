<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Plugin\LoggerChannels\Handlers\HandlerFactory;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * Class LoggerChannelsPlugin
 * @package Grav\Plugin
 */
class LoggerChannelsPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => [
                ['autoload', 100000], // TODO: Remove when plugin requires Grav >=1.7
                ['onPluginsInitialized', 0]
            ]
        ];
    }

    /**
     * Composer autoload.
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            //return;
        }

        // Enable the main events we are interested in
        $this->enable([
            // Put your main events here
        ]);

        /** @var Logger $log */
        $log = Grav::instance()['log'];

        foreach ($this->config()['handlers'] as $channel => $configs) {
            foreach ($configs as $config) {
                $handler = HandlerFactory::create($channel, $config)->getHandler();
                $handler->setFormatter(new LineFormatter());
                $log->pushHandler($handler);
            }
        }
    }
}
