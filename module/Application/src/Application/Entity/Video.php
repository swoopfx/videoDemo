<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use CsnUser\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="video")
 *
 * @author otaba
 *        
 */
class Video
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="video_url", type="string", length=200, nullable=true)
     */
    private $videoUrl;

    /**
     *
     * @var string @ORM\Column(name="video_name", type="string", length=200, nullable=true)
     */
    private $videoName;

    /**
     *
     * @var Datetime @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     *
     * @var Datetime @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     *
     * @var boolean @ORM\Column(name="is_hidden", type="boolean", nullable=true)
     */
    private $isHidden;

    /**
     *
     * @var string @ORM\Column(name="mime_type", type="string", length=100, nullable=true)
     */
    private $mimeType;

    /**
     *
     * @var string @ORM\Column(name="doc_ext", type="string", length=45, nullable=true)
     */
    private $docExt;

    /**
     *
     * @var string @ORM\Column(name="doc_code", type="string", length=100, nullable=true)
     */
    private $docCode;

    /**
     *
     * @var text
     */
    private $videoMeta;
    
    /**
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\User", inversedBy="video")
     * @var U
     */
    private $user;

    // TODO - Insert your code here
    
    /**
     */
    public function __construct()
    {
        
        // TODO - Insert your code here
    }
    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $videoUrl
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    /**
     * @return the $videoName
     */
    public function getVideoName()
    {
        return $this->videoName;
    }

    /**
     * @return the $createdOn
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return the $updatedOn
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * @return the $isHidden
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * @return the $mimeType
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return the $docExt
     */
    public function getDocExt()
    {
        return $this->docExt;
    }

    /**
     * @return the $docCode
     */
    public function getDocCode()
    {
        return $this->docCode;
    }

    /**
     * @return the $videoMeta
     */
    public function getVideoMeta()
    {
        return $this->videoMeta;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $videoUrl
     */
    public function setVideoUrl($videoUrl)
    {
        $this->videoUrl = $videoUrl;
        return $this;
    }

    /**
     * @param string $videoName
     */
    public function setVideoName($videoName)
    {
        $this->videoName = $videoName;
        return $this; 
    }

    /**
     * @param Datetime $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
        return $this;
    }

    /**
     * @param Datetime $updatedOn
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;
        return $this;
    }

    /**
     * @param boolean $isHidden
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;
        return $this;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @param string $docExt
     */
    public function setDocExt($docExt)
    {
        $this->docExt = $docExt;
        return $this;
    }

    /**
     * @param string $docCode
     */
    public function setDocCode($docCode)
    {
        $this->docCode = $docCode;
        return $this;
    }

    /**
     * @param text $videoMeta
     */
    public function setVideoMeta($videoMeta)
    {
        $this->videoMeta = $videoMeta;
        
        return $this;
    }

}

