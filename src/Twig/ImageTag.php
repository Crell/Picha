<?php
declare(strict_types=1);

namespace App\Twig;


class ImageTag
{
    /**
     * @var array
     */
    protected $srcsets = [];

    /**
     * @var array
     */
    protected $sizes = [];

    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $src = 0;

    /**
     * @var string
     */
    protected $alt = '';

    public function setSrc(string $src) : self
    {
        $this->src = $src;
        return $this;
    }

    public function addSrcSet(string $srcset) : self
    {
        $this->srcsets[] = $srcset;
        return $this;
    }

    public function addSize(string $size) : self
    {
        $this->sizes[] = $size;
        return $this;
    }

    public function addSizes(array $sizes) : self
    {
        $this->sizes = array_merge($this->sizes, $sizes);
        return $this;
    }

    public function setWidth(int $width) : self
    {
        $this->width = $width;
        return $this;
    }

    public function setAlt(string $alt) : self
    {
        $this->alt = $alt;
        return $this;
    }

    public function __toString()
    {
        $attributes = [];

        if ($this->src) {
            $attributes[] = "src=\"{$this->src}\"";
        }

        if ($this->srcsets) {
            $attributes[] = sprintf('srcset="%s"', implode(', ', $this->srcsets));
        }

        if ($this->sizes) {
            $attributes[] = sprintf('sizes="%s"', implode(', ', $this->sizes));
        }

        if ($this->width) {
            $attributes[] = "width=\"{$this->width}px\"";
        }

        if ($this->alt) {
            $attributes[] = "alt=\"{$this->alt}\"";
        }

        return '<img ' . implode(' ', $attributes) . '/>';
    }
}
