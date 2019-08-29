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
        $objectManager->persist($user);
        $this->addReference('user_admin', $user);

        $user = new User();
        $user->setUsername('zuck');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'sukasepjay'));
        $user->setName('Zuckachev');
        $user->setEmail('zuckachev@gmail.com');
        $objectManager->persist($user);
        $this->addReference('user_zuck', $user);        
        
        $objectManager->flush();
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
                $comment->setAuthor($this->getReference('user_zuck'));
                $comment->setBlogPost($this->getReference('blog_post_'.$i));
                $objectManager->persist($comment);
            }
        }

        $objectManager->flush();
    }
}