<?php

namespace GitHub\Tests\API\User;

use GitHub\API\Api;
use GitHub\API\User\User;
use GitHub\Tests\API\ApiTest;

class UserTest extends ApiTest
{
    public function testGetWithUsername()
    {
        $transportMock = $this->getTransportMock();

        $expectedResults = $this->getResultUser();
        $expectedResults['status'] = Api::HTTP_STATUS_OK;

        $transportMock->expects($this->once())
             ->method('get')
             ->will($this->returnValue($expectedResults));

        $user = new User($transportMock);

        // No authentication required
        $result = $user->get('octocat');
        $this->assertEquals('octocat', $result['login']);
    }

    public function testGetAuthenticated()
    {
        $transportMock = $this->getTransportMock();

        $expectedResults = $this->getResultUser();
        $expectedResults['status'] = Api::HTTP_STATUS_OK;

        $transportMock->expects($this->once())
             ->method('get')
             ->will($this->returnValue($expectedResults));

        $user = new User($transportMock);
        // Get authenticated
        $user->setCredentials('username', 'password');
        $user->login();
        $result = $user->get();
        $this->assertEquals('octocat', $result['login']);
    }

    public function testGetUnauthenticated()
    {
        $transportMock = $this->getTransportMock();

        // Should never try to access the API - expecting Exception
        $transportMock->expects($this->never())
             ->method('get')
             ->will($this->returnValue($this->getResultUser()));

        $user = new User($transportMock);
        $this->setExpectedException('GitHub\API\AuthenticationException');
        // Try without authentication
        $result = $user->get();
    }

    public function testUpdateAuthenticated()
    {
        $transportMock = $this->getTransportMock();

        // Changes for the user
        $changes = array('name' => 'dsyph3r');

        // Update the return value for name
        $transportMock->expects($this->once())
             ->method('patch')
             ->will($this->returnValue($this->getResultUser($changes)));

        $user = new User($transportMock);
        // Get authenticated
        $user->setCredentials('username', 'password');
        $user->login();
        $result = $user->update($changes);
        $this->assertEquals('dsyph3r', $result['name']);
    }

    public function testUpdateUnauthenticated()
    {
        $transportMock = $this->getTransportMock();

        // Changes for the user
        $changes = array('name' => 'dsyph3r');

        // Should never try to access the API - expecting Exception
        $transportMock->expects($this->never())
             ->method('patch')
             ->will($this->returnValue($this->getResultUser($changes)));

        $user = new User($transportMock);
        $this->setExpectedException('GitHub\API\AuthenticationException');
        // Try without authentication
        $result = $user->update($changes);
    }

    public function testFollowersWithUsername()
    {
        $transportMock = $this->getTransportMock();

        $expectedResults = $this->getResultFollowers();
        $expectedResults['status'] = Api::HTTP_STATUS_OK;

        $transportMock->expects($this->once())
             ->method('get')
             ->will($this->returnValue($expectedResults));

        $user = new User($transportMock);

        // No authentication required
        $result = $user->followers('octocat');
        $this->assertEquals('octocat', $result[0]['login']);
    }

    public function testFollowersAuthenticated()
    {
        $transportMock = $this->getTransportMock();

        $expectedResults = $this->getResultFollowers();
        $expectedResults['status'] = Api::HTTP_STATUS_OK;

        $transportMock->expects($this->once())
             ->method('get')
             ->will($this->returnValue($expectedResults));

        $user = new User($transportMock);
        // Get authenticated
        $user->setCredentials('username', 'password');
        $user->login();
        $result = $user->followers();
        $this->assertEquals('octocat', $result[0]['login']);
    }

    public function testFollowersUnauthenticated()
    {
        $transportMock = $this->getTransportMock();

        // Should never try to access the API - expecting Exception
        $transportMock->expects($this->never())
             ->method('get')
             ->will($this->returnValue($this->getResultFollowers()));

        $user = new User($transportMock);
        $user->setCredentials('username', 'password');
        // Ensure login/logout process also works
        $user->login();
        $user->logout();
        $user->clearCredentials();
        $this->setExpectedException('GitHub\API\AuthenticationException');
        // Try without authentication
        $result = $user->followers();
    }

    public function testFollowingWithUsername()
    {
        $transportMock = $this->getTransportMock();

        $expectedResults = $this->getResultFollowing();
        $expectedResults['status'] = Api::HTTP_STATUS_OK;

        $transportMock->expects($this->once())
             ->method('get')
             ->will($this->returnValue($expectedResults));

        $user = new User($transportMock);

        // No authentication required
        $result = $user->following('octocat');
        $this->assertEquals('octocat', $result[0]['login']);
    }

    public function testFollowingAuthenticated()
    {
        $transportMock = $this->getTransportMock();

        $expectedResults = $this->getResultFollowing();
        $expectedResults['status'] = Api::HTTP_STATUS_OK;

        $transportMock->expects($this->once())
             ->method('get')
             ->will($this->returnValue($expectedResults));

        $user = new User($transportMock);
        // Get authenticated
        $user->setCredentials('username', 'password');
        $user->login();
        $result = $user->following();
        $this->assertEquals('octocat', $result[0]['login']);
    }

    public function testFollowingUnauthenticated()
    {
        $transportMock = $this->getTransportMock();

        // Should never try to access the API - expecting Exception
        $transportMock->expects($this->never())
             ->method('get')
             ->will($this->returnValue($this->getResultFollowing()));

        $user = new User($transportMock);
        $this->setExpectedException('GitHub\API\AuthenticationException');
        // Try without authentication
        $result = $user->following();
    }

    public function testFollowAuthenticated()
    {
        $transportMock = $this->getTransportMock();

        $transportMock->expects($this->once())
             ->method('put')
             ->will($this->returnValue(array('status' => Api::HTTP_STATUS_NO_CONTENT)));

        $user = new User($transportMock);
        // Get authenticated
        $user->setCredentials('username', 'password');
        $user->login();
        $this->assertTrue($user->follow('dsyph3r'));
    }

    public function testFollowUnauthenticated()
    {
        $transportMock = $this->getTransportMock();

        // Should never try to access the API - expecting Exception
        $transportMock->expects($this->never())
             ->method('put')
             ->will($this->returnValue(array('status' => Api::HTTP_STATUS_NO_CONTENT)));

        $user = new User($transportMock);
        $this->setExpectedException('GitHub\API\AuthenticationException');
        // Try without authentication
        $result = $user->follow('dsyph3r');
    }

    public function testUnfollowAuthenticated()
    {
        $transportMock = $this->getTransportMock();

        $transportMock->expects($this->once())
             ->method('delete')
             ->will($this->returnValue(array('status' => Api::HTTP_STATUS_NO_CONTENT)));

        $user = new User($transportMock);
        // Get authenticated
        $user->setCredentials('username', 'password');
        $user->login();
        $this->assertTrue($user->unfollow('dsyph3r'));
    }

    public function testUnfollowUnauthenticated()
    {
        $transportMock = $this->getTransportMock();

        // Should never try to access the API - expecting Exception
        $transportMock->expects($this->never())
             ->method('delete')
             ->will($this->returnValue(array('status' => Api::HTTP_STATUS_NO_CONTENT)));

        $user = new User($transportMock);
        $this->setExpectedException('GitHub\API\AuthenticationException');
        // Try without authentication
        $result = $user->unfollow('dsyph3r');
    }

    public function testIsFollowingAuthenticated()
    {
        $transportMock = $this->getTransportMock();

        $transportMock->expects($this->once())
             ->method('get')
             ->will($this->returnValue(array('status' => Api::HTTP_STATUS_NO_CONTENT)));

        $user = new User($transportMock);
        // Get authenticated
        $user->setCredentials('username', 'password');
        $user->login();
        $this->assertTrue($user->isFollowing('dsyph3r'));
    }

    public function testIsFollowingUnauthenticated()
    {
        $transportMock = $this->getTransportMock();

        // Should never try to access the API - expecting Exception
        $transportMock->expects($this->never())
             ->method('get')
             ->will($this->returnValue(array('status' => Api::HTTP_STATUS_NO_CONTENT)));

        $user = new User($transportMock);
        $this->setExpectedException('GitHub\API\AuthenticationException');
        // Try without authentication
        $result = $user->isFollowing('dsyph3r');
    }

    public function testEmails()
    {
        $user = new User();
        $this->assertInstanceOf('GitHub\API\User\Email', $user->emails());
    }
    
    public function testKeys()
    {
        $user = new User();
        $this->assertInstanceOf('GitHub\API\User\Key', $user->keys());
    }
    
    public function testRepos()
    {
        $user = new User();
        $this->assertInstanceOf('GitHub\API\User\Repo', $user->repos());
    }
    
    public function testGists()
    {
        $user = new User();
        $this->assertInstanceOf('GitHub\API\Gist\Gist', $user->gists());
    }

    private function getResultUser($details = array())
    {
        $user = array(
            'login'         => 'octocat',
            'id'            => 1,
            'gravatar_url'  => 'https://github.com/images/error/octocat_happy.gif',
            'url'           => 'https://api.github.com/users/octocat',
            'name'          => 'monalisa octocat',
            'company'       => 'GitHub',
            'blog'          => 'https://github.com/blog',
            'location'      => 'San Francisco',
            'email'         => 'octocat@github.com',
            'hireable'      => false,
            'bio'           => 'There once was...',
            'public_repos'  => 2,
            'public_gists'  => 1,
            'followers'     => 20,
            'following'     => 0,
            'html_url'      => 'https://github.com/octocat',
            'created_at'    => '2008-01-14T04:33:35Z',
            'type'          => 'User'
        );

        return array('data' => array_merge($user, $details));
    }

    private function getResultFollowers()
    {
        return array(
            'data' => array(
                array(
                    'login'         => 'octocat',
                    'id'            => 1,
                    'gravatar_url'  => 'https://github.com/images/error/octocat_happy.gif',
                    'url'           => 'https://api.github.com/users/octocat'
                )
            )
        );
    }

    private function getResultFollowing()
    {
        return array(
            'data' => array(
                array(
                    'login'         => 'octocat',
                    'id'            => 1,
                    'gravatar_url'  => 'https://github.com/images/error/octocat_happy.gif',
                    'url'           => 'https://api.github.com/users/octocat'
                )
            )
        );
    }
}