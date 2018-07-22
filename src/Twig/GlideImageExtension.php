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
     * @param string $imageUrl
     *
     * @return string
     */
    public function glideImageFilter($imageUrl, int $width = null, array $allowedPresets = [])
    {
        $srcsets = [];
        $largestWidth = 0;
        foreach ($this->getPresetList($allowedPresets) as $preset => $info) {
            $url = $this->generator->generate('generated_image', [
                'preset' => $preset,
                'path' => $imageUrl,
            ]);
            $srcsets[] = sprintf('%s %dw', $url, $info['w']);
            //$sizes[] = sprintf('(min-width: %spx) %spx', $info['w'], $info['w']);
            $largestWidth = max($info['w'], $largestWidth);
        }

        $width = $width ?? $largestWidth;

        return sprintf('<img srcset="%s" sizes="%dpx" width="%dpx" />', implode(', ', $srcsets), $width, $width);
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
