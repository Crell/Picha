<?php

namespace App\Controller;

use League\Glide\Server;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DirectoryController extends Controller
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
     * This is the route for listing a directory.
     *
     * @Route("/dir/{path}", name="imagedir", requirements={"path"=".+"})
     */
    public function index(string $path = '')
    {
        $source = $this->glideServer->getSource();

        $list = $source->listContents($path);

        $directories = array_filter($list, function($item) {
            return $item['type'] == 'dir';
        });

        $images = array_filter($list, function($item) {
            return $item['type'] == 'file'
                && in_array(strtolower($item['extension']), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });

        return $this->render('directory/index.html.twig', [
            'controller_name' => 'ListController',
            'directory_name' => '/' . $path,
            'directories' => $directories,
            'images' => $images,
        ]);
    }

    /**
     * This is the route for an individual image's page.
     *
     * @Route("/image/{path}", name="glide_image", requirements={"path"=".+"})
     */
    public function image($path)
    {
        $directory = dirname($path);


        return $this->render('directory/image.html.twig', [
            'controller_name' => 'DirectoryController',
            'path' => $path,
            'image_name' => '/' . $path,
        ]);
    }
}
