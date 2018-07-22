<?php

namespace App\Controller;

use League\Glide\Server;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use League\Glide\ServerFactory;
use League\Glide\Responses\SymfonyResponseFactory;

class GlideImageController extends Controller
{
    /**
     * @Route("/glide/image", name="glide_image")
     */
    public function index()
    {
        $server = $this->getGlideServer();

        $presets = $server->getPresets();

        return $this->render('glide_image/index.html.twig', [
            'controller_name' => 'GlideImageController',
        ]);
    }

    /**
     * @Route("/generated/{preset}/{path}", name="generated_image", requirements={"path"=".+"})
     */
    public function generatedImage(string $preset, string $path)
    {

        $server = $this->getGlideServer();

        $response = $server->getImageResponse($path, [
            'p' => $preset,
        ]);

        return $response;
    }

    public function getGlideServer() : Server
    {
        $sourceDir = $this->container->getParameter('image_sources')[0];
        $cacheDir = $this->container->getParameter('kernel.cache_dir');

        $server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $sourceDir,
            'cache' => $cacheDir . '/glide',
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
