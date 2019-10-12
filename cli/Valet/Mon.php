<?php


namespace Valet;


class Mon
{
    var $files;

    const BINARY = __DIR__ . '/../../bin/mongroup';

    /**
     * Create a new PHP FPM class instance.
     *
     * @param  Filesystem $files
     */
    function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    function install()
    {
        passthru(
            sprintf(
            "(mkdir /tmp/mon && cd /tmp/mon && curl -L# https://github.com/tj/mon/archive/master.tar.gz | tar zx --strip 1 && PREFIX=%s make install && rm -rf /tmp/mon)",
                __DIR__ . '/../..'
            )
        );
    }

    function configure() {
        $this->files->ensureDirExists(VALET_HOME_PATH . '/Pids', user(), 0775);
        $this->files->ensureDirExists(VALET_HOME_PATH . '/Log', user(), 0775);
    }
}
