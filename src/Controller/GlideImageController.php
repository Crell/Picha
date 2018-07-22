<?php

namespace App\Controller;

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
        $sourceDir = $this->container->getParameter('image_sources')[0];
        $cacheDir = $this->container->getParameter('kernel.cache_dir');

        $server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $sourceDir,
            'cache' => $cacheDir,
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

        $presets = $server->getPresets();

        $path = 'Blades/IMG_20180711_164352.jpg';

        $response = $server->getImageResponse($path, [
            'p' => 'medium',
        ]);

        return $response;

        return $this->render('glide_image/index.html.twig', [
            'controller_name' => 'GlideImageController',
        ]);
    }
}
