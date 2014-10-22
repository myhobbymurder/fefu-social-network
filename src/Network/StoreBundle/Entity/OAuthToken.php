<?php

use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_oauth_tokens", indexes={
 *     @ORM\Index(name="token", columns={"type", "username"}),
 *     @ORM\Index(name="email", columns={"email"})
 * })
 */
class OAuthToken
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue()
     */
    private $id;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="oauthTokens")
     * @ORM\JoinColumn(name="user_id", nullable=false, onDelete="CASCADE")
     */
    private $user;
    /**
     * @var string
     * @ORM\Column(type="string", length=15)
     */
    private $type;
    /**
     * @var string|null
     * @ORM\Column(type="text", length=500, nullable=true)
     */
    private $token;
    /**
     * @var \DateTime|null
     * @ORM\Column(name="expired_in", type="datetime", nullable=true)
     */
    private $expiredIn;

    public function __construct(UserResponseInterface $response)
    {
        $this->type = $response->getResourceOwner()->getName();
        $this->setOAuthUserResponse($response);
    }

    public function setOAuthUserResponse(UserResponseInterface $response)
    {
        $this->token = $response->getAccessToken();
        $lifetime = $response->getExpiresIn();
        if ($lifetime !== null) {
            $this->expiredIn = new \DateTime();
            $this->expiredIn->setTimestamp(time() + $lifetime);
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param null|string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }



}