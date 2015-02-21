<?php

namespace AppBundle\Entities\Model;

use SourceBundle\Base\Model;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * AppBundle\Entities\Model\Visitor
 *
 * @ORM\Table(name="emails")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Entities\Repository\Email")
 */
class Email extends Model
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Visitor", inversedBy="emails")
     * @ORM\JoinColumn(name="visitor_id", referencedColumnName="id")
     */
    protected $visitor;

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
     * @return Email
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
     * @return Email
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
     * @return Email
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
     * Get content
     *
     * @return string
     */
    public function getVisitorId()
    {
        return $this->visitor->getId();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Email
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

    /**
     * Set visitor
     *
     * @param \AppBundle\Entities\Model\Visitor $visitor
     * @return Email
     */
    public function setVisitor(\AppBundle\Entities\Model\Visitor $visitor = null)
    {
        $this->visitor = $visitor;

        return $this;
    }

    /**
     * Get visitor
     *
     * @return \AppBundle\Entities\Model\Visitor 
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * Set from_address
     *
     * @param string $fromAddress
     * @return Email
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
     * @return Email
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

    public function asJSON()
    {
        $normalizer = new GetSetMethodNormalizer();
        $normalizer->setIgnoredAttributes(['visitor']);

        $serializer = new Serializer([$normalizer], [ new JsonEncode() ]);

        return $serializer->serialize($this, 'json');
    }


    public function asArray()
    {
        $normalizer = new GetSetMethodNormalizer();
        $normalizer->setIgnoredAttributes(['visitor']);

        $serializer = new Serializer([$normalizer], [ new JsonEncode() ]);

        return json_decode($serializer->serialize($this, 'json'), TRUE);
    }
}
