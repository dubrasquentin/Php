<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    public function __construct()
    {
        parent::__construct();

    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(name="nom", type="string", length=255)
     */
    protected $nom;
    /**
     * @ORM\Column(name="date_naissance", type="date")
     */
    protected $date_naissance;

    /**
     * @ORM\Column(name="boolean", type="date")
     */
    protected $sexe;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Evaluation", mappedBy="user")
     */
    private $evalsU;

}
