<?php

declare(strict_types=1);

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
     * @Route("/")
     */
    public function homepage()
    {
        return $this->redirect('/dir');
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

        $directories = $this->filterDirectories($list);

        $images = $this->filterImages($list);

        $parentDir = $path != '.' ? dirname($path) : '';

        return $this->render('directory/index.html.twig', [
            'controller_name' => 'ListController',
            'directory_name' => '/' . $path,
            'directories' => $directories,
            'images' => $images,
            'dir' => $parentDir,
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

        $images = $this->filterImages($this->glideServer->getSource()->listContents($directory));

        $prev = '';
        $next = '';
        foreach ($images as $i => $image) {
            if ($image['path'] == $path) {
                if (array_key_exists($i + 1, $images)) {
                    $next = $images[$i + 1]['path'];
                    break;
                }
            }
            $prev = $image['path'];
        }

        $response = $this->render('directory/image.html.twig', [
            'controller_name' => 'DirectoryController',
            'path' => $path,
            'image_name' => '/' . $path,
            'prev' => $prev,
            'next' => $next,
            'dir' => $directory,
        ]);

        return $response;
    }

    /**
     * Returns just the directories from a list of Glide file entries.
     *
     * @param array $list
     *   A list of directory records from Glide. These are nested arrays.
     * @return array
     *   Just those file records that are directories.
     */
    protected function filterDirectories(array $list) : array
    {
        return array_values(array_filter($list, function($item) {
            return $item['type'] == 'dir';
        }));
    }

    /**
     * Returns just the image files from a list of Glide file entries.
     *
     * @param array $list
     *   A list of file records from Glide. These are nested arrays.
     * @return array
     *   Just those file records that are images.
     */
    protected function filterImages(array $list) : array
    {
        return array_values(array_filter($list, function($item) {
            return $item['type'] == 'file'
                && in_array(strtolower($item['extension']), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        }));
    }
}
