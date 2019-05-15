<?php


namespace Valet;


class Mon
{
    static function install()  {
        passthru(
            sprintf(
            "(mkdir /tmp/mon && cd /tmp/mon && curl -L# https://github.com/tj/mon/archive/master.tar.gz | tar zx --strip 1 && PREFIX=%s make install && rm -rf /tmp/mon)",
                __DIR__ . '/../..'
            )
        );
    }
}
