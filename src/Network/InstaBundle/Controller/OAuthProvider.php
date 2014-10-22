<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 15.10.2014
 * Time: 21:09
 */

namespace Network\InstaBundle\Controller;


use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Network\StoreBundle\Entity\User;
use OAuthToken;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuthProvider implements UserProviderInterface, OAuthAwareUserProviderInterface, AccountConnectorInterface
{
    private $userModel;
    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username)
    {
        throw new \BadMethodCallException('Method not implemented');
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException();
        }

        return $this->userModel->findById($user->getId());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === $this->_class;
    }

    /**
     * @param string $type
     * @param string $username
     *
     * @return User|null
     */
    public function findByToken($type, $username)
    {
        $qb = $this->userModel->createQueryBuilder('u');
        $qb->join('u.oauthTokens', 't')->addSelect('t');
        $qb->where('t.type = :type AND t.username = :username');
        $qb->setParameters([
                'type'     => $type,
                'username' => $username
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }


    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $type = $response->getResourceOwner()->getName();
        $user = $this->findByToken($type, $response->getUsername());
        if ($user === null) {
            throw new UsernameNotFoundException('User not found');
        }
        $token = $user->getOAuthTokenByType($type);
        $token->setOAuthUserResponse($response);
        $this->userModel->_em->persist($token);
        $this->userModel->_em->flush();

        return $user;
    }

    /**
     * Connects the response the the user object.
     *
     * @param UserInterface $user The user object
     * @param UserResponseInterface $response The oauth response
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $token = new OAuthToken($response);
        $token->setUser($user);
        $this->userModel->_em->persist($token);
        $this->userModel->_em->flush();
    }
}