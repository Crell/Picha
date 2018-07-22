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
     * @var Server
     */
    protected $glideServer;

    public function __construct(Server $glideServer)
    {
        $this->glideServer = $glideServer;
    }

    /**
     * @Route("/glide/image", name="glide_image")
     */
    public function index()
    {
        $presets = $this->glideServer->getPresets();

        return $this->render('glide_image/index.html.twig', [
            'controller_name' => 'GlideImageController',
        ]);
    }

    /**
     * @Route("/generated/{preset}/{path}", name="generated_image", requirements={"path"=".+"})
     */
    public function generatedImage(string $preset, string $path)
    {
        $response = $this->glideServer->getImageResponse($path, [
            'p' => $preset,
        ]);

        return $response;
    }
}
