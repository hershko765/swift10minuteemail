<?php

namespace AppBundle\Entities\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SourceBundle\Base\Model;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * AppBundle\Entities\Model\Visitor
 *
 * @ORM\Table(name="visitors")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Entities\Repository\Visitor")
 */
class Visitor extends Model
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
     * @ORM\Column(type="string", length=80)
     */
    protected $email;

    /**
     * @ORM\OneToMany(targetEntity="Email", mappedBy="visitors")
     */
    protected $emails;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime")
     */
    protected $close_date;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created = new \DateTime();
        $this->close_date = new \DateTime('10 minutes');
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set email
     *
     * @param string $email
     * @return Visitor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Visitor
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
     * Set close_date
     *
     * @param \DateTime $closeDate
     * @return Visitor
     */
    public function setCloseDate($closeDate)
    {
        $this->close_date = $closeDate;

        return $this;
    }

    /**
     * Get close_date
     *
     * @return \DateTime 
     */
    public function getCloseDate()
    {
        return $this->close_date;
    }

    /**
     * Add emails
     *
     * @param \AppBundle\Entities\Model\Email $emails
     * @return Visitor
     */
    public function addEmail(\AppBundle\Entities\Model\Email $emails)
    {
        $this->emails[] = $emails;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param \AppBundle\Entities\Model\Email $emails
     */
    public function removeEmail(\AppBundle\Entities\Model\Email $emails)
    {
        $this->emails->removeElement($emails);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    public function asJSON()
    {
        $normalizer = new GetSetMethodNormalizer();
        $normalizer->setIgnoredAttributes(['emails']);

        $serializer = new Serializer([$normalizer], [ new JsonEncode() ]);

        return $serializer->serialize($this, 'json');
    }


    public function asArray()
    {
        $normalizer = new GetSetMethodNormalizer();
        $normalizer->setIgnoredAttributes(['emails']);

        $serializer = new Serializer([$normalizer], [ new JsonEncode() ]);

        return json_decode($serializer->serialize($this, 'json'), TRUE);
    }
}
