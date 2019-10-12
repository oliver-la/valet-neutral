<?php


namespace Valet;


class Mon
{
    const BINARY = __DIR__ . '/../../bin/mongroup';

        /**
     * Create a new PHP FPM class instance.
     *
     * @param  Brew $brew
     * @param  CommandLine $cli
     * @param  Filesystem $files
     */
    function __construct(CommandLine $cli, Filesystem $files)
    {
        $this->cli = $cli;
        $this->files = $files;
    }

    function install()
    {
        if ($this->files->exists(__DIR__ . '/../../bin/mon')) {
            info('[Mon] Already installed');
            return;
        }

        info('[Mon] Installing');

        passthru(
            sprintf(
            "(mkdir /tmp/mon && cd /tmp/mon && curl -L# https://github.com/tj/mon/archive/master.tar.gz | tar zx --strip 1 && PREFIX=%s make install && rm -rf /tmp/mon)",
                __DIR__ . '/../..'
            )
        );

        $this->files->ensureDirExists(VALET_HOME_PATH . '/Pids', user(), 0775);
        $this->files->ensureDirExists(VALET_HOME_PATH . '/Log', user(), 0775);
    }
}
