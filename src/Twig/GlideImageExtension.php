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
            new TwigFilter('glide_image', [$this, 'glideImageFilter'], ['is_safe' => ['html']]),
            new TwigFilter('glide_picture', [$this, 'glidePictureFilter'], ['is_safe' => ['html']]),
        ];
    }

    protected function getPresetList(array $allowedPresets = []) : iterable
    {
        $presets = $this->glideServer->getPresets();

        foreach ($presets as $preset => $info) {
            if (!array_key_exists('w', $info)) {
                continue;
            }
            if ($allowedPresets && !in_array($preset, $allowedPresets)) {
                continue;
            }
            yield $preset => $info;
        }
    }

    public function glidePictureFilter($imageUrl, array $allowedPresets = [])
    {
        $sources = [];
        foreach ($this->getPresetList($allowedPresets) as $preset => $info) {
            $url = $this->generator->generate('generated_image', [
                'preset' => $preset,
                'path' => $imageUrl,
            ]);

            $sources[] = sprintf('<source media="max-width: %dpx" srcset="%s" />', $info['w'], $url);

//            $srcsets[] = sprintf('%s %dw', $url, $info['w']);
//            $sizes[] = sprintf('(min-width: %spx) %spx', $info['w'], $info['w']);
//            $largestWidth = max($info['w'], $largestWidth);
        }

        return "<picture>\n" . implode("\n", $sources) . "\n</picture>\n";
    }

    /**
     * Creates an image tag using srcset via Glide.
     *
     * @param string $imageUrl
     *   The URL to the native image.  It will be processed to glide variants
     *   automatically.
     *
     * @return string
     *   A formatted image tag.
     */
    public function glideImageFilter($imageUrl, array $config = []) : string
    {
        // It would be better to include the src tag too, but we need a direct route to the raw image then.
        // @todo Add that.

        $config += [
            // The image tag's width.
            'width' => 0,
            // Which defined presets to use.  If not specified, all defined presets will be used.
            'presets' => [],
            // The size steps to define for this image.
            'sizes' => [],
        ];

        $allowedPresets = $config['presets'];
        $width = $config['width'];
        $sizes = $config['sizes'];

        $image = new ImageTag();

        foreach ($this->getPresetList($allowedPresets) as $preset => $info) {
            $url = $this->generator->generate('generated_image', [
                'preset' => $preset,
                'path' => $imageUrl,
            ]);
            $image->addSrcSet(sprintf('%s %dw', $url, $info['w']));
        }

        $image
            ->setWidth($width)
            ->addSizes($sizes);

        return (string)$image;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'glide';
    }
}
