<?php
declare(strict_types=1);

namespace App\Twig;

use League\Glide\Server;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Generates a source tag to use inside an responsive picture
 *
 */
class GlideImageExtension extends AbstractExtension
{
    /**
     * @var Server
     */
    protected $glideServer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $generator;

    public function __construct(Server $glideServer, UrlGeneratorInterface $generator)
    {
        $this->glideServer = $glideServer;
        $this->generator = $generator;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('glide_image', [$this, 'glideImageSourceFilter'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $imageUrl
     *
     * @return string
     */
    public function glideImageSourceFilter($imageUrl)
    {
        $presets = $this->glideServer->getPresets();

        foreach ($presets as $preset => $info) {

            $url = $this->generator->generate('generated_image', [
                'preset' => $preset,
                'path' => $imageUrl,
            ]);
            $srcsets[] = sprintf('%s %dw', $url, $info['w']);
        }

        return sprintf('<img srcset="%s" />', implode(', ', $srcsets));
        // It would be better to include the src tag too, but we need a direct route to the raw image then.
        // @todo Add that.
        //return sprintf('<img src="%s" srcset="%s" />', $imageUrl, implode(', ', $srcsets));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'glide';
    }
}
