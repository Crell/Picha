<?php
declare(strict_types=1);

namespace App;


use League\Glide\Responses\ResponseFactoryInterface;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\DependencyInjection\Container;

class PichaGlideFactory
{
    const CACHE_SUBDIR = 'glide';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $imageSourceDir;

    /**
     * @var string
     */
    protected $imageCacheDir;

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    public function __construct(string $imageSourceDir, string $kernelCacheDir, ResponseFactoryInterface $responseFactory)
    {
        //$this->container = $container;
        // Normalize to a single trailing slash so that it doesn't break mysteriously if the user
        // omits the slash.
        $this->imageSourceDir = trim($imageSourceDir, '/') . '/';

        $this->imageCacheDir = $kernelCacheDir . '/' . static::CACHE_SUBDIR;
        $this->responseFactory = $responseFactory;
    }

    public function make() : Server
    {
        $server = ServerFactory::create([
            'response' => $this->responseFactory,
            'source' => $this->imageSourceDir,
            'cache' => $this->imageCacheDir,
        ]);

        $server->setPresets([
            'small' => [
                'w' => 200,
                'h' => 200,
            ],
            'medium' => [
                'w' => 600,
                'h' => 400,
            ]
        ]);

        return $server;
    }
}
