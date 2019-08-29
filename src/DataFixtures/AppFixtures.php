<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use DOctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
        $user->setName('Admin');
        $user->setEmail('admin@gmail.com');
        $this->addReference('user_admin', $user);

        $objectManager->persist($user);

        $user = new User();
        $user->setUsername('zuck');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'sukasepjay'));
        $user->setName('Zuckachev');
        $user->setEmail('zuckachev@gmail.com');
        $this->addReference('user_zuck', $user);
        
        $objectManager->persist($user);
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
        $blogPost->setAuthor($this->getReference('user_zuck'));
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
            $slug = strtolower(str_replace(" ", "", $title));
    
            $blogPost->setTitle($title);
            $blogPost->setPublished($this->faker->dateTimeThisYear('now', 'Asia/Jakarta'));
            $blogPost->setContent($this->faker->realText());
            $blogPost->setAuthor($this->getReference('user_admin'));
            $blogPost->setSlug($slug);

            $objectManager->persist($blogPost);    
        }
        // $this->setReference($slug, $blogPost);

        $objectManager->flush();
    }

    public function loadComments(ObjectManager $objectManager)
    {
        
    }
}