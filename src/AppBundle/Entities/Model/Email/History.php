<?php

namespace AppBundle\Entities\Model\Email;

use SourceBundle\Base\Model;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * AppBundle\Entities\Model\Email\History
 *
 * @ORM\Table(name="emails_history")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Entities\Repository\Email\History")
 */
class History extends Model
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $headers;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $from_address;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $from_name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set headers
     *
     * @param string $headers
     * @return History
     */
    public function setHeaders($headers)
    {
        $this->headers = is_array($headers) ? json_encode($headers) : $headers;

        return $this;
    }

    /**
     * Get headers
     *
     * @return string 
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return History
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return History
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set from_address
     *
     * @param string $fromAddress
     * @return History
     */
    public function setFromAddress($fromAddress)
    {
        $this->from_address = $fromAddress;

        return $this;
    }

    /**
     * Get from_address
     *
     * @return string
     */
    public function getFromAddress()
    {
        return $this->from_address;
    }

    /**
     * Set from_name
     *
     * @param string $fromName
     * @return History
     */
    public function setFromName($fromName)
    {
        $this->from_name = $fromName;

        return $this;
    }

    /**
     * Get from_name
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return History
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    public function asJSON()
    {
        $normalizer = new GetSetMethodNormalizer();
        $serializer = new Serializer([$normalizer], [ new JsonEncode() ]);

        return $serializer->serialize($this, 'json');
    }

    public function asArray()
    {
        $normalizer = new GetSetMethodNormalizer();
        $serializer = new Serializer([$normalizer], [ new JsonEncode() ]);

        return json_decode($serializer->serialize($this, 'json'), TRUE);
    }
}
