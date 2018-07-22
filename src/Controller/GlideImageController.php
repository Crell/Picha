<?php

namespace App\Controller;

use League\Glide\Server;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * Route("/glide/image/{path}", name="glide_image", requirements={"path"=".+"})
     */
    public function index($path)
    {

        return $this->render('image.html.twig', [
            'controller_name' => 'GlideImageController',
            'path' => $path,
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
