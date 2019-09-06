<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use DOctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var Factory
     */
    private $faker;

    private const USERS = [
        [
            'username' => 'admin',
            'password' => 'Rahasiy4',
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'roles' => [User::ROLE_SUPERADMIN]
        ],
        [
            'username' => 'abah',
            'password' => 'Rahasiy4',
            'name' => 'Abah',
            'email' => 'abah@gmail.com',
            'roles' => [User::ROLE_ADMIN]
        ],
        [
            'username' => 'zuckachev',
            'password' => 'Rahasiy4',
            'name' => 'Zuckachev',
            'email' => 'zuckachev@gmail.com',
            'roles' => [User::ROLE_WRITER]
        ],
        [
            'username' => 'zuck',
            'password' => 'Rahasiy4',
            'name' => 'Zuck',
            'email' => 'zuck@gmail.com',
            'roles' => [User::ROLE_EDITOR]
        ],
        [
            'username' => 'wawansky',
            'password' => 'Rahasiy4',
            'name' => 'Wawan Sky',
            'email' => 'wawansky@gmail.com',
            'roles' => [User::ROLE_COMMENTATOR]
        ],
        [
            'username' => 'surasep',
            'password' => 'Rahasiy4',
            'name' => 'Surasep',
            'email' => 'Surasep@gmail.com',
            'roles' => [User::ROLE_COMMENTATOR]
        ]
    ];


    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }

    /**
     * Load data fixtures with the passed Entity manager
     * @param  ObjectManager $manager
     */
    public function load(ObjectManager $objectManager)
    {
        $this->loadUsers($objectManager);
        $this->loadBlogPosts($objectManager);
        $this->loadComments($objectManager);
    }   
    
    public function loadUsers(ObjectManager $objectManager) 
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userFixture['password']));
            $user->setName($userFixture['name']);
            $user->setEmail($userFixture['email']);
            $user->setRoles($userFixture['roles']);
            $objectManager->persist($user);
            $this->addReference('user_'.$userFixture['username'], $user);
        }

        // $user = new User();
        // $user->setUsername('admin');
        // $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
        // $user->setName('Admin');
        // $user->setEmail('admin@gmail.com');
        // $objectManager->persist($user);
        // $this->addReference('user_admin', $user);

        // $user = new User();
        // $user->setUsername('zuck');
        // $user->setPassword($this->passwordEncoder->encodePassword($user, 'sukasepjay'));
        // $user->setName('Zuckachev');
        // $user->setEmail('zuckachev@gmail.com');
        // $objectManager->persist($user);
        // $this->addReference('user_zuck', $user);        
        
        $objectManager->flush();
    }
    
    public function getRandomUserReference($entity): User
    {
        $randomUser = self::USERS[rand(0,5)];

        if ($entity instanceof BlogPost && !count(
            array_intersect(
                $randomUser['roles'],
                [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER]
            )
        )) {
            return $this->getRandomUserReference($entity);            
        }
        
        if ($entity instanceof Comment && !count(
            array_intersect(
                $randomUser['roles'],
                [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER, User::ROLE_COMMENTATOR]
            )
        )) {
            return $this->getRandomUserReference($entity);
            
        }
        
        return $this->getReference('user_'.$randomUser['username']);
        // return $this->getReference('user_'.self::USERS[rand(0,3)]['username']);
    }

    public function loadBlogPosts(ObjectManager $objectManager)
    {
        // First three manual
        $blogPost = new BlogPost();
        $blogPost->setTitle('Bismillah, This is The first post!');
        $blogPost->setPublished(new \DateTime('2018-08-27 18:00:00'));
        $blogPost->setContent('First fixture post');
        $blogPost->setAuthor($this->getReference('user_admin'));
        $blogPost->setSlug('a-first-post');
        $this->setReference('a-first-post', $blogPost);

        $objectManager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A second post!');
        $blogPost->setPublished(new \DateTime('2018-08-27 19:00:00'));
        $blogPost->setContent('Second fixture post');
        $blogPost->setAuthor($this->getReference('user_abah'));
        $blogPost->setSlug('a-second-post');
        $this->setReference('a-second-post', $blogPost);

        $objectManager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A third post!');
        $blogPost->setPublished(new \DateTime('2018-08-27 19:00:00'));
        $blogPost->setContent('Third fixture post');
        $blogPost->setAuthor($this->getReference('user_zuck'));
        $blogPost->setSlug('a-third-post');
        $this->setReference('a-third-post', $blogPost);

        $objectManager->persist($blogPost);

        $this->faker = Factory::create();

        // The rest by faker

        for($i = 0; $i < 100; $i++) {
            
            $blogPost = new BlogPost();
            
            $title = $this->faker->realText(40);
            
            $slug = str_replace(["'", "\"","-", '!', '?', '(', ')', ':', ';', '.', ','], "", trim($title));
            $slug = str_replace(" ", "-", $slug);
            $slug = str_replace(["--", "---"], "-", $slug);
            $slug = strtolower($slug);
    
            $blogPost->setTitle($title);
            $blogPost->setPublished($this->faker->dateTimeThisYear('now', 'Asia/Jakarta'));
            $blogPost->setContent($this->faker->realText());
            
            // Randomize this
            // $blogPost->setAuthor($this->getReference('user_admin'));
            $blogPost->setAuthor($this->getRandomUserReference($blogPost));
            
            $blogPost->setSlug($slug);
            $objectManager->persist($blogPost);

            // Create reference for the later linking by comments
            $this->setReference("blog_post_".$i, $blogPost);
        }
        
        $objectManager->flush();
    }

    public function loadComments(ObjectManager $objectManager)
    {
        $comment = new Comment();
        $comment->setContent("First comment on the first post!!");
        $comment->setPublished($this->faker->dateTimeThisYear);
        $comment->setAuthor($this->getReference('user_admin'));
        $comment->setBlogPost($this->getReference('a-first-post'));
        $objectManager->persist($comment);
        
        $comment = new Comment();
        $comment->setContent("First comment on the second post!!");
        $comment->setPublished($this->faker->dateTimeThisYear);
        $comment->setAuthor($this->getReference('user_admin'));
        $comment->setBlogPost($this->getReference('a-second-post'));
        $objectManager->persist($comment);
        
        $comment = new Comment();
        $comment->setContent("First comment on the third post!!");
        $comment->setPublished($this->faker->dateTimeThisYear);
        $comment->setAuthor($this->getReference('user_admin'));
        $comment->setBlogPost($this->getReference('a-third-post'));
        $objectManager->persist($comment);

        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < 100; $j++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                
                // Randomize this
                // $comment->setAuthor($this->getReference('user_zuck'));
                $comment->setAuthor($this->getRandomUserReference($comment));

                $comment->setBlogPost($this->getReference('blog_post_'.$i));
                $objectManager->persist($comment);
            }
        }

        $objectManager->flush();
    }
}