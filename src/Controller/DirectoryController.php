<?php

declare(strict_types=1);

namespace App\Controller;

use League\Glide\Server;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DirectoryController extends AbstractController
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

        // Compute the next/prev images based on the current image.
        // We can't use array_search() here because $images is an array of arrays,
        // not just the values to check.
        $imageIndex = key(array_filter($images, function($image, $key) use ($path) {
            return $image['path'] == $path;
        }, ARRAY_FILTER_USE_BOTH));
        $prev = $images[$imageIndex - 1]['path'] ?? '';
        $next = $images[$imageIndex + 1]['path'] ?? '';

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
                && isset($item['extension'])
                && in_array(strtolower($item['extension']), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        }));
    }
}
