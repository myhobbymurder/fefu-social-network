<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGallery as BaseGallery;
use Doctrine\ORM\Mapping as ORM;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */

/**
 * gallery
 *
 * @ORM\Table(name="media__gallery")
 * @ORM\Entity
 */
class Gallery extends BaseGallery
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * NotShowInForm!
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @param string $name
     * @return \Aplication\Sonata\MediaBundle\Entity\Gallery
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @var string $context
     */
    protected $context;

    /**
     * @param string $context
     * @return \Aplication\Sonata\MediaBundle\Entity\Gallery
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @var string $defaultFormat
     */
    protected $defaultFormat;

    /**
     * @param string $format
     * @return \Aplication\Sonata\MediaBundle\Entity\Gallery
     */
    public function setDefaultFormat($format)
    {
        $this->defaultFormat = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $galleryHasMedias;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->galleryHasMedias = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add galleryHasMedias
     *
     * @param \Application\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedias
     * @return Gallery
     */
    public function addGalleryHasMedia(\Application\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedias)
    {
        $this->galleryHasMedias[] = $galleryHasMedias;

        return $this;
    }

    /**
     * Remove galleryHasMedias
     *
     * @param \Application\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedias
     */
    public function removeGalleryHasMedia(\Application\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedias)
    {
        $this->galleryHasMedias->removeElement($galleryHasMedias);
    }

    /**
     * Get galleryHasMedias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGalleryHasMedias()
    {
        return $this->galleryHasMedias;
    }

    /**
     * @return array
     */
    public function getGalleryHasMediasIds()
    {
        $ids = [];
        foreach ($this->getGalleryHasMedias() as $n) {
            $ids[$n->getMedia()->getId()] = $n;
        }

        return $ids;
    }

    /**
     * @param integer $id
     * @return \Application\Sonata\MediaBundle\Entity\GalleryHasMedia
     */
    public function getGalleryHasMedia($id)
    {
        $n = $this->getGalleryHasMediasIds();
        if (array_key_exists($id, $n)) {

            return $n[$id];
        }

        return null;
    }

}
