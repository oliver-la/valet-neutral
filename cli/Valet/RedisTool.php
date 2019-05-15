<?php

namespace Valet;

class RedisTool extends AbstractService
{
    var $brew;
    var $cli;
    var $files;
    var $site;

    const REDIS_CONF = HOMEBREW_PREFIX . '/etc/redis.conf';

    /**
     * Create a new instance.
     *
     * @param  Brew          $brew
     * @param  CommandLine   $cli
     * @param  Filesystem    $files
     * @param  Configuration $configuration
     * @param  Site          $site
     */
    function __construct(
        Brew $brew,
        CommandLine $cli,
        Filesystem $files,
        Configuration $configuration,
        Site $site
    ) {
        $this->cli   = $cli;
        $this->brew  = $brew;
        $this->site  = $site;
        $this->files = $files;
        parent::__construct($configuration);
    }

    /**
     * Install the service.
     *
     * @return void
     */
    function install()
    {
        if ($this->installed()) {
            info('[redis] already installed');
        } else {
            $this->brew->installOrFail('redis');
            if (PHP_OS === 'Darwin') {
                $this->cli->quietly('sudo brew services stop redis');
            }
        }

        $this->installConfiguration();
        $this->setEnabled(self::STATE_ENABLED);
        $this->restart();
    }

    /**
     * Returns wether redis is installed or not.
     *
     * @return bool
     */
    function installed()
    {
        return $this->brew->installed('redis');
    }

    /**
     * Install the configuration file.
     *
     * @return void
     */
    function installConfiguration()
    {
        $contents = $this->files->get(__DIR__.'/../stubs/redis.conf');
        $contents = str_replace('$HOMEBREW_PREFIX', HOMEBREW_PREFIX, $contents);
        $this->files->putAsUser(static::REDIS_CONF, $contents);
    }

    /**
     * Restart the service.
     *
     * @return void
     */
    function restart()
    {
        if (!$this->installed() || !$this->isEnabled()) {
            return;
        }

        info('[redis] Restarting');
        $this->brew->restartService('redis');
    }

    /**
     * Stop the service.
     *
     * @return void
     */
    function stop()
    {
        if (!$this->installed()) {
            return;
        }

        info('[redis] Stopping');
        $this->brew->stopService('redis');
    }

    /**
     * Prepare for uninstallation.
     *
     * @return void
     */
    function uninstall()
    {
        $this->stop();
    }
}
