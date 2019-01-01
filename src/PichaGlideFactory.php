<?php
declare(strict_types=1);

namespace App;


use League\Glide\Responses\ResponseFactoryInterface;
use League\Glide\Server;
use League\Glide\ServerFactory;

/**
 * Factory for a Glide server, configured by Symfony.
 */
class PichaGlideFactory
{
    const CACHE_SUBDIR = 'glide';

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
        // Normalize to a single trailing slash so that it doesn't break mysteriously if the user
        // omits the slash.
        $this->imageSourceDir = rtrim($imageSourceDir, '/') . '/';

        $this->imageCacheDir = $kernelCacheDir . '/' . static::CACHE_SUBDIR;
        $this->responseFactory = $responseFactory;
    }

    public function make() : Server
    {
        $server = ServerFactory::create([
            'response' => $this->responseFactory,
            'source' => $this->imageSourceDir,
            'cache' => $this->imageCacheDir,
            //'driver' => 'imagick',
        ]);

        // @todo There should be some way to push this to a YAML file, but I don't know what yet.
        // Needs some Symfony research.
        $server->setPresets([
            'small' => [
                'w' => 320,
                'h' => 240,
                #'filt' => 'greyscale',
            ],
            'medium' => [
                'w' => 640,
                'h' => 480,
                # 'filt' => 'sepia',
            ],
            'large' => [
                'w' => 1024,
                'h' => 768,
                # 'filt' => 'greyscale',
            ],
            'huge' => [
                'w' => 2048,
                'h' => 768*2,
//                'filt' => 'greyscale',
            ],
            'mega' => [
                'w' => 2048*2,
                'h' => 768*4,
            ],
        ]);

        return $server;
    }
}
