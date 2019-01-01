<?php

declare(strict_types=1);

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
     * This is the route for generating a specific image's variant.
     *
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
