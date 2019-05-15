<?php

namespace Valet;

use Httpful\Request;
use DomainException;

class Ngrok
{
    var $tunnelsEndpoint = 'http://127.0.0.1:4040/api/tunnels';

    /**
     * Get the current tunnel URL from the Ngrok API.
     *
     * @return string
     */
    function currentTunnelUrl()
    {
        return retry(20, function () {
            $body = Request::get($this->tunnelsEndpoint)->send()->body;

            // If there are active tunnels on the Ngrok instance we will spin through them and
            // find the one responding on HTTP. Each tunnel has an HTTP and a HTTPS address
            // but for local testing purposes we just desire the plain HTTP URL endpoint.
            if (isset($body->tunnels) && count($body->tunnels) > 0) {
                return $this->findHttpTunnelUrl($body->tunnels);
            } else {
                throw new DomainException("Tunnel not established.");
            }
        }, 250);
    }

    /**
     * Find the HTTP tunnel URL from the list of tunnels.
     *
     * @param  array  $tunnels
     * @return string|null
     */
    function findHttpTunnelUrl($tunnels)
    {
        foreach ($tunnels as $tunnel) {
            if ($tunnel->proto === 'http') {
                return $tunnel->public_url;
            }
        }
    }

    static function install() {
        $arch = strtolower(PHP_OS . '-' . php_uname("m"));

        $packages = array(
            'linux-aarch64_be' => 'https://bin.equinox.io/a/nmkK3DkqZEB/ngrok-2.2.8-linux-arm64.zip',
            'linux-aarch64' => 'https://bin.equinox.io/a/nmkK3DkqZEB/ngrok-2.2.8-linux-arm64.zip',
            'linux-armv8b' => 'https://bin.equinox.io/a/nmkK3DkqZEB/ngrok-2.2.8-linux-arm64.zip',
            'linux-armv8l' => 'https://bin.equinox.io/a/nmkK3DkqZEB/ngrok-2.2.8-linux-arm64.zip',
            'linux-x86_64' => 'https://bin.equinox.io/a/nmkK3DkqZEB/ngrok-2.2.8-linux-arm64.zip',
            'linux-arm' => 'https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-arm.zip',
        );

        if (in_array($arch, array_keys($packages))) {
            $url = $packages[$arch];

            passthru(
                sprintf(
                    "(cd %s && curl -L# %s > ngrok.zip && unzip -o ngrok.zip && rm ngrok.zip)",
                    __DIR__ . '/../../bin',
                    $url
                )
            );
        } else {
            throw new DomainException('Unable to find a suitable ngrok binary for your architecture: ' . $arch);
        }
    }
}
