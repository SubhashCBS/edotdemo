<?php
/**
 * 
*/

namespace coolroof\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Projects
 *
 * @ORM\Table(name="projects")
 * @ORM\Entity
 */
class Project
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
	
	/**
     * @var CsnUser\Entity\User
     * 
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Set name
     *
     * @param  string   $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
	
	/**
     * Set user
     *
     * @param  User $user
     * @return User
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
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
}
