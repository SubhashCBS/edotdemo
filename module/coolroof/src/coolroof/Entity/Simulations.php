<?php
/**
 * 
*/

namespace Coolroof\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Projects
 *
 * @ORM\Table(name="projects")
 * @ORM\Entity
 */
class Simulations
{

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
     * @var integer
     *
     * @ORM\Column(azimuth="azimuth", type="integer", length=100, nullable=false)
     */
    protected $azimuth;
	
    /**
     * @var integer
     *
     * @ORM\Column(aspect_ratio="aspect_ratio", type="integer", length=100, nullable=false)
     */
    protected $aspect_ratio;

    /**
     * @var integer
     *
     * @ORM\Column(over_hang="over_hang", type="integer", length=100, nullable=false)
     */
    protected $over_hang;

    /**
     * @var integer
     *
     * @ORM\Column(roof_type="roof_type", type="integer", length=100, nullable=false)
     */
    protected $roof_type;
	
    /**
     * @var integer
     *
     * @ORM\Column(wall_type="wall_type", type="integer", length=100, nullable=false)
     */
    protected $wall_type;

    /**
     * @var integer
     *
     * @ORM\Column(glass_types="glass_types", type="integer", length=100, nullable=false)
     */
    protected $glass_types;

    /**
     * @var integer
     *
     * @ORM\Column(wwr="wwr", type="integer", length=100, nullable=false)
     */
    protected $wwr;

    /**
     * @var integer
     *
     * @ORM\Column(file_name="file_name", type="integer", length=100, nullable=false)
     */
    protected $file_name;
	
	
    /**
     * Set Data
     *
     * @param  object   $name
     * @return Simulations
     */
    public function setData($data)
    {
        $this->azimuth = $data->azimuth;
		$this->aspect_ratio = $data->aspect_ratio;
		$this->over_hang = $data->over_hang;
		$this->roof_type = $data->roof_type;
		$this->wall_type = $data->wall_type;
		$this->glass_types = $data->glass_types;
		$this->wwr = $data->wwr;
		$this->file_name = $data->file_name;
		
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
