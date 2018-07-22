<?php

namespace App\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ImageController extends Controller
{
    /**
     * @Route("/image/{path}", name="image", requirements={"path"=".+"})
     */
    public function index(string $path)
    {

//        $imageSizes = $this->container->getParameter('image_sizes');

//        $image = new ImageElement($path, $imageSizes, $this->container->get('router'));

        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
            'image' => '@' . $path,
        ]);
    }

    /**
     * @Route("/generated/{imageSize}/{path}", name="generated_image", requirements={"path"=".+"})
     */
    public function generatedImage(string $imageSize, string $path)
    {
        $imageSources = $this->container->getParameter('image_sources');
        $imageRoot = $imageSources[0];

        $pathToFile = $imageRoot . $path;

        $filesystem = new Filesystem();

        if (!$filesystem->exists($pathToFile)) {

        }

        return new BinaryFileResponse($pathToFile);
    }
}

class ImageElement
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $imageSizes;

    /**
     * @var UrlGeneratorInterface
     */
    protected $generator;

    public function __construct(string $path, array $imageSizes, UrlGeneratorInterface $generator)
    {
        $this->path = $path;
        $this->imageSizes = $imageSizes;
        $this->generator = $generator;
    }

    public function srcset() : string
    {
        $srcsets = [];

        foreach ($this->imageSizes as $sizeName => $dims) {
            $url = $this->generator->generate('generated_image', [
                'imageSize' => $sizeName,
                'path' => $this->path,
            ]);
            $srcsets[] = sprintf('%s %dw', $url,$dims['width']);
        }

        return implode(', ', $srcsets);
    }

    public function altText() : string
    {
        return '';
    }

    public function caption() : string
    {
        return 'This is a caption';
    }
}

class PictureSource
{
    public function url() {

    }

    public function width()
    {

    }

    public function height()
    {

    }

}

class Picture
{

    protected $defaultUrl;

    public function __construct(string $url)
    {
        $this->defaultUrl = $url;
    }

    public function defaultUrl() : string
    {

    }

    public function altText() : string
    {
        return '';
    }

    public function getSources() : iterable
    {

    }
}
